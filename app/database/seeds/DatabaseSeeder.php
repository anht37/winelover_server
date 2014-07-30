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
		$this->call('LikeTableSeeder');
		$this->call('CommentTableSeeder');
		$this->call('FollowTableSeeder');
		$this->call('ProfileTableSeeder');
		$this->call('CountryTableSeeder');

	}

}
