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
			$table->string('image_url');
			$table->string('wine_unique_id');
			$table->float('average_price');
			$table->float('average_rate');
			$table->integer('wine_type')->default(3);
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
