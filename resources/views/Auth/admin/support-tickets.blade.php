<x-layouts.admin :active="'support-tickets'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tiket Support</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola pertanyaan mahasiswa yang belum terjawab oleh FAQ.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total tiket terbuka</p>
            <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($openCount) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Tiket terjawab</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($answeredCount) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total tiket tampil</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($tickets->total()) }}</p>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('admin.support-tickets') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_180px_140px]">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari subjek, pertanyaan, nama, email, atau nomor induk..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            >
            <select
                name="status"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            >
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua status</option>
                <option value="open" {{ $statusFilter === 'open' ? 'selected' : '' }}>Open</option>
                <option value="answered" {{ $statusFilter === 'answered' ? 'selected' : '' }}>Answered</option>
            </select>
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($tickets as $ticket)
            @php
                $isSelected = $selectedTicketId === (int) $ticket->id_support_ticket;
            @endphp
            <div id="ticket-{{ $ticket->id_support_ticket }}" class="rounded-2xl border bg-white shadow-sm transition dark:bg-gray-800 {{ $isSelected ? 'border-blue-400 ring-2 ring-blue-200 dark:border-blue-500 dark:ring-blue-500/20' : 'border-gray-100 dark:border-gray-700' }}">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ticket->subject }}</h2>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $ticket->status === 'answered' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">
                                    {{ strtoupper($ticket->status) }}
                                </span>
                                @if($isSelected)
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                        Dari notifikasi
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $ticket->mahasiswa?->name ?? 'Mahasiswa' }}
                                • {{ $ticket->mahasiswa?->email ?? '-' }}
                                • {{ $ticket->mahasiswa?->profile?->nomor_induk ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Dibuat {{ optional($ticket->created_at)->translatedFormat('d M Y H:i') }}
                                @if($ticket->answered_at)
                                    • Dijawab {{ optional($ticket->answered_at)->translatedFormat('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-2 text-xs text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            Ticket #{{ $ticket->id_support_ticket }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 px-5 py-5 xl:grid-cols-[minmax(0,1fr)_420px]">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pertanyaan mahasiswa</h3>
                        <div class="mt-2 rounded-xl bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            {{ $ticket->question }}
                        </div>

                        @if($ticket->faq_suggestion)
                            <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <p class="font-semibold">Saran FAQ</p>
                                <p class="mt-1">{{ $ticket->faq_suggestion }}</p>
                            </div>
                        @endif

                        @if($ticket->admin_reply)
                            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                <p class="font-semibold">Balasan admin</p>
                                <p class="mt-1 whitespace-pre-line">{{ $ticket->admin_reply }}</p>
                                <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400">
                                    Oleh {{ $ticket->answeredBy?->name ?? 'Admin' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $ticket->status === 'answered' ? 'Perbarui balasan' : 'Balas tiket' }}
                        </h3>

                        <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket->id_support_ticket) }}?status={{ urlencode($statusFilter) }}{{ $search !== '' ? '&search=' . urlencode($search) : '' }}" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select
                                    name="status"
                                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>Answered</option>
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Tetap Open</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Balasan</label>
                                <textarea
                                    name="admin_reply"
                                    rows="7"
                                    required
                                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    placeholder="Tulis balasan untuk mahasiswa..."
                                >{{ old('admin_reply', $isSelected ? $ticket->admin_reply : $ticket->admin_reply) }}</textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Simpan Balasan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center dark:border-gray-600 dark:bg-gray-800">
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Belum ada tiket support</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Notifikasi support baru akan mengarah ke halaman ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>

    @if($selectedTicketId)
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const target = document.getElementById('ticket-{{ $selectedTicketId }}');
                if (!target) {
                    return;
                }

                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
        @endpush
    @endif
</x-layouts.admin>
