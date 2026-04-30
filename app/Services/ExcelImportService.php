<?php

namespace App\Services;

use App\Models\ImportLog;
use App\Models\User;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelImportService
{
    private static ?array $jurusanLookups = null;

    /**
     * Required columns for each type.
     */
    private static array $requiredColumns = [
        'mahasiswa' => ['nama', 'nomor_induk', 'email'],
        'dosen' => ['nama', 'nomor_induk', 'email'],
    ];

    /**
     * Optional columns for each type.
     */
    private static array $optionalColumns = [
        'mahasiswa' => ['jurusan', 'id_jurusan', 'kode_jurusan', 'no_hp', 'status'],
        'dosen' => ['kode_jurusan', 'jurusan', 'id_jurusan', 'no_hp', 'status'],
    ];

    /**
     * Parse an uploaded Excel/CSV file and return preview data.
     */
    public static function preview(string $filePath, string $type): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, false, false, true);

        if (count($rows) < 2) {
            throw new \RuntimeException('File harus memiliki minimal 1 baris header dan 1 baris data.');
        }

        // Parse header (first row)
        $headerRow = array_shift($rows);
        $headerKeys = array_keys($headerRow);
        $header = array_map(fn($h) => strtolower(trim((string) ($h ?? ''))), $headerRow);
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
                $columnLetter = $headerKeys[$colIdx] ?? null;
                if ($columnLetter === null) {
                    continue;
                }

                $cellCoordinate = $columnLetter . ($rowIndex + 2);
                $rowData[$colName] = self::readCellAsString($sheet->getCell($cellCoordinate), $colName);
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
        $identifier = $row['nomor_induk'] ?? '';
        $identifierLabel = 'Nomor Induk';

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

        $status = self::normalizeImportedStatus($row['status'] ?? null);
        if ($status === null && filled($row['status'] ?? null)) {
            $errors[] = "Baris {$rowNumber}: Status harus aktif atau nonaktif.";
        }

        if ($type === 'dosen') {
            $kodeJurusan = trim((string) ($row['kode_jurusan'] ?? ''));
            if ($kodeJurusan !== '') {
                $lookups = self::getJurusanLookups();
                $invalidCodes = collect(self::parseDelimitedValues($kodeJurusan))
                    ->reject(fn (string $code) => isset($lookups['byCode'][strtoupper($code)]))
                    ->values()
                    ->all();

                if (!empty($invalidCodes)) {
                    $errors[] = "Baris {$rowNumber}: Kode jurusan tidak ditemukan: " . implode(', ', $invalidCodes) . '.';
                }
            }
        } else {
            $idJurusan = trim((string) ($row['id_jurusan'] ?? ''));
            if ($idJurusan !== '' && !ctype_digit($idJurusan)) {
                $errors[] = "Baris {$rowNumber}: ID Program Studi harus berupa angka.";
            }
        }

        return $errors;
    }

    private static function readCellAsString($cell, string $columnName): string
    {
        $value = $cell->getFormattedValue();

        if ($value === null || $value === '') {
            $value = $cell->getValue();
        }

        $value = trim((string) ($value ?? ''));

        // Keep identifiers exactly as displayed in Excel when the user formats cells as text.
        if (in_array($columnName, ['nomor_induk', 'no_hp'], true)) {
            return preg_replace("/^[\\'=]+/", '', $value) ?? '';
        }

        return $value;
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
        $jurusanLookups = self::getJurusanLookups();

        $imported = 0;
        $skipped = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($validRows as $row) {
                $email = strtolower(trim($row['email']));
                $identifier = $row['nomor_induk'] ?? '';
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
                        $status = self::normalizeImportedStatus($row['status'] ?? null) ?? 'aktif';
                        $existingUser->update([
                            'name' => $row['nama'],
                            'status' => $status,
                        ]);

                        $jurusanIds = $type === 'dosen'
                            ? self::resolveDosenJurusanIds($row, $jurusanLookups)
                            : [];
                        $jurusanId = $type === 'dosen'
                            ? ($jurusanIds[0] ?? null)
                            : self::resolveJurusanId($row, $jurusanLookups);

                        $profile = $existingUser->profile()->updateOrCreate(
                            ['user_id' => $existingUser->id],
                            array_filter([
                                'nomor_induk' => $identifier,
                                'id_jurusan' => $jurusanId,
                                'no_hp' => $row['no_hp'] ?? null,
                            ])
                        );

                        if ($type === 'dosen') {
                            self::syncDosenJurusans($profile, $jurusanIds);
                        }

                        $updated++;
                        continue;
                    }
                }

                // Also check Nomor Induk uniqueness
                $existingProfile = \App\Models\Profile::where('nomor_induk', $identifier)->first();
                if ($existingProfile) {
                    if ($strategy === 'stop') {
                        DB::rollBack();
                        return [
                            'imported' => 0,
                            'skipped' => 0,
                            'updated' => 0,
                            'errors' => ["Import dihentikan: Nomor Induk {$identifier} sudah terdaftar (baris {$row['_row']})."],
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
                    'status' => self::normalizeImportedStatus($row['status'] ?? null) ?? 'aktif',
                ]);

                $jurusanIds = $type === 'dosen'
                    ? self::resolveDosenJurusanIds($row, $jurusanLookups)
                    : [];
                $jurusanId = $type === 'dosen'
                    ? ($jurusanIds[0] ?? null)
                    : self::resolveJurusanId($row, $jurusanLookups);

                $profile = $user->profile()->create([
                    'nomor_induk' => $identifier,
                    'id_jurusan' => $jurusanId,
                    'no_hp' => !empty($row['no_hp']) ? $row['no_hp'] : null,
                ]);

                if ($type === 'dosen') {
                    self::syncDosenJurusans($profile, $jurusanIds);
                }

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
    private static function resolveJurusanId(array $row, array $lookups): ?int
    {
        $idJurusan = trim((string) ($row['id_jurusan'] ?? ''));
        if ($idJurusan !== '' && isset($lookups['byId'][(int) $idJurusan])) {
            return (int) $idJurusan;
        }

        $kodeJurusan = trim((string) ($row['kode_jurusan'] ?? ''));
        if ($kodeJurusan !== '') {
            $firstCode = self::parseDelimitedValues($kodeJurusan)[0] ?? null;
            if ($firstCode !== null && isset($lookups['byCode'][strtoupper($firstCode)])) {
                return $lookups['byCode'][strtoupper($firstCode)];
            }
        }

        $name = trim((string) ($row['jurusan'] ?? ''));
        if ($name === '') {
            return null;
        }

        if (ctype_digit($name) && isset($lookups['byId'][(int) $name])) {
            return (int) $name;
        }

        foreach ($lookups['byName'] as $jurusanName => $id) {
            if (stripos($jurusanName, $name) !== false || stripos($name, $jurusanName) !== false) {
                return $id;
            }
        }

        return null;
    }

    private static function resolveDosenJurusanIds(array $row, array $lookups): array
    {
        $kodeJurusan = trim((string) ($row['kode_jurusan'] ?? ''));
        if ($kodeJurusan !== '') {
            return collect(self::parseDelimitedValues($kodeJurusan))
                ->map(fn (string $code) => $lookups['byCode'][strtoupper($code)] ?? null)
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $idJurusan = trim((string) ($row['id_jurusan'] ?? ''));
        if ($idJurusan !== '') {
            return collect(self::parseDelimitedValues($idJurusan))
                ->filter(fn (string $id) => ctype_digit($id) && isset($lookups['byId'][(int) $id]))
                ->map(fn (string $id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $jurusan = trim((string) ($row['jurusan'] ?? ''));
        if ($jurusan === '') {
            return [];
        }

        return collect(self::parseDelimitedValues($jurusan))
            ->map(function (string $name) use ($lookups) {
                foreach ($lookups['byName'] as $jurusanName => $id) {
                    if (stripos($jurusanName, $name) !== false || stripos($name, $jurusanName) !== false) {
                        return (int) $id;
                    }
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private static function syncDosenJurusans(\App\Models\Profile $profile, array $jurusanIds): void
    {
        $profile->jurusans()->sync($jurusanIds);

        $primaryJurusanId = $jurusanIds[0] ?? null;
        if ((string) $profile->id_jurusan !== (string) $primaryJurusanId) {
            $profile->forceFill(['id_jurusan' => $primaryJurusanId])->save();
        }
    }

    private static function getJurusanLookups(): array
    {
        if (self::$jurusanLookups !== null) {
            return self::$jurusanLookups;
        }

        $jurusans = \App\Models\Jurusan::query()
            ->select(['id_jurusan', 'kode_jurusan', 'nama_jurusan'])
            ->get();

        return self::$jurusanLookups = [
            'byId' => $jurusans->pluck('id_jurusan', 'id_jurusan')->toArray(),
            'byCode' => $jurusans
                ->filter(fn ($jurusan) => filled($jurusan->kode_jurusan))
                ->mapWithKeys(fn ($jurusan) => [strtoupper((string) $jurusan->kode_jurusan) => (int) $jurusan->id_jurusan])
                ->toArray(),
            'byName' => $jurusans
                ->filter(fn ($jurusan) => filled($jurusan->nama_jurusan))
                ->mapWithKeys(fn ($jurusan) => [mb_strtolower((string) $jurusan->nama_jurusan) => (int) $jurusan->id_jurusan])
                ->toArray(),
        ];
    }

    private static function parseDelimitedValues(?string $value): array
    {
        return collect(preg_split('/[\s]*[,;|]+[\s]*/', (string) $value) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private static function normalizeImportedStatus(?string $status): ?string
    {
        $normalized = strtolower(trim((string) $status));

        if ($normalized === '') {
            return 'aktif';
        }

        return match ($normalized) {
            'aktif', 'active', '1' => 'aktif',
            'nonaktif', 'non-aktif', 'inactive', '0' => 'nonaktif',
            default => null,
        };
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
            ? ['Nama', 'Nomor Induk', 'Email', 'ID Jurusan', 'Jurusan', 'No HP', 'Status']
            : ['Nama', 'Nomor Induk', 'Email', 'Kode Jurusan', 'No HP', 'Status'];

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
        $jurusanLookups = self::getJurusanLookups();
        $availableCodes = array_keys($jurusanLookups['byCode']);
        $firstCode = $availableCodes[0] ?? 'TI';
        $secondCode = $availableCodes[1] ?? $firstCode;
        $thirdCode = $availableCodes[2] ?? $firstCode;

        $sampleData = $isMahasiswa
            ? [
                ['Budi Santoso', '2024001001', 'budi.santoso@example.com', '1', 'Teknik Informatika', '081234567890', 'aktif'],
                ['Siti Rahayu', '2024001002', 'siti.rahayu@example.com', '2', 'Sistem Informasi', '081234567891', ''],
                ['Ahmad Fadli', '2024001003', 'ahmad.fadli@example.com', '', 'Teknik Informatika', '081234567892', 'aktif'],
            ]
            : [
                ['Dr. Ahmad Susanto', '198501012010011001', 'ahmad.susanto@example.com', $firstCode, '081234567890', 'aktif'],
                ['Prof. Siti Aminah', '197803152005012002', 'siti.aminah@example.com', $secondCode, '081234567891', 'aktif'],
                ['Dr. Budi Prakoso', '199002202015011003', 'budi.prakoso@example.com', implode(', ', array_unique([$firstCode, $thirdCode])), '081234567892', 'aktif'],
            ];

        $row = 2;
        foreach ($sampleData as $data) {
            foreach ($data as $col => $value) {
                $cell = chr(65 + $col) . $row;
                if ($isMahasiswa && in_array($col, [1, 3, 5], true)) {
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
                    continue;
                }

                if (!$isMahasiswa && in_array($col, [1, 3, 4], true)) {
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
                    continue;
                }

                $sheet->setCellValue($cell, $value);
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
        $sheet->setCellValue("A" . ($notesRow + 1), '- Kolom Nama, Nomor Induk, Email wajib diisi');
        $sheet->setCellValue(
            "A" . ($notesRow + 2),
            $isMahasiswa
                ? '- Kolom ID Jurusan atau Jurusan bisa dipakai. Prioritas baca dari ID Jurusan jika diisi'
                : '- Gunakan kolom Kode Jurusan. Bisa isi lebih dari satu kode jurusan, pisahkan dengan koma'
        );
        $sheet->setCellValue("A" . ($notesRow + 3), '- Status: aktif atau nonaktif. Jika kosong akan otomatis menjadi aktif');
        $sheet->setCellValue("A" . ($notesRow + 4), '- Duplikat dicek berdasarkan Email dan Nomor Induk, bukan Nama');
        $sheet->setCellValue("A" . ($notesRow + 5), '- Format No HP: 10-15 digit angka');

        $writer = new Xlsx($spreadsheet);
        $filename = "template_import_{$type}.xlsx";
        $path = storage_path("app/public/{$filename}");
        $writer->save($path);

        return $path;
    }
}
