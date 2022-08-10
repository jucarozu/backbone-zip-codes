<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations and seeders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Running migrations...');
        \Artisan::call('migrate:fresh');

        $this->info('Running seeders...');
        \Artisan::call('db:seed');

        $this->info('Initialization completed.');

        return Command::SUCCESS;
    }
}
