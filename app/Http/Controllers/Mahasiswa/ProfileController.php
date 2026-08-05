<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\CourseRating;
use App\Models\Enrollment;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user()->loadMissing(['profile.jurusan']);
        $profile = $user->profile;
        $jurusan = $profile?->jurusan;

        $userName = $user->name ?? 'Mahasiswa';
        $userEmail = $user->email ?? '-';
        $nomor_induk = $profile?->nomor_induk;
        $noHp = $profile?->no_hp;
        $alamat = $profile?->alamat;
        $tempatLahir = $profile?->tempat_lahir;
        $tanggalLahir = $profile?->tanggal_lahir
            ? Carbon::parse($profile->tanggal_lahir)->translatedFormat('d F Y')
            : null;
        $ttl = collect([$tempatLahir, $tanggalLahir])->filter()->implode(', ');

        $jenisKelaminRaw = $profile?->jenis_kelamin;
        $jenisKelamin = match ($jenisKelaminRaw) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };

        $ipk = $profile?->ipk;
        $totalSks = $profile?->total_sks ?? 0;
        $maxSks = 144;
        $statusAkademik = $profile?->status_akademik ?? 'Aktif';
        $fotoProfile = $this->publicStorageUrlIfExists($profile?->foto_profile)
            ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&size=128&background=3b82f6&color=fff';
        $bio = $profile?->bio;

        $programStudi = $jurusan?->nama_jurusan;
        $fakultas = $jurusan?->fakultas;
        $jenjang = $jurusan?->jenjang;

        $enrollments = Enrollment::where('id_mahasiswa', $user->id)->get();

        $kursusAktif = $enrollments->whereIn('status', ['aktif', 'in_progress'])->count();
        if ($kursusAktif === 0) {
            $kursusAktif = $enrollments->count();
        }

        $tugasDiselesaikan = $enrollments->where('progress', '>=', 100)->count();
        $semesterProgress = (int) round($enrollments->avg('progress') ?? 0);

        $tahunMasuk = $profile?->created_at?->format('Y') ?? $user->created_at?->format('Y');
        $kegiatanTerakhir = $this->buildRecentActivities($user->id);

        return view('pages.mahasiswa.profile', compact(
            'userName',
            'userEmail',
            'nomor_induk',
            'noHp',
            'alamat',
            'tempatLahir',
            'tanggalLahir',
            'ttl',
            'jenisKelamin',
            'ipk',
            'totalSks',
            'maxSks',
            'statusAkademik',
            'fotoProfile',
            'bio',
            'programStudi',
            'fakultas',
            'jenjang',
            'kursusAktif',
            'tugasDiselesaikan',
            'semesterProgress',
            'tahunMasuk',
            'kegiatanTerakhir'
        ));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::guard('mahasiswa')->user()->loadMissing(['profile.jurusan']);
        $profile = $user->profile;
        $jurusan = $profile?->jurusan;

        return view('pages.mahasiswa.edit-profile', [
            'userName' => $user->name ?? '',
            'userEmail' => $user->email ?? '',
            'nomor_induk' => $profile?->nomor_induk ?? '',
            'noHp' => $profile?->no_hp ?? '',
            'alamat' => $profile?->alamat ?? '',
            'tempatLahir' => $profile?->tempat_lahir ?? '',
            'tanggalLahir' => $profile?->tanggal_lahir
                ? Carbon::parse($profile->tanggal_lahir)->format('Y-m-d')
                : '',
            'jenisKelamin' => $profile?->jenis_kelamin ?? 'L',
            'fotoProfile' => $this->publicStorageUrlIfExists($profile?->foto_profile),
            'bio' => $profile?->bio ?? '',
            'programStudi' => $jurusan?->nama_jurusan ?? 'Belum diisi',
            'fakultas' => $jurusan?->fakultas ?? 'Belum diisi',
            'tahunMasuk' => $profile?->created_at?->format('Y') ?? $user->created_at?->format('Y') ?? '-',
        ]);
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/'],
        ], [
            'password.regex' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
        ]);

        $user = Auth::guard('mahasiswa')->user();

        // Update user name
        $user->name = $validated['name'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Update or create profile
        $profileData = [
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        // Handle foto upload - simpan ke local storage
        if ($request->hasFile('foto_profile')) {
            if ($user->profile?->foto_profile && Storage::disk('public')->exists($user->profile->foto_profile)) {
                Storage::disk('public')->delete($user->profile->foto_profile);
            }

            $file = $request->file('foto_profile');
            $path = $file->store('profiles', 'public');
            $profileData['foto_profile'] = $path;
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            Profile::create($profileData);
        }

        return redirect()->route('mahasiswa.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    private function buildRecentActivities(int $mahasiswaId): array
    {
        $kegiatanTerakhir = collect();
        $enrolledCourseIds = Enrollment::where('id_mahasiswa', $mahasiswaId)
            ->pluck('id_course')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $recentAgenda = Agenda::query()
            ->visibleToMahasiswa($mahasiswaId, $enrolledCourseIds)
            ->orderBy('tanggal', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentAgenda as $agenda) {
            $iconMap = [
                'webinar' => ['icon' => 'calendar', 'color' => 'blue'],
                'workshop' => ['icon' => 'calendar', 'color' => 'green'],
                'deadline' => ['icon' => 'calendar', 'color' => 'rose'],
                'quiz' => ['icon' => 'calendar', 'color' => 'yellow'],
            ];
            $iconData = $iconMap[$agenda->tipe] ?? ['icon' => 'calendar', 'color' => 'gray'];
            $agendaDate = $agenda->tanggal ? Carbon::parse($agenda->tanggal) : null;

            $text = $agenda->judul ?? 'Agenda';
            if ($agendaDate) {
                $text .= ', ' . $agendaDate->translatedFormat('d F Y');
            }

            $kegiatanTerakhir->push([
                'icon' => $iconData['icon'],
                'text' => $text,
                'color' => $iconData['color'],
                'date' => $agendaDate?->timestamp ?? 0,
            ]);
        }

        $recentEnrollments = Enrollment::where('id_mahasiswa', $mahasiswaId)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        foreach ($recentEnrollments as $enrollment) {
            if ($enrollment->course) {
                $kegiatanTerakhir->push([
                    'icon' => 'check',
                    'text' => 'Mendaftar kursus ' . $enrollment->course->nama_course,
                    'color' => 'green',
                    'date' => $enrollment->created_at?->timestamp ?? 0,
                ]);
            }
        }

        $recentRatings = CourseRating::where('id_mahasiswa', $mahasiswaId)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        foreach ($recentRatings as $rating) {
            if ($rating->course) {
                $kegiatanTerakhir->push([
                    'icon' => 'chat',
                    'text' => 'Memberikan ulasan untuk ' . $rating->course->nama_course,
                    'color' => 'blue',
                    'date' => $rating->created_at?->timestamp ?? 0,
                ]);
            }
        }

        $items = $kegiatanTerakhir->sortByDesc('date')
            ->take(5)
            ->map(function (array $item) {
                unset($item['date']);
                return $item;
            })
            ->values()
            ->all();

        if (empty($items)) {
            return [
                ['icon' => 'info', 'text' => 'Belum ada kegiatan terbaru', 'color' => 'gray'],
            ];
        }

        return $items;
    }

    private function publicStorageUrlIfExists(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        $path = preg_replace('#^https?://[^/]+/storage/#i', '', $path);
        $path = preg_replace('#^/?storage/#i', '', (string) $path);
        $path = ltrim((string) $path, '/');

        if ($path === '' || !Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/' . $path;
    }
}
