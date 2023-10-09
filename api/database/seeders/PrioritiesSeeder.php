<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now()->format('Y-m-d H:i:s');
        $data = [
            [
                'id' => 1,
                'name' => 'Low',
                'level' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Normal',
                'level' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'High',
                'level' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Urgent',
                'level' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Priority::insert($data);
    }
}
