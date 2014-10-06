<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToWinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('wines', function(Blueprint $table)
		{
			$table->string('name_en')->nullable();
			$table->string('sub_name')->nullable();
			$table->string('sub_name_en')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('wines', function(Blueprint $table)
		{
			$table->dropColumn('name_en');
			$table->dropColumn('sub_name');
			$table->dropColumn('sub_name_en');
		});
	}

}
