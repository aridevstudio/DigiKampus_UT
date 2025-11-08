<h1>Hello World</h1>
<form action="{{ route('mahasiswa.logout') }}" method="post">
    @csrf
    <button type="submit">Logout</button>
</form>
