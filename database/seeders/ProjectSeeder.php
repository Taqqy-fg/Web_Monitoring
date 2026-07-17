<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {

        Project::create([

            'name'=>'Google',

            'company'=>'Google',

            'base_url'=>'https://google.com',

            'monitor_url'=>'https://google.com',

            'description'=>'Search Engine',

            'interval_minutes'=>60,

            'is_active'=>1

        ]);

        Project::create([

            'name'=>'Github',

            'company'=>'Github',

            'base_url'=>'https://github.com',

            'monitor_url'=>'https://github.com',

            'description'=>'Repository',

            'interval_minutes'=>60,

            'is_active'=>1

        ]);

        Project::create([

            'name'=>'OpenAI',

            'company'=>'OpenAI',

            'base_url'=>'https://openai.com',

            'monitor_url'=>'https://openai.com',

            'description'=>'AI Platform',

            'interval_minutes'=>60,

            'is_active'=>1

        ]);

    }
}