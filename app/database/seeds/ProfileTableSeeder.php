<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class ProfileTableSeeder extends Seeder {
    public function run() {
        DB::table('profiles')->delete();
        $users = User::all();
        $i = 1;
        foreach ($users as $user) {
            Profile::create(array(
                'id' => $i,
                'user_id' => $user->user_id,
                'follower_count' => 0,
                'following_count' => 0,
                'rate_count' => 0,
                'comment_count' => 0,
                'scan_count' => 0,
                'country_id' => $i,
                'image' => 'wines/'. $i .'.png',
                'bio' => 'this is profile of user' . $i,
                'last_name' => 'pro' . $i,
                'first_name' => 'user',
            ));
            $i ++;
        }
    }
}