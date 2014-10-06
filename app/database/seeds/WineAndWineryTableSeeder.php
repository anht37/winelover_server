<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class WineAndWineryTableSeeder extends Seeder {
    public function run() {
        DB::table('wines')->truncate();
        DB::table('wineries')->truncate();
        DB::connection()->disableQueryLog();
        set_time_limit (3600);
        $c = 0;
        $i = 1;
        $count = 1;
        $winery_id = 1;
        try{

            $file = app_path() . '/rakuten_wine_data_20140905.csv';
            //$file = app_path() . '/testcsv.csv';

            $i = 1;
            $country_name_on_wines = Wine::take(300)->get(array('country_name'));
            $country_name_ja_list = Country::get(array('country_name_ja'));
            foreach ($country_name_ja_list as $country_name) {
                if($country_name->country_name_ja != null) {
                    if($i == 1) {
                        $re = $country_name->country_name_ja; 
                        $i ++;
                    } else {
                        $re = $re . "|" . $country_name->country_name_ja;
                    }
                } 
            }
            $re_country_name = "'/".$re."/'";

            $parser = \KzykHys\CsvParser\CsvParser::fromFile($file,array("encoding" => "UTF-8"));
            foreach ($parser as $column_wine) {
                echo $c++ . "\n";

                if(count($column_wine) == 33) {

                    $validator = Validator::make(
                        array('rakuten_id' => $column_wine[0]),
                        array(
                            'rakuten_id' => 'exists:wines2,rakuten_id',
                        )
                    );

                    if($validator->passes()) {

                        if($column_wine[13] == 'NA' && $column_wine[14] !== 'NA') {
                            $column_wine[13] = $column_wine[14];
                        }
                        if($column_wine[9] == 'NA' && $column_wine[10] !== 'NA') {
                            $column_wine[9] = $column_wine[10];
                        }
                        for ($j = 0; $j < 33 ; $j++) { 
                            if($column_wine[$j] == 'NA') {
                                $column_wine[$j] = '';
                            }
                        }
                        if($column_wine[3] == '') {
                            $column_wine[3] = $column_wine[1]; 
                        }
                        if($column_wine[9] != null) {
                            $in_string = preg_match($re_country_name, $column_wine[9], $matches);
                            if($in_string == true) {
                                //$country = explode('・', $country_name->country_name, -1);
                                $country = Country::where('country_name_ja', $matches[0])->first();
                                if($country) {
                                    $flag = $country->flag_url;
                                } else {
                                    $flag = null;
                                }
                            }
                        } 

                        $winery = Winery::where('brand_name',$column_wine[16])->first();

                        if($winery == null) {
                            $winery_data = array(
                                'id' => $winery_id,
                                'brand_name' => $column_wine[16],
                                'country_id' => '',
                                'country_name' => $column_wine[10],
                                'year' => $column_wine[14],
                                'winery_url' => $column_wine[18],
                                'region' => $column_wine[11],
                                'description' => '',
                            );

                            Winery::create($winery_data);
                            $winery = $winery_id;
                            $winery_id ++;
                        } else {
                            $winery = $winery->id;
                        }

                        $wine = array(
                            'name' => $column_wine[3],
                            'name_en' => $column_wine[4],
                            'sub_name' => $column_wine[5],
                            'sub_name_en' => $column_wine[6],
                            'year' => $column_wine[13],
                            'winery_id' => $winery,
                            'rakuten_id' => $column_wine[0],
                            'original_name' => $column_wine[1],
                            'original_name_2' => $column_wine[2],
                            'country_name' => $column_wine[9],
                            'image_url' => $column_wine[27],
                            'wine_flag' => $flag,
                            'imformation_image' => $column_wine[21] . ','. $column_wine[22] . ',' .  $column_wine[23]. ',' .  $column_wine[24]. ',' . $column_wine[25] . ',' . $column_wine[26],
                            'rakuten_url' => $column_wine[17],
                            'wine_unique_id' => $i . '_' . $column_wine[13],
                            'color' => $column_wine[12],
                            'average_price' => $column_wine[15],
                            'average_rate' => 0,
                            'rate_count' => 0,
                            'wine_type' => $column_wine[7],
                            'folder_code' => $column_wine[20],
                        );
                        if ($wine['year'] == '' || $wine['year'] == 0) {
                            $wine['wine_unique_id'] = $i . '_' . $i;
                        }
                        Wine::create($wine);
                        $i++;
                    }
                } else {
                    $error = implode(",", $column_wine);
                    $file_error = app_path() . "/error.txt";
                    file_put_contents($file_error, $error."\n", FILE_APPEND | LOCK_EX);
                }
               
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
} 
// class WineAndWineryTableSeeder extends Seeder {
//     public function run() {
//         DB::table('wines')->truncate();
//         DB::table('wineries')->truncate();
//         DB::connection()->disableQueryLog();
//         set_time_limit (3600);
//         $c = 0;
//         $i = 1;
//         $winery_id = 1;
//         try{

//             $file = app_path() . '/rakuten_wine_data.csv';
//             $parser = \KzykHys\CsvParser\CsvParser::fromFile($file,array("encoding" => "UTF-8"));
//             foreach ($parser as $column_wine) {

//                 echo $c++ . "\n";                
//                 if(count($column_wine) == 29) {
//                     if($column_wine[5] == 'NA' && $column_wine[6] !== 'NA') {
//                         $column_wine[5] = $column_wine[6];
//                     }
//                     if($column_wine[9] == 'NA' && $column_wine[10] !== 'NA') {
//                         $column_wine[9] = $column_wine[10];
//                     }
//                     for ($j = 0; $j < 29 ; $j++) { 
//                         if($column_wine[$j] == 'NA') {
//                             $column_wine[$j] = '';
//                         }
//                     }
//                     if($column_wine[4] == '') {
//                         $column_wine[4] = $column_wine[2]; 
//                     }
//                     $winery = Winery::where('brand_name',$column_wine[12])->first();

//                     if($winery == null) {
//                         $winery_data = array(
//                             'id' => $winery_id,
//                             'brand_name' => $column_wine[12],
//                             'country_id' => '',
//                             'country_name' => $column_wine[6],
//                             'year' => $column_wine[10],
//                             'winery_url' => $column_wine[14],
//                             'region' => $column_wine[7],
//                             'description' => '',
//                         );

//                         Winery::create($winery_data);
//                         $winery = $winery_id;
//                         $winery_id ++;
//                     } else {
//                         $winery = $winery->id;
//                     }

//                     $wine = array(
//                         'name' => $column_wine[4],
//                         'year' => $column_wine[9],
//                         'winery_id' => $winery,
//                         'rakuten_id' => $column_wine[0],
//                         'original_name' => $column_wine[1],
//                         'original_name_2' => $column_wine[2],
//                         'country_name' => $column_wine[5],
//                         'image_url' => $column_wine[23],
//                         'wine_flag' => '',
//                         'imformation_image' => $column_wine[17] . ','. $column_wine[18] . ',' .  $column_wine[19]. ',' .  $column_wine[20]. ',' . $column_wine[21] . ',' . $column_wine[22],
//                         'rakuten_url' => $column_wine[13],
//                         'wine_unique_id' => $i . '_' . $column_wine[9],
//                         'color' => $column_wine[8],
//                         'average_price' => $column_wine[11],
//                         'average_rate' => 0,
//                         'rate_count' => 0,
//                         'wine_type' => $column_wine[3],
//                         'folder_code' => $column_wine[16],
//                     );
//                     if ($wine['year'] == '' || $wine['year'] == 0) {
//                         $wine['wine_unique_id'] = $i . '_' . $i;
//                     }
//                     Wine::create($wine);

                    
//                     $i++;
//                 } else {
//                     $error = implode(",", $column_wine);
//                     $file_error = app_path() . "/error.txt";
//                     file_put_contents($file_error, $error."\n", FILE_APPEND | LOCK_EX);
//                 }
               
//             }
//         } catch (Exception $e) {
//             echo 'Caught exception: ',  $e->getMessage(), "\n";
//         }
//     }
// } 