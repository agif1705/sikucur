<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class bersihkan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bersihkan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'membersihkan route:clear, config:clear, cache:clear, view:clear, optimize:clear';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('route:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('queue:restart');
        $this->call('view:clear');
        $this->call('view:cache');
        $this->call('optimize'); // Hanya untuk Laravel < 11 (karena di Laravel 11+ dihapus)
        $this->call('filament:optimize'); // Hanya untuk Laravel < 11 (karena di Laravel 11+ dihapus)
        $this->call('filament:optimize-clear'); // Hanya untuk Laravel < 11 (karena di Laravel 11+ dihapus)

        // Optional: Bisa juga menjalankan optimize

        $this->info('Aplikasi berhasil di-clear dan di-cache!');
    }
}
