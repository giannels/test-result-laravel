<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class TestReportController extends Controller
{
    private $jsonPath;

    public function __construct()
    {
        /**
         * Menggunakan storage_path standar Laravel agar universal 
         * di folder manapun paket ini diinstall.
         */
        $this->jsonPath = storage_path('app/testing/test-results.json');
    }

    public function downloadPDF()
    {
        $data = $this->prepareData();
        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan. Jalankan php artisan test:export dulu.'], 404);
        }

        // Nama view diarahkan ke lokasi standar resources/views/pdf
        $pdf = Pdf::loadView('pdf.test-report', $data)->setPaper('a4', 'portrait');
        return $pdf->download('Test-Report-Final.pdf');
    }

    public function previewPDF()
    {
        $data = $this->prepareData();
        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        $pdf = Pdf::loadView('pdf.test-report', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('Test-Report-Preview.pdf');
    }

    public function runTestAndGeneratePDF()
    {
        /**
         * Menaikkan limit eksekusi karena testing bisa memakan waktu lama.
         * Memanggil command 'test:export' yang sudah digenerate sebelumnya.
         */
        set_time_limit(1200); 
        Artisan::call('test:export');
        
        return $this->previewPDF();
    }

    private function prepareData()
    {
        if (!File::exists($this->jsonPath)) {
            return null;
        }

        $jsonContent = File::get($this->jsonPath);
        $json = json_decode($jsonContent, true);

        if (!$json) {
            return null;
        }

        return [
            'date' => $json['generated_at'] ?? now()->format('d/m/Y H:i:s'),
            'duration' => $json['duration'] ?? '0s',
            
            // Header Stats dengan default value 0 jika data tidak lengkap
            'total_tests' => $json['stats']['total'] ?? 0,
            'passed' => $json['stats']['passed'] ?? 0,
            'failed' => $json['stats']['failed'] ?? 0,
            'risky'  => $json['stats']['risky'] ?? 0,
            
            // Menggunakan summary category jika tersedia, atau array kosong jika tidak
            'category_summary' => $json['summary_by_category'] ?? [],
            
            /**
             * Mengelompokkan detail berdasarkan class. 
             * Menggunakan helper collect() bawaan Laravel agar universal.
             */
            'results' => collect($json['details'] ?? [])->groupBy('class'),
        ];
    }
}