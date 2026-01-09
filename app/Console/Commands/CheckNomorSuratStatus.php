<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Support\Facades\Log;

class CheckNomorSuratStatus extends Command
{
    use GeneratesNomorSurat;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nomor-surat:check 
                            {--reset : Reset nomor surat counter}
                            {--stats : Show statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check nomor surat status, reset counter, or show statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            if ($this->option('reset')) {
                $this->handleReset();
            } elseif ($this->option('stats')) {
                $this->handleStats();
            } else {
                $this->handleCheck();
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('CheckNomorSuratStatus command error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Handle check command
     */
    protected function handleCheck()
    {
        $this->info('=== Status Nomor Surat ===');
        $this->newLine();

        $activeTahunAjaran = $this->getActiveTahunAjaran();
        
        $this->table(
            ['Field', 'Value'],
            [
                ['Tahun Ajaran', $activeTahunAjaran->tahun],
                ['Semester', $activeTahunAjaran->semester],
                ['Academic Year ID', $this->getAcademicYearIdentifier($activeTahunAjaran)],
                ['Will Reset on Genap?', $this->shouldResetCounter($activeTahunAjaran) ? 'YES' : 'NO'],
            ]
        );

        $this->newLine();
        
        $lastNomor = $this->getLastUsedNomorSurat();
        if ($lastNomor) {
            $this->info("Last Used Nomor Surat: {$lastNomor}");
        } else {
            $this->warn('No nomor surat found for current academic year');
        }

        $nextNomor = $this->getNextNomorSurat();
        $this->info("Next Nomor Surat: {$nextNomor}");
    }

    /**
     * Handle reset command
     */
    protected function handleReset()
    {
        $this->warn('=== Reset Nomor Surat Counter ===');
        $this->newLine();

        if (!$this->confirm('Are you sure you want to reset the nomor surat counter?')) {
            $this->info('Reset cancelled.');
            return;
        }

        $message = $this->resetNomorSuratCounter();
        
        $this->newLine();
        $this->info($message);
        $this->newLine();
        
        $this->info('✓ Reset process completed successfully!');
    }

    /**
     * Handle stats command
     */
    protected function handleStats()
    {
        $this->info('=== Nomor Surat Statistics ===');
        $this->newLine();

        $stats = $this->getNomorSuratStatistics();

        // General info
        $this->table(
            ['Field', 'Value'],
            [
                ['Tahun Ajaran', $stats['tahun_ajaran']],
                ['Semester', $stats['semester']],
                ['Academic Year ID', $stats['academic_year_id']],
                ['Will Reset on Genap?', $stats['will_reset_on_genap'] ? 'YES' : 'NO'],
            ]
        );

        $this->newLine();
        $this->info('Per Model Statistics:');
        $this->newLine();

        // Model statistics
        $tableData = [];
        foreach ($stats['models'] as $modelName => $modelStats) {
            $tableData[] = [
                $modelName,
                $modelStats['count'],
                $modelStats['latest_number'],
                $modelStats['latest_nomor_surat'] ?? '-',
            ];
        }

        $this->table(
            ['Model', 'Count', 'Latest Number', 'Latest Nomor Surat'],
            $tableData
        );

        // Calculate total
        $totalCount = array_sum(array_column($stats['models'], 'count'));
        $this->newLine();
        $this->info("Total Surat: {$totalCount}");
    }

    /**
     * Get nomor surat prefix (required by trait)
     */
    public function getNomorSuratPrefix()
    {
        return 'UN41.2/TI';
    }
}
