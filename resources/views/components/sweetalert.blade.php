@if (session('status') || session('success') || session('error') || session('alert') || session('info') || $errors->any())
<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const tailwindButtonClass = 'bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors';

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
                    confirmButton: tailwindButtonClass
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
                    confirmButton: tailwindButtonClass
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
                    confirmButton: tailwindButtonClass
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
                    confirmButton: tailwindButtonClass
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
                    confirmButton: tailwindButtonClass
                }
            });
        @endif
    });
</script>
@endif
