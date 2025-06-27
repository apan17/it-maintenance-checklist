<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checklist;
use App\Models\Asset;

class CreateChecklistDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:daily';

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
            $query->where('maintenance_frequency', 'Daily');
        })->get();

        foreach ($assets as $asset) {
            $checklist = Checklist::create([
                'asset_id' => $asset->id,
                'asset_status' => $asset->status,
                'due_date' => now()->setTime(17, 0), // Set due date to 5 PM the same day
            ]);
        }
    }
}
