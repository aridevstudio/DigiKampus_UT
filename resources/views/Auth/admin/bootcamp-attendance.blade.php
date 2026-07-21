<x-layouts.admin :active="'bootcamp-tiket'">
@php
    use Illuminate\Support\Str;
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('admin.bootcamp-tiket') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Bootcamp & Tiket</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Antrean Verifikasi Bukti Kehadiran</span>
    </nav>

    <header class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-1">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[.14em] text-blue-700 dark:text-blue-300">Operasional bootcamp</p>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-950 dark:text-white">Verifikasi bukti kehadiran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                Daftar bukti kehadiran yang diunggah mahasiswa. Setujui agar memenuhi prasyarat proyek akhir, atau tolak dengan catatan agar mahasiswa dapat mengunggah bukti baru.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                {{ number_format($pendingCount) }} pending
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                {{ number_format($verifiedCount) }} verified
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                {{ number_format($rejectedCount) }} rejected
            </span>
        </div>
    </header>

    <nav aria-label="Status bukti" class="flex gap-2 overflow-x-auto rounded-xl border border-black/10 bg-white p-1.5 shadow-sm dark:border-white/10 dark:bg-gray-800">
        @foreach(['pending' => ['Perlu ditinjau', $pendingCount], 'verified' => ['Disetujui', $verifiedCount], 'rejected' => ['Ditolak', $rejectedCount]] as $key => [$label, $count])
            <a href="{{ route('admin.bootcamp-tiket.attendance', array_filter(['status' => $key, 'search' => $searchQuery])) }}" @class(['inline-flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500', 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300' => $selectedStatus === $key, 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700' => $selectedStatus !== $key])>{{ $label }}<span class="rounded-full bg-black/5 px-2 py-0.5 text-xs tabular-nums dark:bg-white/10">{{ $count }}</span></a>
        @endforeach
    </nav>

    <form method="GET" action="{{ route('admin.bootcamp-tiket.attendance') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-5 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Cari mahasiswa / course / sesi</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, course, atau session_key"
                   class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $key => $label)
                    <option value="{{ $key }}" @selected(request('status', 'pending') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
            Terapkan
        </button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Mahasiswa</th>
                        <th class="px-4 py-3 text-left font-semibold">Bootcamp / Sesi</th>
                        <th class="px-4 py-3 text-left font-semibold">Bukti</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Diunggah</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($attendances as $row)
                        @php
                            $statusBadge = match($row->status) {
                                'verified' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Verified'],
                                'rejected' => ['bg-rose-50 text-rose-700 border-rose-200', 'Rejected'],
                                default => ['bg-amber-50 text-amber-700 border-amber-200', 'Pending'],
                            };
                            $proofUrl = $row->proof_file ? asset('storage/' . $row->proof_file) : null;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $row->user?->name ?? 'Mahasiswa' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $row->user?->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $row->course?->nama_course ?? 'Course #' . $row->id_course }}</p>
                                <p class="text-xs text-gray-700 dark:text-gray-200 font-medium">{{ $row->humanSessionLabel() }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $row->session_key }}</p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                @if($proofUrl)
                                    <button type="button" data-proof-url="{{ $proofUrl }}" data-proof-title="{{ $row->user?->name ?? 'Mahasiswa' }}" data-proof-context="{{ ($row->course?->nama_course ?? 'Bootcamp') . ' - ' . $row->humanSessionLabel() }}"
                                       class="inline-flex items-center gap-1 rounded-md border border-gray-200 px-2.5 py-1.5 text-blue-600 hover:border-blue-300 hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat bukti
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                                @if($row->catatan_mahasiswa)
                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 italic line-clamp-2" title="{{ $row->catatan_mahasiswa }}">
                                        "{{ $row->catatan_mahasiswa }}"
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold border {{ $statusBadge[0] }}">
                                    {{ $statusBadge[1] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-600 dark:text-gray-300 text-xs">
                                {{ optional($row->created_at)->diffForHumans() ?? '-' }}
                            </td>
                            <td class="px-4 py-3 align-top text-right">
                                <div class="flex flex-col items-end gap-2">
                                    @if($row->status !== \App\Models\BootcampLiveClassAttendance::STATUS_VERIFIED)
                                        <form method="POST" action="{{ route('admin.bootcamp.attendance.verify', $row->id_bootcamp_live_class_attendance) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium transition">
                                                Setujui
                                            </button>
                                        </form>
                                    @endif
                                    @if($row->status !== \App\Models\BootcampLiveClassAttendance::STATUS_REJECTED)
                                        <form method="POST" action="{{ route('admin.bootcamp.attendance.reject', $row->id_bootcamp_live_class_attendance) }}"
                                              data-reject-form
                                              data-row-id="{{ $row->id_bootcamp_live_class_attendance }}">
                                            @csrf
                                            <input type="hidden" name="catatan" value="">
                                            <button type="button"
                                                    data-reject-trigger="{{ $row->id_bootcamp_live_class_attendance }}" data-reject-student="{{ $row->user?->name ?? 'Mahasiswa' }}" data-reject-session="{{ $row->humanSessionLabel() }}"
                                                    class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium transition">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    @if($row->catatan_reviewer)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 italic max-w-xs line-clamp-2" title="{{ $row->catatan_reviewer }}">Catatan: {{ $row->catatan_reviewer }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada bukti kehadiran dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($attendances, 'links'))
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

<dialog id="proofDialog" aria-labelledby="proofDialogTitle" class="m-auto w-[calc(100%-2rem)] max-w-4xl overflow-hidden rounded-2xl bg-white p-0 text-gray-950 shadow-2xl backdrop:bg-black/50 dark:bg-gray-900 dark:text-white">
    <div class="flex items-start justify-between border-b border-black/10 px-5 py-4 dark:border-white/10"><div class="min-w-0"><h2 id="proofDialogTitle" class="truncate text-sm font-semibold">Pratinjau bukti</h2><p id="proofDialogContext" class="mt-0.5 truncate text-xs text-gray-500"></p></div><button type="button" data-close-proof aria-label="Tutup" class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-800">X</button></div>
    <div id="proofPreview" class="flex h-[65vh] items-center justify-center bg-[#f6f5f4] p-3 dark:bg-gray-950"></div>
    <div class="flex justify-end gap-2 border-t border-black/10 px-5 py-3 dark:border-white/10"><a id="proofOpen" target="_blank" rel="noopener" class="inline-flex min-h-[38px] items-center rounded-md border border-gray-300 px-3 text-sm font-medium dark:border-gray-600">Buka tab baru</a><button type="button" data-close-proof class="min-h-[38px] rounded-md bg-gray-900 px-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900">Selesai</button></div>
</dialog>
<dialog id="rejectDialog" aria-labelledby="rejectDialogTitle" class="m-auto w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl bg-white p-0 text-gray-950 shadow-2xl backdrop:bg-black/50 dark:bg-gray-900 dark:text-white">
    <div class="p-5"><h2 id="rejectDialogTitle" class="text-base font-semibold">Tolak bukti kehadiran?</h2><p id="rejectContext" class="mt-1 text-sm text-gray-500"></p><label for="rejectReason" class="mb-1.5 mt-5 block text-sm font-medium">Alasan penolakan <span class="text-orange-600">*</span></label><textarea id="rejectReason" rows="4" minlength="3" maxlength="1000" required placeholder="Contoh: Nama peserta dan judul sesi tidak terlihat." class="w-full resize-none rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-950 dark:text-white"></textarea><p class="mt-1.5 text-xs text-gray-500">Catatan dikirim kepada mahasiswa agar bukti dapat diperbaiki.</p></div>
    <div class="flex justify-end gap-2 border-t border-black/10 bg-[#f6f5f4] px-5 py-3 dark:border-white/10 dark:bg-gray-800"><button type="button" data-close-reject class="min-h-[38px] rounded-md border border-gray-300 bg-white px-3 text-sm font-medium dark:border-gray-600 dark:bg-gray-900">Batal</button><button type="button" id="confirmReject" class="min-h-[38px] rounded-md bg-orange-600 px-3 text-sm font-semibold text-white hover:bg-orange-700">Tolak & kirim catatan</button></div>
</dialog>

@push('scripts')
<script>
(() => {
 const proofDialog=document.getElementById('proofDialog'), preview=document.getElementById('proofPreview');
 document.querySelectorAll('[data-proof-url]').forEach(b=>b.addEventListener('click',()=>{const u=b.dataset.proofUrl; document.getElementById('proofDialogTitle').textContent=b.dataset.proofTitle; document.getElementById('proofDialogContext').textContent=b.dataset.proofContext; document.getElementById('proofOpen').href=u; preview.replaceChildren(); const m=/\.pdf(?:$|\?)/i.test(u)?document.createElement('iframe'):document.createElement('img'); m.src=u; m.className='h-full max-h-full w-full max-w-full rounded-md object-contain'; m.title='Bukti kehadiran'; m.alt='Bukti kehadiran'; preview.appendChild(m); proofDialog.showModal()}));
 document.querySelectorAll('[data-close-proof]').forEach(b=>b.addEventListener('click',()=>proofDialog.close()));
 const rejectDialog=document.getElementById('rejectDialog'), reason=document.getElementById('rejectReason'); let form=null;
 document.querySelectorAll('[data-reject-trigger]').forEach(b=>b.addEventListener('click',()=>{form=document.querySelector(`[data-row-id="${b.dataset.rejectTrigger}"]`); document.getElementById('rejectContext').textContent=`${b.dataset.rejectStudent} - ${b.dataset.rejectSession}`; reason.value=''; rejectDialog.showModal(); requestAnimationFrame(()=>reason.focus())}));
 document.querySelectorAll('[data-close-reject]').forEach(b=>b.addEventListener('click',()=>rejectDialog.close()));
 document.getElementById('confirmReject').addEventListener('click',()=>{reason.value=reason.value.trim(); if(!reason.reportValidity()||!form)return; form.querySelector('input[name="catatan"]').value=reason.value; form.submit()});
 [proofDialog,rejectDialog].forEach(d=>d.addEventListener('click',e=>{if(e.target===d)d.close()}));
})();
</script>
@endpush
</x-layouts.admin>
