<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->call('WineTableSeeder');
		$this->call('WineryTableSeeder');
		$this->call('RatingTableSeeder');

	}

}
