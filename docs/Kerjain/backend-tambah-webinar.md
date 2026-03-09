# Backend: Tambah Webinar

Saat ini di frontend (di file `resources/views/Auth/admin/kursus.blade.php`), kami telah menambahkan tombol "Tambah Webinar" beserta form modal-nya (`addWebinarModal`). Form ini secara fungsional mirip dengan form "Tambah Kursus" karena pada dasarnya menyasar tabel yang sama (`courses`).

## Yang Perlu Diperhatikan / Ditambahkan di Backend (Controller & Model)

1. **Routing dan Method Store**
    - Form webinar pada frontend saat ini akan melakukan submit ke route `admin.kursus.store` (menyatu dengan fungsi penyimpan kursus).
    - Pastikan bahwa `AdminController@storeKursus` dapat menangani input dari modal webinar ini.
    - Pembedanya ada pada input `kategori` yang dikirim dari frontend, yaitu bernilai `webinar`.

2. **Dukungan Tanggal Pelaksanaan (Jadwal Webinar)**
    - Secara default tabel `courses` memiliki bidang `estimasi_waktu` dan `durasi_satuan`, namun untuk **Webinar** biasanya membutuhkan **Tanggal Pelaksanaan** dan **Jam Pelaksanaan**.
    - Jika belum ada di tabel `courses`, silakan pertimbangkan:
        - **Opsi A**: Menambahkan kolom `jadwal_mulai` (timestamp/datetime) di tabel `courses`.
        - **Opsi B**: Menghubungkan webinar dengan tabel `agendas` untuk tracking tanggal dan waktu pelaksanaan.
    - Saat ini, frontend belum bisa merender input "Tanggal Pelaksanaan" dan "Jam Eksekusi" karena belum ada kejelasan desain tabel mana yang akan menampung data tersebut untuk model webinar. Harap sesuaikan `storeKursus` dan tabel bila ingin mendukung ini di masa depan, kemudian infokan kembali ke sisi UI untuk menambah input tanggal.

3. **Link Platform Meeting (Zoom/Google Meet)**
    - Di form Tambah Kursus, tidak ada field khusus untuk link Zoom/Gmeet, hanya ada `youtube_playlist`.
    - Jika Webinar menggunakan platform live meeting, pertimbangkan untuk menambah kolom seperti `meeting_link` ke database, atau fungsikan kolom `youtube_playlist` sementara waktu.

Sila di-review dan tambahkan logic tersebut jika diperlukan agar data "webinar" yang disimpan benar-benar bisa berfungsi layaknya sebuah acara live, alih-alih cuma sebagai format kursus berbentuk rekaman.
