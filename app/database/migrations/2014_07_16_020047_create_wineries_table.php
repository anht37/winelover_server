<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWineriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wineries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('brand_name');
			$table->integer('year')->nullable();
			$table->integer('country_id')->nullable();
			$table->string('country_name')->nullable();
			$table->string('region')->nullable();
			$table->text('description')->nullable();
			$table->string('winery_url')->nullable();
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
		Schema::drop('wineries');
	}

}
