<?php

namespace App\Services;

use App\Models\ImportLog;
use App\Models\User;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelImportService
{
    /**
     * Required columns for each type.
     */
    private static array $requiredColumns = [
        'mahasiswa' => ['nama', 'nim', 'email'],
        'dosen' => ['nama', 'nip', 'email'],
    ];

    /**
     * Optional columns for each type.
     */
    private static array $optionalColumns = [
        'mahasiswa' => ['jurusan', 'no_hp', 'status'],
        'dosen' => ['jurusan', 'no_hp', 'status'],
    ];

    /**
     * Parse an uploaded Excel/CSV file and return preview data.
     */
    public static function preview(string $filePath, string $type): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            throw new \RuntimeException('File harus memiliki minimal 1 baris header dan 1 baris data.');
        }

        // Parse header (first row)
        $headerRow = array_shift($rows);
        $header = array_map(fn($h) => strtolower(trim($h ?? '')), $headerRow);
        $header = array_values(array_filter($header, fn($h) => !empty($h)));

        // Validate required columns
        $required = self::$requiredColumns[$type] ?? [];
        $missing = array_diff($required, $header);
        if (!empty($missing)) {
            throw new \RuntimeException('Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '. Kolom yang ada: ' . implode(', ', $header));
        }

        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $rowIndex => $row) {
            $rowData = [];
            foreach ($header as $colIdx => $colName) {
                $cellKey = array_keys($headerRow)[$colIdx] ?? null;
                $rowData[$colName] = trim($row[$cellKey] ?? '');
            }

            // Skip completely empty rows
            if (empty(array_filter($rowData))) {
                continue;
            }

            $errors = self::validateRow($rowData, $type, $rowIndex + 2);
            if (!empty($errors)) {
                $rowData['_errors'] = $errors;
                $rowData['_row'] = $rowIndex + 2;
                $invalidRows[] = $rowData;
            } else {
                $rowData['_row'] = $rowIndex + 2;
                $validRows[] = $rowData;
            }
        }

        return [
            'header' => $header,
            'valid_rows' => $validRows,
            'invalid_rows' => $invalidRows,
            'total' => count($validRows) + count($invalidRows),
            'valid_count' => count($validRows),
            'invalid_count' => count($invalidRows),
        ];
    }

    /**
     * Validate a single row.
     */
    private static function validateRow(array $row, string $type, int $rowNumber): array
    {
        $errors = [];

        $nama = $row['nama'] ?? '';
        $email = $row['email'] ?? '';
        $identifier = $type === 'mahasiswa' ? ($row['nim'] ?? '') : ($row['nip'] ?? '');
        $identifierLabel = $type === 'mahasiswa' ? 'NIM' : 'NIP';

        if (empty($nama)) {
            $errors[] = "Baris {$rowNumber}: Nama wajib diisi.";
        } elseif (strlen($nama) > 255) {
            $errors[] = "Baris {$rowNumber}: Nama terlalu panjang (maks 255).";
        }

        if (empty($email)) {
            $errors[] = "Baris {$rowNumber}: Email wajib diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Baris {$rowNumber}: Format email tidak valid.";
        }

        if (empty($identifier)) {
            $errors[] = "Baris {$rowNumber}: {$identifierLabel} wajib diisi.";
        } elseif (strlen($identifier) > 50) {
            $errors[] = "Baris {$rowNumber}: {$identifierLabel} terlalu panjang (maks 50).";
        }

        $noHp = $row['no_hp'] ?? '';
        if (!empty($noHp) && !preg_match('/^[0-9]{10,15}$/', $noHp)) {
            $errors[] = "Baris {$rowNumber}: Format No HP tidak valid (harus 10-15 digit).";
        }

        return $errors;
    }

    /**
     * Execute the import with the given strategy.
     *
     * @param array $validRows Pre-validated rows
     * @param string $type 'mahasiswa' or 'dosen'
     * @param string $strategy 'skip' | 'update' | 'stop'
     * @param int $adminId ID of the admin performing the import
     * @return array Summary
     */
    public static function executeImport(array $validRows, string $type, string $strategy, int $adminId): array
    {
        $jurusanMap = \App\Models\Jurusan::pluck('id_jurusan', 'nama_jurusan')->toArray();

        $imported = 0;
        $skipped = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($validRows as $row) {
                $email = strtolower(trim($row['email']));
                $identifier = $type === 'mahasiswa' ? ($row['nim'] ?? '') : ($row['nip'] ?? '');
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    if ($strategy === 'stop') {
                        DB::rollBack();
                        return [
                            'imported' => 0,
                            'skipped' => 0,
                            'updated' => 0,
                            'errors' => ["Import dihentikan: Email {$email} sudah terdaftar (baris {$row['_row']})."],
                            'status' => 'stopped',
                        ];
                    }

                    if ($strategy === 'skip') {
                        $skipped++;
                        continue;
                    }

                    if ($strategy === 'update') {
                        $existingUser->update(['name' => $row['nama']]);

                        $jurusanId = self::matchJurusan($row['jurusan'] ?? '', $jurusanMap);
                        $existingUser->profile()->updateOrCreate(
                            ['user_id' => $existingUser->id],
                            array_filter([
                                'nim' => $identifier,
                                'id_jurusan' => $jurusanId,
                                'no_hp' => $row['no_hp'] ?? null,
                            ])
                        );
                        $updated++;
                        continue;
                    }
                }

                // Also check NIM/NIP uniqueness
                $existingProfile = \App\Models\Profile::where('nim', $identifier)->first();
                if ($existingProfile) {
                    if ($strategy === 'stop') {
                        DB::rollBack();
                        $label = $type === 'mahasiswa' ? 'NIM' : 'NIP';
                        return [
                            'imported' => 0,
                            'skipped' => 0,
                            'updated' => 0,
                            'errors' => ["Import dihentikan: {$label} {$identifier} sudah terdaftar (baris {$row['_row']})."],
                            'status' => 'stopped',
                        ];
                    }
                    $skipped++;
                    continue;
                }

                // Create new user
                $password = Str::random(12);
                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $type,
                    'status' => strtolower($row['status'] ?? '') === 'nonaktif' ? 'nonaktif' : 'aktif',
                ]);

                $jurusanId = self::matchJurusan($row['jurusan'] ?? '', $jurusanMap);
                $user->profile()->create([
                    'nim' => $identifier,
                    'id_jurusan' => $jurusanId,
                    'no_hp' => !empty($row['no_hp']) ? $row['no_hp'] : null,
                ]);

                $imported++;
            }

            DB::commit();

            // Log the import
            ImportLog::create([
                'admin_id' => $adminId,
                'type' => $type,
                'filename' => 'upload',
                'total_rows' => count($validRows),
                'imported' => $imported,
                'skipped' => $skipped,
                'updated' => $updated,
                'duplicate_strategy' => $strategy,
                'status' => 'completed',
                'errors' => !empty($errors) ? $errors : null,
            ]);

            // Notify admins
            $label = $type === 'mahasiswa' ? 'Mahasiswa' : 'Dosen';
            AdminNotification::notifyAllAdmins(
                "Import {$label} selesai",
                "{$imported} ditambahkan, {$updated} diperbarui, {$skipped} dilewati dari total " . count($validRows) . " data.",
                'success',
                'import',
                route('admin.' . ($type === 'mahasiswa' ? 'mahasiswa' : 'dosen'))
            );

            return [
                'imported' => $imported,
                'skipped' => $skipped,
                'updated' => $updated,
                'errors' => $errors,
                'status' => 'completed',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            ImportLog::create([
                'admin_id' => $adminId,
                'type' => $type,
                'filename' => 'upload',
                'total_rows' => count($validRows),
                'imported' => 0,
                'skipped' => 0,
                'updated' => 0,
                'duplicate_strategy' => $strategy,
                'status' => 'failed',
                'errors' => [$e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Match jurusan name to ID (fuzzy).
     */
    private static function matchJurusan(string $name, array $map): ?int
    {
        if (empty($name)) return null;

        foreach ($map as $jurusanName => $id) {
            if (stripos($jurusanName, $name) !== false || stripos($name, $jurusanName) !== false) {
                return $id;
            }
        }
        return null;
    }

    /**
     * Generate a template Excel file.
     *
     * @return string Path to the generated file
     */
    public static function generateTemplate(string $type): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $isMahasiswa = ($type === 'mahasiswa');
        $sheet->setTitle($isMahasiswa ? 'Template Mahasiswa' : 'Template Dosen');

        // Headers
        $headers = $isMahasiswa
            ? ['Nama', 'NIM', 'Email', 'Jurusan', 'No HP', 'Status']
            : ['Nama', 'NIP', 'Email', 'Jurusan', 'No HP', 'Status'];

        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header row
        $lastCol = chr(65 + count($headers) - 1);
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF3B82F6'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Sample rows
        $sampleData = $isMahasiswa
            ? [
                ['Budi Santoso', '2024001001', 'budi.santoso@example.com', 'Teknik Informatika', '081234567890', 'aktif'],
                ['Siti Rahayu', '2024001002', 'siti.rahayu@example.com', 'Sistem Informasi', '081234567891', 'aktif'],
                ['Ahmad Fadli', '2024001003', 'ahmad.fadli@example.com', 'Teknik Informatika', '081234567892', 'aktif'],
            ]
            : [
                ['Dr. Ahmad Susanto', '198501012010011001', 'ahmad.susanto@example.com', 'Teknik Informatika', '081234567890', 'aktif'],
                ['Prof. Siti Aminah', '197803152005012002', 'siti.aminah@example.com', 'Sistem Informasi', '081234567891', 'aktif'],
                ['Dr. Budi Prakoso', '199002202015011003', 'budi.prakoso@example.com', 'Teknik Informatika', '081234567892', 'aktif'],
            ];

        $row = 2;
        foreach ($sampleData as $data) {
            foreach ($data as $col => $value) {
                $sheet->setCellValue(chr(65 + $col) . $row, $value);
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Notes row
        $notesRow = $row + 1;
        $sheet->setCellValue("A{$notesRow}", 'Catatan:');
        $sheet->getStyle("A{$notesRow}")->getFont()->setBold(true);
        $sheet->setCellValue("A" . ($notesRow + 1), '- Kolom Nama, ' . ($isMahasiswa ? 'NIM' : 'NIP') . ', Email wajib diisi');
        $sheet->setCellValue("A" . ($notesRow + 2), '- Kolom Jurusan, No HP, Status opsional');
        $sheet->setCellValue("A" . ($notesRow + 3), '- Status: aktif atau nonaktif (default: aktif)');
        $sheet->setCellValue("A" . ($notesRow + 4), '- Email harus unik (belum terdaftar di sistem)');
        $sheet->setCellValue("A" . ($notesRow + 5), '- Format No HP: 10-15 digit angka');

        $writer = new Xlsx($spreadsheet);
        $filename = "template_import_{$type}.xlsx";
        $path = storage_path("app/public/{$filename}");
        $writer->save($path);

        return $path;
    }
}
