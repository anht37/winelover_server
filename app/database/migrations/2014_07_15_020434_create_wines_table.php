<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wines', function(Blueprint $table)
		{
			$table->increments('wine_id');
			$table->string('name');
			$table->integer('year');
			$table->integer('winery_id');
			$table->string('image_url')->nullable();
			$table->string('wine_unique_id');
			$table->float('average_price')->nullable();
			$table->float('average_rate')->nullable();
			$table->integer('wine_type')->default(3);
			$table->integer('rate_count')->->default(0);
			$table->softDeletes();
			$table->timestamps();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wines');
	}

}
