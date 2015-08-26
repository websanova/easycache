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

        \Websanova\EasyCache\Models\Comment::truncate();

        \DB::table('websanova_easycache_comments')->insert([
            'domain_id' => 1,
            'item_id' => 1,
            'slug' => 'comment 1',
            'title' => 'Comment One',
            'body' => 'This is comment one.',
            'status' => 'active',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_comments')->insert([
            'domain_id' => 1,
            'item_id' => 1,
            'slug' => 'comment 2',
            'title' => 'Comment Two',
            'body' => 'This is comment two.',
            'status' => 'active',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_comments')->insert([
            'domain_id' => 1,
            'item_id' => 1,
            'slug' => 'comment 3',
            'title' => 'Comment Three',
            'body' => 'This is comment three.',
            'status' => 'deleted',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('websanova_easycache_comments')->insert([
            'domain_id' => 1,
            'item_id' => 2,
            'slug' => 'comment 4',
            'title' => 'Comment Four',
            'body' => 'This is comment four.',
            'status' => 'active',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}