<?php

namespace Websanova\EasyCache\Seeds;

use Illuminate\Database\Seeder;

class EasyCacheSeeder extends Seeder
{
    public function run()
    {
        \Websanova\EasyCache\Models\Domain::truncate();

        \DB::table('websanova_easycache_domains')->insert([
            'slug' => 'en',
            'name' => 'English',
        ]);

        \DB::table('websanova_easycache_domains')->insert([
            'slug' => 'fr',
            'name' => 'French',
        ]);

        \Websanova\EasyCache\Models\Item::truncate();

        \DB::table('websanova_easycache_items')->insert([
            'domain_id' => 1,
        	'slug' => 'one',
        	'name' => 'One',
        	'description' => 'Item One.',
            'status' => 'active',
        	'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_items')->insert([
            'domain_id' => 1,
            'slug' => 'two',
            'name' => 'Two',
            'description' => 'Item Two.',
            'status' => 'active',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_items')->insert([
            'domain_id' => 1,
            'slug' => 'three',
            'name' => 'Three',
            'description' => 'Item Three.',
            'status' => 'deleted',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_items')->insert([
            'domain_id' => 2,
            'slug' => 'four',
            'name' => 'Four',
            'description' => 'Item Four.',
            'status' => 'active',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_items')->insert([
            'domain_id' => 2,
            'slug' => 'five',
            'name' => 'Five',
            'description' => 'Item Five.',
            'status' => 'deleted',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}