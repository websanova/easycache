<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsanovaEasyCacheDomainsTable extends Migration
{
	public function up()
	{
		Schema::create('websanova_easycache_domains', function(Blueprint $t)
		{
			$t->increments('id')->unsigned();
			$t->text('slug', 255);
			$t->text('name', 255);
			$t->integer('items_total')->unsigned()->default(0);
			$t->integer('comments_total')->unsigned()->default(0);
		});
	}

	public function down()
	{
		Schema::drop('websanova_easycache_domains');
	}
}
