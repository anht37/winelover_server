<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class CountryTableSeeder extends Seeder {
    public function run() {
        DB::table('mst_countries')->truncate();

		$country_name_full = array();
        $country_name_file = app_path() . "/country_name_new.txt";
        $myfile_country_name = fopen($country_name_file, "r") or die("Unable to open file!");
        $read_country_name_file = fread($myfile_country_name,filesize($country_name_file));
        $array_country_names = explode("\n", $read_country_name_file);
        foreach ($array_country_names as $key_country_names) {
        	if($key_country_names != null) {
        		$country_name_list = explode(" ", $key_country_names);
        		$country_name_full[$country_name_list[0]] = $country_name_list[1];
        	} 
        }

        $country_name_ja = "";
		$file_folder = public_path() . "/flags/";
		//use the directory class
		$files = dir($file_folder);
		//read all files ;from the  directory
		chdir($file_folder);
		$file_names = glob('*.png');
		$i = 1;
		//$c = 1;
		foreach ($file_names as $file_name) {
		  	$country_name_en = explode( '.', $file_name,-1);
		 	if(!empty($country_name_full[$country_name_en[0]])) {
		 		$country_name_ja = $country_name_full[$country_name_en[0]];
		 	}
		    $country = array(
		    	"country_name" => $country_name_en[0],
		    	"flag_url" => "flags/".$file_name,
		    	"country_name_ja" => $country_name_ja,
		    );
		    
		    Country::create($country);
		    $i ++;
		    
		    echo $i . "\n";
		} 
		
		closedir($files->handle);
        
    }
}
