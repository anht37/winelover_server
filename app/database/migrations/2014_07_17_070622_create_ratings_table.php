<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ratings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('user_id');
			$table->string('wine_unique_id');
			$table->float('rate')->default(0);
			$table->text('content')->nullable();
			$table->integer('like_count')->default(0);
			$table->integer('comment_count')->default(0);
			$table->tinyInteger('is_my_wine')->default(1);
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
		Schema::drop('ratings');
	}

}