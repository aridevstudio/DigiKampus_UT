<x-layouts.dosen title="Progres Mahasiswa" active="progres">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Progres Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau perkembangan belajar mahasiswa di semua kursus Anda</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('dosen.progres') }}" class="flex flex-wrap items-center gap-4 mb-6">
        <div class="relative flex-1 max-w-xs">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama mahasiswa..." class="w-full px-4 py-2 pl-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <div class="relative">
            <select name="course" onchange="this.form.submit()" class="appearance-none px-4 py-2 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all" {{ ($courseFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kursus</option>
                @foreach($coursesForFilter ?? [] as $course)
                <option value="{{ $course->id_course }}" {{ ($courseFilter ?? '') == $course->id_course ? 'selected' : '' }}>{{ $course->nama_course }}</option>
                @endforeach
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </form>

    {{-- Progress Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Progres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enrollments ?? [] as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($enrollment->mahasiswa?->profile?->foto_profile)
                                    <img src="{{ asset('storage/' . $enrollment->mahasiswa->profile->foto_profile) }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($enrollment->mahasiswa?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900 dark:text-white">{{ $enrollment->mahasiswa?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->course?->nama_course ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ round($enrollment->progress ?? 0) }}%</span>
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
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColors[$enrollment->status] ?? $statusColors['aktif'] }}">
                                {{ ucfirst($enrollment->status ?? 'aktif') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p>Belum ada mahasiswa yang terdaftar di kursus Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($enrollments) && $enrollments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>
</x-layouts.dosen>
