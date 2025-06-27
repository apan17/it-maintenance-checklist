<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checklist;
use App\Models\Asset;

class CreateChecklistWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:weekly';

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
        $assets = Asset::whereHas('component', function($query){
            $query->where('maintenance_frequency', 'Weekly');
        })->get();

        foreach ($assets as $asset) {
            $checklist = Checklist::create([
                'asset_id' => $asset->id,
                'asset_status' => $asset->status,
                'due_date' => now()->addDay(5)->setTime(17, 0), // Set due date to 5 days from now at 5 PM
            ]);
        }
    }
}
