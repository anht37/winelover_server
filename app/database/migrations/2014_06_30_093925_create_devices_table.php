<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create('devices', function(Blueprint $table)
        {
            //
            $table->integer('id',true,true)->unique();
            $table->string('auth_key');
            $table->string('device_id')->unique();
            $table->integer('platform')->default(1);
            $table->integer('privacy')->default(0);
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
		//
        Schema::drop('devices');
	}

}
