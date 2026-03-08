<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMiningStat;

class MiningDecayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mining-decay-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $stats = UserMiningStat::where('fire_bar','>',0)
            ->where('last_fire_given_at','<=',now()->subDays(2))
            ->get();

        foreach ($stats as $stat) {

            $stat->fire_bar -= 1;
            $stat->last_fire_given_at = now();

            $stat->save();
        }

    }
}
