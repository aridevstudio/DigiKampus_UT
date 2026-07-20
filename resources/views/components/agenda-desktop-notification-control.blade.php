@props(['agenda' => collect()])

@php
    $agendaNotificationPayload = collect($agenda)
        ->filter(fn ($item) => $item->id_agenda && $item->judul && $item->tanggal && $item->waktu_mulai)
        ->map(fn ($item) => [
            'id' => $item->id_agenda,
            'title' => $item->judul,
            'date' => $item->tanggal->format('Y-m-d'),
            'time' => $item->waktu_mulai,
            'type' => $item->tipe,
        ])
        ->values();
@endphp

<div data-agenda-desktop-notifications data-calendar-url="{{ route('mahasiswa.calendar') }}" class="rounded-xl border border-blue-100 bg-blue-50/70 p-3 dark:border-blue-500/20 dark:bg-blue-500/10">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-500 text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a3.001 3.001 0 01-5.714 0M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9z" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifikasi desktop agenda</p>
                <p data-agenda-notification-status class="mt-0.5 text-xs leading-5 text-gray-600 dark:text-gray-300" aria-live="polite"></p>
            </div>
        </div>
        <div class="flex shrink-0 gap-2">
            <button type="button" data-agenda-notification-enable hidden style="display: none" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                Aktifkan notifikasi
            </button>
            <button type="button" data-agenda-notification-test hidden style="display: none" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 dark:border-blue-500/30 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-500/10 dark:focus:ring-offset-gray-800">
                Kirim tes
            </button>
        </div>
    </div>
</div>

@once
    <script>
        window.DigiKampusAgendaNotificationPayload = {{ \Illuminate\Support\Js::from($agendaNotificationPayload) }};
    </script>
@endonce
