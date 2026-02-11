<?php

namespace Giannels\TestResultLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File; 
use Symfony\Component\Process\Process;

class ExportTestCommand extends Command
{
    /**
     * Signature diubah agar menerima argument 'folders' opsional.
     * Jika user tidak mengisi, maka akan menjalankan seluruh folder tests.
     */
    protected $signature = 'test:export {folders?*}'; 
    protected $description = 'Run tests and export results to JSON and PDF';

    private $rawOutput = "";
    private $results = [];
    private $stats = [
        'total' => 0, 
        'passed' => 0, 
        'failed' => 0, 
        'errors' => 0, 
        'risky' => 0, 
        'duration' => 0
    ];

    public function handle()
    {
        $this->showStartMessage();
        
        /**
         * LOGIKA GENERAL:
         * Mengambil input folder dari terminal. 
         * Contoh: php artisan test:export tests/Feature/Login
         * Jika kosong, otomatis menjalankan semua di folder 'tests'.
         */
        $folders = $this->argument('folders');
        if (empty($folders)) {
            $folders = ['tests']; 
        }
        
        $this->runTests($folders);

        $this->info('📊 Menganalisa output terminal...');
        $this->parseResults();
        
        $this->saveToJson();

        $this->showSummary();
        
        return 0;
    }

    private function showStartMessage()
    {
        $this->info('🚀 Starting universal test execution...');
        $this->newLine();
    }

    private function runTests(array $folders)
    {
        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');

        $command = [
            $phpBinary,
            $artisan,
            'test',
            ...$folders,
            '--colors=never' 
        ];

        $process = new Process($command);
        $process->setTimeout(1200); 

        $process->setEnv(['APP_ENV' => 'testing', 'TERM' => 'dumb']);

        $process->run(function ($type, $buffer) {
            $this->rawOutput .= $buffer;
            echo $buffer; 
        });
    }

    private function parseResults()
    {
        $lines = explode("\n", $this->rawOutput);
        $currentClass = 'Unknown';
        $testNumber = 1;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^(PASS|FAIL|WARN)\s+(Tests\\\\.+)/', $line, $matches)) {
                $parts = explode('\\', trim($matches[2]));
                $currentClass = end($parts); 
                continue;
            }

            $status = null;
            $testName = '';
            $time = '0.1s';

            if (preg_match('/^[\x{2713}\x{2714}✓]\s+(.+?)\s+([\d\.]+)s$/u', $line, $m)) {
                $status = 'Passed';
                $testName = $m[1];
                $time = $m[2] . 's';
            }
            elseif (preg_match('/^[\x{2717}\x{2718}⨯×x]\s+(.+?)\s+([\d\.]+)s$/u', $line, $m)) {
                $status = 'Failed';
                $testName = $m[1];
                $time = $m[2] . 's';
            }
            elseif (preg_match('/^!\s+(.+?)\s+([\d\.]+)s$/', $line, $m)) {
                $status = 'Risky';
                $testName = $m[1];
                $time = $m[2] . 's';
            }

            if ($status) {
                $testName = str_replace(['→', '…'], '', $testName);
                
                $this->results[] = [
                    'no' => $testNumber++,
                    'class' => $currentClass,
                    'name' => trim($testName),
                    'status' => $status,
                    'time' => $time,
                    'message' => $status == 'Passed' ? 'Success' : ($status == 'Risky' ? 'Risky Test' : 'Failed')
                ];
            }
        }

        if (preg_match('/Tests:\s+(.+)/', $this->rawOutput, $m)) {
            $summaryText = $m[1];
            
            $this->stats['passed'] = 0; $this->stats['failed'] = 0;
            $this->stats['risky'] = 0; $this->stats['errors'] = 0;

            if (preg_match('/(\d+)\s+passed/', $summaryText, $p)) $this->stats['passed'] = (int)$p[1];
            if (preg_match('/(\d+)\s+failed/', $summaryText, $f)) $this->stats['failed'] = (int)$f[1];
            if (preg_match('/(\d+)\s+risky/', $summaryText, $r))  $this->stats['risky']  = (int)$r[1];
            if (preg_match('/(\d+)\s+error/', $summaryText, $e))  $this->stats['errors'] = (int)$e[1];
            
            $this->stats['total'] = $this->stats['passed'] + $this->stats['failed'] + $this->stats['risky'] + $this->stats['errors'];
        }

        if (preg_match('/Time:\s+([\d\.:]+)/', $this->rawOutput, $m)) {
            $this->stats['duration'] = $m[1];
        }
    }

    private function saveToJson()
    {
        $jsonPath = storage_path('app/testing/test-results.json');
        File::ensureDirectoryExists(dirname($jsonPath));

        $catSummary = [];
        foreach ($this->results as $row) {
            $cat = $row['class']; 
            if (!isset($catSummary[$cat])) {
                $catSummary[$cat] = ['total'=>0, 'passed'=>0, 'failed'=>0, 'risky'=>0];
            }
            $catSummary[$cat]['total']++;
            if ($row['status'] == 'Passed') $catSummary[$cat]['passed']++;
            elseif ($row['status'] == 'Failed') $catSummary[$cat]['failed']++;
            else $catSummary[$cat]['risky']++;
        }

        $data = [
            'generated_at' => date('d/m/Y H:i:s'),
            'duration' => $this->stats['duration'],
            'stats' => $this->stats,
            'summary_by_category' => $catSummary,
            'details' => $this->results
        ];

        File::put($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
        $this->info('✅ JSON file saved to storage/app/testing/');
    }

    private function showSummary()
    {
        $this->newLine();
        $this->info('✅ Test results exported successfully!');
        $this->newLine();
        
        $this->table(['Metric', 'Value'], [
            ['Total Tests', $this->stats['total']],
            ['Passed', '✓ ' . $this->stats['passed']],
            ['Failed', '✗ ' . $this->stats['failed']],
            ['Risky',  '! ' . $this->stats['risky']],
            ['Duration', $this->stats['duration'] . 's'],
        ]);
        
        $this->newLine();
        $this->comment('🔗 Periksa folder storage/app/testing/ untuk hasil JSON.');
        
        $this->newLine();
        $baseUrl = config('app.url');
        // Fix localhost URL jika perlu
        if (str_contains($baseUrl, 'localhost')) {
            $baseUrl = 'http://127.0.0.1:8000';
        }

        $this->comment('🔗 Available URLs:');
        $this->line('  📄 Preview PDF: ' . $baseUrl . '/test-result/preview');
        $this->line('  📄 Download PDF: ' . $baseUrl . '/test-result/download');
    }
}