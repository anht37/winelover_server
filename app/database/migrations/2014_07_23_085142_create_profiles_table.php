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
			$table->integer('follower_count')->default(0);
			$table->integer('following_count')->default(0);
			$table->integer('rate_count')->default(0);
			$table->integer('comment_count')->default(0);
			$table->integer('scan_count')->default(0);
			$table->string('last_name')->nullable();
			$table->string('first_name')->nullable();
			$table->string('bio')->nullable();
			$table->integer('country_id')->nullable();
			$table->integer('pref_id')->nullable();
			$table->string('alias')->nullable();
			$table->string('image')->nullable();
			$table->string('website')->nullable();
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
