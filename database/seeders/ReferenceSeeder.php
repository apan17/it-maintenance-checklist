<?php

namespace Database\Seeders;

use App\Models\RefComponent;
use App\Models\RefMaintenanceFrequency;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        RefMaintenanceFrequency::create([
            'name' => 'DAILY',
            'description' => 'Maintenance to be performed daily.',
            'order' => $j = 1,
        ]);

        RefMaintenanceFrequency::create([
            'name' => 'WEEKLY',
            'description' => 'Maintenance to be performed weekly.',
            'order' => ++$j,
        ]);

        RefMaintenanceFrequency::create([
            'name' => 'MONTHLY',
            'description' => 'Maintenance to be performed monthly.',
            'order' => ++$j,
        ]);

        RefComponent::create([
            'name' => 'ISP',
            'maintenance_frequency' => 'DAILY',
            'order' => $i = 1,
        ]);

        RefComponent::create([
            'name' => 'ACCESS SWITCH',
            'maintenance_frequency' => 'WEEKLY',
            'order' => ++$i,
        ]);

        RefComponent::create([
            'name' => 'UPS',
            'maintenance_frequency' => 'MONTHLY',
            'order' => ++$i,
        ]);
    }
}
