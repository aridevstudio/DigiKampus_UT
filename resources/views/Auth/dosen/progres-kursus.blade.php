<x-layouts.dosen title="Progres Kursus" active="kursus-saya">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('dosen.kursus') }}" class="hover:text-blue-500">Kursus Saya</a>
            <span>/</span>
            <span>Progres</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Progres Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $course->nama_course }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($enrollments) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Mahasiswa</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
            <p class="text-2xl font-bold text-green-600">{{ $enrollments->where('progress', 100)->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
            <p class="text-2xl font-bold text-blue-600">{{ round($enrollments->avg('progress') ?? 0) }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Progres</p>
        </div>
    </div>

    {{-- Progress List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Progres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($enrollment['foto'])
                                    <img src="{{ asset('storage/' . $enrollment['foto']) }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($enrollment['nama'], 0, 1)) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900 dark:text-white">{{ $enrollment['nama'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $enrollment['tanggal_daftar'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full {{ $enrollment['progress'] == 100 ? 'bg-green-500' : 'bg-blue-500' }} rounded-full" style="width: {{ $enrollment['progress'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $enrollment['progress'] }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'aktif' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'selesai' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'nonaktif' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColors[$enrollment['status']] ?? $statusColors['aktif'] }}">
                                {{ ucfirst($enrollment['status'] ?? 'aktif') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <p>Belum ada mahasiswa yang terdaftar di kursus ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('dosen.kursus.modul', $course->id_course) }}" class="inline-flex items-center gap-2 text-blue-500 hover:text-blue-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Kelola Modul
        </a>
    </div>
</x-layouts.dosen>
