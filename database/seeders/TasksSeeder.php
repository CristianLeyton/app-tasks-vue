<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Task::create([
            'title' => 'Tarea de ejemplo',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'due_date' => '2023-01-01',
            'status' => 'pending',
            'user_id' => 1,
        ]);

        Task::create([
            'title' => 'Tarea de ejemplo 2',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'due_date' => '2025-01-01',
            'status' => 'in_progress',
            'user_id' => 1,
        ]);

        Task::create([
            'title' => 'Tarea de ejemplo 3',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'due_date' => '2025-09-01',
            'status' => 'completed',
            'user_id' => 2,
        ]);

        Task::create([
            'title' => 'Tarea de ejemplo 4',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'due_date' => '2025-05-01',
            'status' => 'pending',
            'user_id' => 2,
        ]);

        Task::create([
            'title' => 'Tarea de ejemplo 5',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'status' => 'in_progress',
            'user_id' => 3,
        ]);

        Task::create([
            'title' => 'Tarea de ejemplo 6',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus.',
            'status' => 'completed',
            'user_id' => 3,
        ]);
    }
}
