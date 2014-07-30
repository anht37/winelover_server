<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class CountryTableSeeder extends Seeder {
    public function run() {
        DB::table('mst_countries')->delete();

		$file_list = array();
		$file_folder = public_path() . "/flags/";

		//use the directory class
		$files = dir($file_folder);
		//read all files ;from the  directory
		  chdir($file_folder);
		  $file_names = glob('*.png');
		  foreach ($file_names as $file_name) {
		  	$country_name = explode( '.', $file_name,-1);

		    $file_list[] = array(
		    	"country_name" => $country_name[0],
		    	"flag_url" => "flags/".$file_name,
		    );
		   } 

		closedir($files->handle);
		foreach ($file_list as $file) {
			Country::create($file);
		}
        
    }
}
