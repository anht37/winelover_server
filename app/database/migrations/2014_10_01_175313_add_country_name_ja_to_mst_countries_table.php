<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryNameJaToMstCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mst_countries', function(Blueprint $table)
		{
			$table->string('country_name_ja')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mst_countries', function(Blueprint $table)
		{
			$table->dropColumn('country_name_ja');
		});
	}

}
