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
			$table->string('rakuten_id')->nullable();
			$table->string('name');
			$table->string('original_name')->nullable();
			$table->string('original_name_2')->nullable();
			$table->string('country_name')->nullable();
			$table->integer('year')->nullable();
			$table->integer('winery_id');
			$table->string('wine_flag')->nullable();
			$table->string('image_url')->nullable();
			$table->string('imformation_image')->nullable();
			$table->string('wine_unique_id')->nullable();
			$table->string('color')->nullable();
			$table->float('average_price')->nullable();
			$table->float('average_rate')->nullable();
			$table->integer('wine_type')->default(6);
			$table->integer('rate_count')->default(0);
			$table->string('rakuten_url')->nullable();
			$table->integer('folder_code')->nullable();
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
