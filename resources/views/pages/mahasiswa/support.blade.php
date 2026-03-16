<x-layouts.dashboard :active="'home'">

<div class="mb-6 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Hubungi Support</h1>
    <p class="text-gray-500 dark:text-gray-400">Lihat jawaban FAQ terlebih dahulu. Jika belum membantu, kirim pertanyaan ke admin.</p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">FAQ / QA</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jawaban cepat untuk kendala yang paling sering terjadi.</p>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                    {{ $faqs->count() }} jawaban
                </span>
            </div>

            <div class="space-y-3">
                @forelse($faqs as $faq)
                    <details class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                        <summary class="cursor-pointer list-none font-medium text-gray-800 dark:text-gray-100">
                            {{ $faq->question }}
                        </summary>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $faq->answer }}</p>
                    </details>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-8 text-center dark:border-gray-600">
                        <p class="font-medium text-gray-700 dark:text-gray-200">FAQ belum tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Tanya Admin</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jika belum terjawab di FAQ, pertanyaan akan diteruskan ke admin.</p>

            <form id="support-ticket-form" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Subjek</label>
                    <input type="text" name="subject" placeholder="Contoh: Pembayaran belum masuk" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Pertanyaan</label>
                    <textarea name="question" rows="5" required placeholder="Jelaskan kendala Anda..." class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Kirim Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Riwayat Tiket</h2>
            <div class="mt-4 space-y-3">
                @forelse($tickets as $ticket)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $ticket->subject }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ optional($ticket->created_at)->diffForHumans() }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $ticket->status === 'answered' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $ticket->question }}</p>
                        @if($ticket->admin_reply)
                            <div class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                                {{ $ticket->admin_reply }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-8 text-center dark:border-gray-600">
                        <p class="font-medium text-gray-700 dark:text-gray-200">Belum ada tiket support</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('support-ticket-form')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const form = event.currentTarget;
        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton || submitButton.disabled) {
            return;
        }

        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Mengirim...';

        try {
            const response = await fetch('{{ route('mahasiswa.support.ask') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengirim pertanyaan support.');
            }

            if (data.resolved) {
                if (window.Swal?.isVisible()) {
                    window.Swal.close();
                }

                await Swal.fire({
                    icon: 'info',
                    title: 'Jawaban ditemukan di FAQ',
                    html: `<div class="text-left"><p class="font-semibold mb-2">${data.data.question}</p><p>${data.data.answer}</p></div>`,
                    confirmButtonText: 'OK',
                });

                form.reset();
            } else {
                if (window.Swal?.isVisible()) {
                    window.Swal.close();
                }

                await showAppAlert(
                    data.message || 'Pertanyaan berhasil diteruskan ke admin.',
                    'success',
                    'Tiket berhasil dibuat',
                    {
                        toast: true,
                        timer: 2200,
                        position: 'top-end',
                    }
                );
            }

            window.location.reload();
        } catch (error) {
            if (window.Swal?.isVisible()) {
                window.Swal.close();
            }

            await showAppAlert(
                error.message || 'Terjadi kesalahan saat mengirim pertanyaan.',
                'error',
                'Gagal',
                {
                    toast: true,
                    timer: 2600,
                    position: 'top-end',
                }
            );
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });
</script>
@endpush

</x-layouts.dashboard>
