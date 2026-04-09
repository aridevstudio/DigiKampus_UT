@once
    @if(config('services.midtrans.client_key'))
        <script
            type="text/javascript"
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}">
        </script>
    @endif

    <script>
        (() => {
            if (window.__mahasiswaMidtransSnapReady) {
                return;
            }

            window.__mahasiswaMidtransSnapReady = true;

            function buildMidtransResultUrl(baseUrl, result = {}) {
                const url = new URL(baseUrl, window.location.origin);

                [
                    'order_id',
                    'transaction_status',
                    'payment_type',
                    'fraud_status',
                    'status_code',
                ].forEach((key) => {
                    if (result[key]) {
                        url.searchParams.set(key, result[key]);
                    }
                });

                return url.toString();
            }

            window.openMahasiswaMidtransSnap = function openMahasiswaMidtransSnap(options = {}) {
                const snapToken = options.snapToken;

                if (!snapToken) {
                    throw new Error('Token Snap Midtrans tidak tersedia.');
                }

                if (!window.snap || typeof window.snap.pay !== 'function') {
                    if (options.fallbackUrl) {
                        window.location.href = options.fallbackUrl;
                        return true;
                    }

                    throw new Error('Midtrans Snap belum siap. Coba muat ulang halaman.');
                }

                window.snap.pay(snapToken, {
                    onSuccess(result) {
                        if (typeof options.onSuccess === 'function') {
                            options.onSuccess(result);
                            return;
                        }

                        window.location.href = buildMidtransResultUrl(options.finishUrl || options.pendingUrl, result);
                    },
                    onPending(result) {
                        if (typeof options.onPending === 'function') {
                            options.onPending(result);
                            return;
                        }

                        window.location.href = buildMidtransResultUrl(options.pendingUrl || options.finishUrl, result);
                    },
                    onError(result) {
                        if (typeof options.onError === 'function') {
                            options.onError(result);
                            return;
                        }

                        window.location.href = buildMidtransResultUrl(options.errorUrl || options.finishUrl || options.pendingUrl, result);
                    },
                    onClose() {
                        if (typeof options.onClose === 'function') {
                            options.onClose();
                            return;
                        }

                        if (options.closeUrl) {
                            window.location.href = options.closeUrl;
                        }
                    },
                });

                return true;
            };

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-midtrans-snap-trigger]');
                if (!trigger) {
                    return;
                }

                event.preventDefault();

                trigger.disabled = true;

                Promise.resolve()
                    .then(() => window.openMahasiswaMidtransSnap({
                        snapToken: trigger.dataset.snapToken || '',
                        finishUrl: trigger.dataset.finishUrl || '',
                        pendingUrl: trigger.dataset.pendingUrl || '',
                        errorUrl: trigger.dataset.errorUrl || '',
                        closeUrl: trigger.dataset.closeUrl || '',
                        fallbackUrl: trigger.dataset.fallbackUrl || '',
                        onClose: () => {
                            trigger.disabled = false;
                        },
                    }))
                    .catch((error) => {
                        trigger.disabled = false;
                        alert(error.message || 'Gagal membuka Midtrans Snap.');
                    });
            });
        })();
    </script>
@endonce
