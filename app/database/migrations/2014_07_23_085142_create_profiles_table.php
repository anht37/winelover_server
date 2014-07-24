<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profiles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('user_id');
			$table->integer('follower_count');
			$table->integer('following_count');
			$table->integer('rate_count');
			$table->integer('comment_count');
			$table->integer('scan_count');
			$table->string('last_name');
			$table->string('first_name');
			$table->string('bio');
			$table->integer('country_id');
			$table->integer('pref_id');
			$table->string('alias');
			$table->string('image');
			$table->string('website');
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
		Schema::drop('profiles');
	}

}
