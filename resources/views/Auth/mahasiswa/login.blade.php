<form action="{{ route('mahasiswa.post') }}" method="POST">
    @csrf
    <div>
        <label for="nis">NIM</label>
        <input type="text" name="nim" id="nim" placeholder="Masukkan NIS" required>
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Masukkan Password" required>
    </div>

    <button type="submit">Login</button>
</form>
