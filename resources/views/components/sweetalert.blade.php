@if (session('status') || session('success') || session('error') || session('alert') || session('info') || $errors->any())
<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const alertButtonClass = 'app-swal-confirm';

        @if (session('status') || session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('status') ?? session('success') }}',
                confirmButtonText: 'Oke',
                showCancelButton: false,
                showDenyButton: false,
                buttonsStyling: false,
                customClass: { 
                    container: 'font-inter',
                    popup: 'app-swal-popup',
                    title: 'app-swal-title',
                    htmlContainer: 'app-swal-html',
                    confirmButton: alertButtonClass
                }
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '{{ session('error') }}',
                confirmButtonText: 'Oke',
                showCancelButton: false,
                showDenyButton: false,
                buttonsStyling: false,
                customClass: { 
                    container: 'font-inter',
                    popup: 'app-swal-popup',
                    title: 'app-swal-title',
                    htmlContainer: 'app-swal-html',
                    confirmButton: alertButtonClass
                }
            });
        @elseif (session('alert'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: '{{ session('alert') }}',
                confirmButtonText: 'Oke',
                showCancelButton: false,
                showDenyButton: false,
                buttonsStyling: false,
                customClass: { 
                    container: 'font-inter',
                    popup: 'app-swal-popup',
                    title: 'app-swal-title',
                    htmlContainer: 'app-swal-html',
                    confirmButton: alertButtonClass
                }
            });
        @elseif (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '{{ session('info') }}',
                confirmButtonText: 'Oke',
                showCancelButton: false,
                showDenyButton: false,
                buttonsStyling: false,
                customClass: { 
                    container: 'font-inter',
                    popup: 'app-swal-popup',
                    title: 'app-swal-title',
                    htmlContainer: 'app-swal-html',
                    confirmButton: alertButtonClass
                }
            });
        @elseif ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Data gagal disimpan',
                html: `
                    <ul class="text-left text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonText: 'Oke',
                showCancelButton: false,
                showDenyButton: false,
                buttonsStyling: false,
                customClass: { 
                    container: 'font-inter',
                    popup: 'app-swal-popup',
                    title: 'app-swal-title',
                    htmlContainer: 'app-swal-html',
                    confirmButton: alertButtonClass
                }
            });
        @endif
    });
</script>
@endif
