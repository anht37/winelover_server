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
                'follower_count' => $i + '3',
                'following_count' => $i + '5',
                'rate_count' => $i + '2',
                'comment_count' => $i + '3',
                'scan_count' => $i + '1',
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