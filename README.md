# RESULT TEST LARAVEL


🚀 Laravel Test Result Exporter
Laravel Test Result Exporter adalah paket elegan yang membantu Anda menjalankan pengujian (automated tests) dan merangkum hasilnya ke dalam format JSON serta PDF secara otomatis. Sangat cocok untuk dokumentasi hasil test atau sekadar memantau kesehatan aplikasi Anda.


🛠 Instalasi
Ikuti langkah-langkah di bawah ini untuk mengintegrasikan paket ke dalam proyek Laravel Anda:

1. Install via Composer
Buka terminal dan jalankan perintah berikut:

composer require giannels/test-result-laravel:devmain

3. Registrasi Service Provider
Buka file config/app.php dan tambahkan TestResultServiceProvider ke dalam array providers. Langkah ini memastikan Laravel mengenali perintah dan rute dari paket ini.

'providers' => ServiceProvider::defaultProviders()->merge([
    // ... Provider lainnya
    
    /*
     * Package Service Providers...
     */
    Giannels\TestResultLaravel\TestResultServiceProvider::class,
])->toArray(),

5. Refresh Konfigurasi
Agar perubahan terbaca dengan sempurna, bersihkan cache view dan konfigurasi Anda:

php artisan view:clear
php artisan config:clear

Gemini said
Tentu, ini adalah draf README.md yang profesional, bersih, dan mudah diikuti untuk paket Anda. Saya telah menyusunnya agar terlihat menarik bagi pengguna lain yang ingin menggunakan alat Anda.

🚀 Laravel Test Result Exporter
Laravel Test Result Exporter adalah paket elegan yang membantu Anda menjalankan pengujian (automated tests) dan merangkum hasilnya ke dalam format JSON serta PDF secara otomatis. Sangat cocok untuk dokumentasi laporan magang, audit proyek, atau sekadar memantau kesehatan aplikasi Anda.

🛠 Instalasi
Ikuti langkah-langkah di bawah ini untuk mengintegrasikan paket ke dalam proyek Laravel Anda:

1. Install via Composer
Buka terminal dan jalankan perintah berikut:

Bash
composer require giannels/test-result-laravel
2. Registrasi Service Provider
Buka file config/app.php dan tambahkan TestResultServiceProvider ke dalam array providers. Langkah ini memastikan Laravel mengenali perintah dan rute dari paket ini.

PHP
'providers' => ServiceProvider::defaultProviders()->merge([
    // ... Provider lainnya
    
    /*
     * Package Service Providers...
     */
    Giannels\TestResultLaravel\TestResultServiceProvider::class,
])->toArray(),
3. Refresh Konfigurasi
Agar perubahan terbaca dengan sempurna, bersihkan cache view dan konfigurasi Anda:

Bash
php artisan view:clear
php artisan config:clear


🚀 Cara Penggunaan
Menjalankan Test & Export
Untuk menjalankan seluruh test sekaligus mengekspor hasilnya, cukup jalankan satu perintah:
php artisan test:export


Melihat Hasil Laporan
Setelah perintah selesai dijalankan, Anda dapat mengakses laporan dalam beberapa format:

JSON: File akan tersimpan di storage/app/testing/test-results.json.
Preview PDF: Buka browser dan akses http://127.0.0.1:8000/test-result/preview.
Download PDF: Akses http://127.0.0.1:8000/test-result/download.


📊 Fitur Utama
✅ Auto-Parsing: Mengubah output terminal PHPUnit yang rumit menjadi data JSON yang rapi.
✅ Visual Report: Menghasilkan laporan PDF yang cantik menggunakan DomPDF.
✅ Summary Stats: Menampilkan statistik Passed, Failed, dan Risky secara instan.
✅ Category Grouping: Hasil test dikelompokkan berdasarkan Class untuk memudahkan pembacaan.


📝 Lisensi
Paket ini bersifat open-source.
Dibuat oleh Giannels
