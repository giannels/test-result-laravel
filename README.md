# RESULT TEST LARAVEL

Laravel Test Result Export adalah paket elegan yang membantu Anda menjalankan pengujian dan merangkum hasilnya ke dalam format JSON serta PDF secara otomatis. Sangat cocok untuk dokumentasi laporan magang, audit proyek, atau sekadar memantau kesehatan aplikasi Anda.

# 🛠 Instalasi
Ikuti langkah-langkah di bawah ini untuk mengintegrasikan paket ke dalam proyek Laravel Anda:

1. Install via Composer
Buka terminal dan jalankan perintah berikut:

```bash
composer require giannels/test-result-laravel
```

atau jika tidak bisa, menggunakan

```bash
composer require giannels/test-result-laravel:dev-main
```


2. Registrasi Service Provider
Buka file config/app.php dan tambahkan TestResultServiceProvider ke dalam array providers. Langkah ini memastikan Laravel mengenali perintah dan rute dari paket ini.

```bash
'providers' => ServiceProvider::defaultProviders()->merge([
    // ... Provider lainnya
    
    /*
     * Package Service Providers...
     */
    Giannels\TestResultLaravel\TestResultServiceProvider::class,
])->toArray(),
```


3. Refresh Konfigurasi
Agar perubahan terbaca dengan sempurna, bersihkan cache view dan konfigurasi Anda:

```bash
php artisan view:clear
php artisan config:clear
```

# 🚀 Cara Penggunaan
Menjalankan Test & Export
Untuk menjalankan seluruh test sekaligus mengekspor hasilnya, cukup jalankan satu perintah:
```bash
php artisan test:export
```

Melihat Hasil Laporan
Setelah perintah selesai dijalankan, Anda dapat mengakses laporan dalam beberapa format:

JSON: File akan tersimpan di storage/app/testing/test-results.json.

Preview PDF: Buka browser dan akses http://127.0.0.1:8000/test-result/preview.

Download PDF: Akses http://127.0.0.1:8000/test-result/download.


# 📊 Fitur Utama
✅ Auto-Parsing: Mengubah output terminal PHPUnit yang rumit menjadi data JSON yang rapi.

✅ Visual Report: Menghasilkan laporan PDF yang cantik menggunakan DomPDF.

✅ Summary Stats: Menampilkan statistik Passed, Failed, dan Risky secara instan.

✅ Category Grouping: Hasil test dikelompokkan berdasarkan Class untuk memudahkan pembacaan.


# 📝 Lisensi
Paket ini bersifat open-source di bawah lisensi MIT.
Dibuat oleh Giannels
