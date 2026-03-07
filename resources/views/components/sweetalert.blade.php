@if (session('status') || session('success') || session('error') || session('alert') || session('info') || $errors->any())
<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        @if (session('status') || session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('status') ?? session('success') }}',
                confirmButtonColor: '#3B9BD9',
                customClass: { container: 'font-inter' }
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3B9BD9',
                customClass: { container: 'font-inter' }
            });
        @elseif (session('alert'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: '{{ session('alert') }}',
                confirmButtonColor: '#3B9BD9',
                customClass: { container: 'font-inter' }
            });
        @elseif (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '{{ session('info') }}',
                confirmButtonColor: '#3B9BD9',
                customClass: { container: 'font-inter' }
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
                confirmButtonColor: '#3B9BD9',
                customClass: { container: 'font-inter' }
            });
        @endif
    });
</script>
@endif
