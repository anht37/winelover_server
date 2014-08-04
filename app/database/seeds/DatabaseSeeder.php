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
		$this->call('FollowTableSeeder');
		$this->call('ProfileTableSeeder');
		$this->call('CountryTableSeeder');
		$this->call('WineryTableSeeder');
		$this->call('WineTableSeeder');
		$this->call('RatingTableSeeder');
		$this->call('LikeTableSeeder');
		$this->call('CommentTableSeeder');
		$this->call('WinenoteTableSeeder');
		$this->call('WishlistTableSeeder');
		//$this->call('LoginTableSeeder');

	}

}
