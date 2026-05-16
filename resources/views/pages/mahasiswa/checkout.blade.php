<x-layouts.dashboard :active="'get-courses'">
@php
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop';
    $defaultSelectedPayment = $paymentMethods[0]['id'] ?? 'bca_va';
    $paymentMethodIcons = [
        'bca_va' => asset('assets/payment-methods/bca-va.svg'),
        'bni_va' => asset('assets/payment-methods/bni-va.svg'),
        'bri_va' => asset('assets/payment-methods/bri-va.svg'),
        'echannel' => asset('assets/payment-methods/mandiri-bill.svg'),
        'gopay' => asset('assets/payment-methods/gopay.svg'),
        'qris' => asset('assets/payment-methods/qris.svg'),
    ];

    $voucherCatalog = $vouchers->mapWithKeys(function ($voucher) {
        $normalizedCode = strtoupper(trim((string) $voucher->code));
        $value = (float) $voucher->value;
        $label = $normalizedCode . ' - ' . ($voucher->type === 'percent'
            ? ('Diskon ' . rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') . '%')
            : ('Potongan Rp ' . number_format($value, 0, ',', '.')));

        return [$normalizedCode => [
            'code' => $normalizedCode,
            'label' => $label,
            'type' => $voucher->type,
            'value' => $value,
            'minSubtotal' => (float) $voucher->min_subtotal,
            'usageLimit' => $voucher->usage_limit,
            'usedCount' => (int) $voucher->used_count,
            'remainingUses' => $voucher->remainingUses(),
            'expiresAt' => optional($voucher->expires_at)->format('d M Y H:i'),
        ]];
    });
@endphp

@if(session('success'))
<div class="mb-4 rounded-xl bg-green-100 p-4 text-green-700 dark:bg-green-500/20 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 rounded-xl bg-red-100 p-4 text-red-700 dark:bg-red-500/20 dark:text-red-400">
    {{ session('error') }}
</div>
@endif

<div class="mb-8 flex items-center justify-center gap-2 sm:gap-4 overflow-x-auto animate-fade-in-up">
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">1</div>
        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 sm:text-sm">Keranjang</span>
    </div>
    <div class="h-0.5 w-8 flex-shrink-0 bg-gray-300 dark:bg-gray-600 sm:w-16"></div>
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500 dark:bg-gray-600 dark:text-gray-400">2</div>
        <span class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">Pembayaran</span>
    </div>
    <div class="h-0.5 w-8 flex-shrink-0 bg-gray-300 dark:bg-gray-600 sm:w-16"></div>
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500 dark:bg-gray-600 dark:text-gray-400">3</div>
        <span class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">Selesai</span>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="flex items-center gap-3 animate-fade-in-up delay-100">
            <svg class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Keranjang Anda</h1>
            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">{{ $cartItems->count() }} item</span>
        </div>

        <div class="space-y-4 animate-fade-in-up delay-200">
            @forelse($cartItems as $item)
            @php
                $course = $item->course;
                $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
            @endphp
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
                <div class="flex gap-4">
                    <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="h-20 w-20 flex-shrink-0 rounded-xl object-cover">
                    <div class="min-w-0 flex-1">
                        <h3 class="mb-1 font-bold text-gray-800 dark:text-gray-100">{{ $course->nama_course }}</h3>
                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">Kode: {{ $course->kode_course }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $course->dosen->name ?? 'Instructor' }}</p>
                        <p class="mt-2 text-lg font-bold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($course->harga ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end justify-between">
                        <form action="{{ route('mahasiswa.cart.remove', $item->id_cart) }}" method="POST" onsubmit="return confirm('Hapus dari keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-1 text-sm text-gray-400 transition hover:text-red-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
                <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mb-2 text-lg font-bold text-gray-800 dark:text-gray-100">Keranjang Kosong</h3>
                <p class="mb-4 text-gray-500 dark:text-gray-400">Belum ada kursus di keranjang Anda</p>
                <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-500 px-6 py-2 text-sm font-medium text-white transition hover:bg-blue-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Jelajahi Kursus
                </a>
            </div>
            @endforelse
        </div>

        @if($cartItems->count() > 0)
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937] animate-fade-in-up delay-300">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <svg class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <input id="voucher-code-input" type="text" placeholder="Masukkan kode voucher..." class="flex-1 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-[#111827] dark:text-gray-200">
                <button id="apply-voucher-btn" type="button" class="rounded-lg bg-blue-500 px-6 py-2 text-sm font-medium text-white transition hover:bg-blue-600">
                    Gunakan
                </button>
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                @forelse($vouchers as $voucher)
                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ strtoupper($voucher->code) }}</span>
                @empty
                <span class="text-gray-400">Belum ada voucher aktif saat ini.</span>
                @endforelse
                <span class="text-gray-400">Hanya 1 voucher per transaksi. Voucher mengikuti kuota dan masa berlaku.</span>
            </div>
            <div id="voucher-feedback" class="mt-3 hidden rounded-lg px-3 py-2 text-sm"></div>
            <div id="voucher-active-box" class="mt-3 hidden items-center justify-between rounded-xl border border-green-200 bg-green-50 px-3 py-2 dark:border-green-700/40 dark:bg-green-500/10">
                <div>
                    <p class="text-xs text-green-700 dark:text-green-400">Voucher Aktif</p>
                    <p id="voucher-active-label" class="text-sm font-semibold text-green-700 dark:text-green-300">-</p>
                </div>
                <button id="remove-voucher-btn" type="button" class="rounded-lg border border-green-300 px-2.5 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:border-green-600 dark:text-green-300 dark:hover:bg-green-500/20">
                    Hapus
                </button>
            </div>
        </div>
        @endif
    </div>

    <div class="lg:col-span-1">
        <div class="sticky top-24 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937] animate-fade-in-up delay-200">
            <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-gray-100">Ringkasan Pesanan</h2>

            <div class="mb-4 space-y-3 border-b border-gray-200 pb-4 dark:border-gray-700/50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ count($cartItems) }} kursus)</span>
                    <span id="checkout-subtotal" class="text-gray-800 dark:text-gray-200">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div id="voucher-discount-row" class="hidden justify-between text-sm">
                    <span class="text-green-600 dark:text-green-400">Diskon Voucher</span>
                    <span id="voucher-discount-value" class="text-green-600 dark:text-green-400">-Rp 0</span>
                </div>
                @if($cartItems->count() > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Biaya Layanan</span>
                    <span id="checkout-service-fee" class="text-gray-800 dark:text-gray-200">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <div class="mb-6 flex items-center justify-between">
                <span class="font-medium text-gray-800 dark:text-gray-100">Total Pembayaran</span>
                <span id="checkout-total" class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            @if($cartItems->count() > 0)
            <div class="mb-6">
                <h3 class="mb-3 font-medium text-gray-800 dark:text-gray-100">Metode Pembayaran</h3>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($paymentMethods as $method)
                    @php
                        $methodId = $method['id'] ?? 'midtrans';
                        $methodName = $method['name'] ?? 'Midtrans Payment Gateway';
                        $methodType = $method['type'] ?? 'Secure Checkout';
                        $methodIcon = $method['icon'] ?? 'MT';
                        $methodAccent = $method['accent'] ?? 'from-sky-600 to-blue-500';
                        $methodIconAsset = $paymentMethodIcons[$methodId] ?? null;
                    @endphp
                    <label class="group block cursor-pointer">
                        <input
                            type="radio"
                            name="payment"
                            value="{{ $methodId }}"
                            class="peer sr-only"
                            {{ $loop->first ? 'checked' : '' }}>
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-[0_0_0_3px_rgba(59,130,246,0.12)] hover:border-blue-300 dark:border-gray-700 dark:bg-[#111827] dark:peer-checked:bg-blue-500/10">
                            <div class="flex items-start gap-3">
                                @if($methodIconAsset)
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-gray-100 bg-white p-1.5 shadow-sm dark:border-gray-700 dark:bg-white">
                                    <img src="{{ $methodIconAsset }}" alt="{{ $methodName }}" class="h-full w-full object-contain">
                                </div>
                                @else
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $methodAccent }} text-sm font-bold text-white shadow-sm">
                                    {{ $methodIcon }}
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $methodName }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $methodType }}</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Metode yang Anda pilih akan langsung dibuka di popup Midtrans pada tab ini.</p>
            </div>

            <form id="payment-form" action="{{ route('mahasiswa.payment') }}" method="GET">
                <input type="hidden" name="payment" id="selected-payment" value="{{ $defaultSelectedPayment }}">
                <input type="hidden" name="voucher" id="selected-voucher" value="">
                <button type="submit" class="mb-4 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-500 py-3 font-medium text-white transition hover:bg-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span id="checkout-pay-label">Bayar Sekarang</span>
                </button>
            </form>
            <div id="payment-feedback" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300"></div>

            <div class="mb-4 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Pembayaran aman melalui Midtrans
            </div>

            <div class="rounded-xl bg-green-50 p-4 text-center dark:bg-green-500/10">
                <div class="mb-1 flex items-center justify-center gap-2 text-sm font-medium text-green-600 dark:text-green-400">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Garansi Uang Kembali 100%
                </div>
                <p class="text-xs text-green-600 dark:text-green-400">Dalam 7 hari setelah pembelian</p>
            </div>
            @else
            <div class="py-4 text-center">
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Tambahkan kursus ke keranjang untuk melanjutkan pembayaran</p>
                <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Jelajahi Kursus
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
@include('pages.mahasiswa.partials.midtrans-snap-handler')
<script>
    const checkoutMoney = {
        subtotal: {{ (int) $subtotal }},
        serviceFee: {{ (int) $serviceFee }},
    };

    const voucherCatalog = @json($voucherCatalog);
    let activeVoucher = null;

    function setPaymentFeedback(message = '', type = 'info') {
        const feedback = document.getElementById('payment-feedback');
        if (!feedback) return;

        feedback.classList.remove(
            'hidden',
            'border-amber-200',
            'bg-amber-50',
            'text-amber-700',
            'dark:border-amber-500/30',
            'dark:bg-amber-500/10',
            'dark:text-amber-300',
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'dark:border-red-500/30',
            'dark:bg-red-500/10',
            'dark:text-red-300'
        );

        if (!message) {
            feedback.classList.add('hidden');
            feedback.textContent = '';
            return;
        }

        if (type === 'error') {
            feedback.classList.add('border-red-200', 'bg-red-50', 'text-red-700', 'dark:border-red-500/30', 'dark:bg-red-500/10', 'dark:text-red-300');
        } else {
            feedback.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-700', 'dark:border-amber-500/30', 'dark:bg-amber-500/10', 'dark:text-amber-300');
        }

        feedback.textContent = message;
        feedback.classList.remove('hidden');
    }

    function setCheckoutSubmitting(isSubmitting) {
        const button = document.querySelector('#payment-form button[type="submit"]');
        const label = document.getElementById('checkout-pay-label');

        if (!button || !label) return;

        button.disabled = isSubmitting;
        button.classList.toggle('opacity-70', isSubmitting);
        button.classList.toggle('cursor-not-allowed', isSubmitting);
        label.textContent = isSubmitting ? 'Menyiapkan Pembayaran...' : 'Bayar Sekarang';
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function showVoucherFeedback(message, type = 'error') {
        const el = document.getElementById('voucher-feedback');
        if (!el) return;
        el.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'dark:bg-red-500/20', 'dark:text-red-400', 'bg-green-100', 'text-green-700', 'dark:bg-green-500/20', 'dark:text-green-300');
        if (type === 'success') {
            el.classList.add('bg-green-100', 'text-green-700', 'dark:bg-green-500/20', 'dark:text-green-300');
        } else {
            el.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-500/20', 'dark:text-red-400');
        }
        el.textContent = message;
        el.classList.remove('hidden');
    }

    function clearVoucherFeedback() {
        const el = document.getElementById('voucher-feedback');
        if (!el) return;
        el.classList.add('hidden');
        el.textContent = '';
    }

    function getVoucherDiscount(voucher) {
        if (!voucher) return 0;
        if (voucher.type === 'percent') {
            return Math.round(checkoutMoney.subtotal * (voucher.value / 100));
        }
        return Math.min(voucher.value, checkoutMoney.subtotal);
    }

    function refreshCheckoutSummary() {
        const discount = getVoucherDiscount(activeVoucher);
        const total = Math.max(0, checkoutMoney.subtotal - discount) + checkoutMoney.serviceFee;

        const totalEl = document.getElementById('checkout-total');
        if (totalEl) totalEl.textContent = formatRupiah(total);

        const discountRow = document.getElementById('voucher-discount-row');
        const discountValue = document.getElementById('voucher-discount-value');
        if (discountRow && discountValue) {
            if (discount > 0) {
                discountRow.classList.remove('hidden');
                discountRow.classList.add('flex');
                discountValue.textContent = '-' + formatRupiah(discount);
            } else {
                discountRow.classList.remove('flex');
                discountRow.classList.add('hidden');
            }
        }

        const activeBox = document.getElementById('voucher-active-box');
        const activeLabel = document.getElementById('voucher-active-label');
        if (activeBox && activeLabel) {
            if (activeVoucher) {
                activeBox.classList.remove('hidden');
                activeBox.classList.add('flex');
                activeLabel.textContent = activeVoucher.label;
            } else {
                activeBox.classList.remove('flex');
                activeBox.classList.add('hidden');
                activeLabel.textContent = '-';
            }
        }

        const hiddenVoucher = document.getElementById('selected-voucher');
        if (hiddenVoucher) hiddenVoucher.value = activeVoucher ? activeVoucher.code : '';
    }

    document.querySelectorAll('input[name="payment"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            document.getElementById('selected-payment').value = this.value;
            setPaymentFeedback('');
        });
    });

    document.getElementById('apply-voucher-btn')?.addEventListener('click', function () {
        clearVoucherFeedback();
        const rawCode = (document.getElementById('voucher-code-input')?.value || '').trim().toUpperCase();
        if (!rawCode) {
            showVoucherFeedback('Masukkan kode voucher terlebih dahulu.');
            return;
        }

        const voucher = voucherCatalog[rawCode];
        if (!voucher) {
            showVoucherFeedback('Kode voucher tidak dikenali atau sudah tidak aktif.');
            return;
        }

        if (checkoutMoney.subtotal < voucher.minSubtotal) {
            showVoucherFeedback('Voucher butuh minimal belanja ' + formatRupiah(voucher.minSubtotal) + '.');
            return;
        }

        activeVoucher = voucher;
        refreshCheckoutSummary();
        const expiryInfo = voucher.expiresAt ? ' Berlaku sampai ' + voucher.expiresAt + '.' : '';
        const quotaInfo = voucher.remainingUses === null ? ' Kuota tanpa batas.' : ' Sisa kuota ' + voucher.remainingUses + '.';
        showVoucherFeedback('Voucher ' + voucher.code + ' aktif.' + quotaInfo + expiryInfo, 'success');
    });

    document.getElementById('remove-voucher-btn')?.addEventListener('click', function () {
        activeVoucher = null;
        refreshCheckoutSummary();
        showVoucherFeedback('Voucher dihapus.', 'success');
    });

    refreshCheckoutSummary();

    document.getElementById('payment-form')?.addEventListener('submit', async function (event) {
        if (!window.snap || typeof window.openMahasiswaMidtransSnap !== 'function') {
            return;
        }

        event.preventDefault();
        setPaymentFeedback('');
        setCheckoutSubmitting(true);

        try {
            const url = new URL(this.action, window.location.origin);
            const formData = new FormData(this);
            formData.forEach((value, key) => {
                if (value !== null && `${value}` !== '') {
                    url.searchParams.set(key, value);
                }
            });

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const payload = await response.json();
            if (!response.ok || !payload.success || !payload.snapToken) {
                throw new Error(payload.message || 'Gagal membuat transaksi pembayaran.');
            }

            setCheckoutSubmitting(false);

            window.openMahasiswaMidtransSnap({
                snapToken: payload.snapToken,
                finishUrl: payload.finishUrl,
                pendingUrl: payload.pendingUrl,
                errorUrl: payload.errorUrl,
                closeUrl: payload.closeUrl,
                fallbackUrl: payload.redirectUrl,
                onClose: () => {
                    setPaymentFeedback('Transaksi sudah dibuat. Anda bisa melanjutkan pembayaran dari halaman status transaksi.', 'info');
                    window.location.href = payload.closeUrl || payload.detailUrl || payload.finishUrl;
                },
            });
        } catch (error) {
            setCheckoutSubmitting(false);
            setPaymentFeedback(error.message || 'Gagal membuka pembayaran Midtrans.', 'error');
        }
    });
</script>
@endpush

</x-layouts.dashboard>
