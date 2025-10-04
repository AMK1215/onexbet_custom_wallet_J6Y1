<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PlaceBet;
use Illuminate\Console\Command;

class CleanUpOldPlaceBet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-up-old-place-bet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    $start = Carbon::now()->subMonth()->startOfMonth(); //  1
    $end   = Carbon::now()->subMonth()->endOfMonth();   // 30

    $deletedCount = 0;

    PlaceBet::whereBetween('created_at', [$start, $end])
        ->chunkById(10000, function($placeBets) use (&$deletedCount) {
            foreach ($placeBets as $placeBet) {
                $placeBet->forceDelete();
                $deletedCount++;
            }
        });

    $this->info("Deleted {$deletedCount} PlaceBet from {$start->toDateString()} to {$end->toDateString()}");
}
}
