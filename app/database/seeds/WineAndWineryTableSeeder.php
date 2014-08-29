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

        try{

            $file = app_path() . '/rakuten_wine_data.csv';
            //$file = app_path() . '/testcsv.csv';
            $parser = \KzykHys\CsvParser\CsvParser::fromFile($file,array("encoding" => "UTF-8"));
            // dd ($parser);
            foreach ($parser as $column_wine) {
            // handles each record

                echo $c++ . "\n";                
                if(count($column_wine) == 29) {
                    if($column_wine[5] == 'NA' && $column_wine[6] !== 'NA') {
                        $column_wine[5] = $column_wine[6];
                    }
                    if($column_wine[9] == 'NA' && $column_wine[10] !== 'NA') {
                        $column_wine[9] = $column_wine[10];
                    }
                    for ($j = 0; $j < 29 ; $j++) { 
                        if($column_wine[$j] == 'NA') {
                            $column_wine[$j] = '';
                        }
                    }
                    if($column_wine[4] == '') {
                        $column_wine[4] = $column_wine[2]; 
                    }
                    $wine = array(
                        'name' => $column_wine[4],
                        'year' => $column_wine[9],
                        'winery_id' => $i,
                        'rakuten_id' => $column_wine[0],
                        'original_name' => $column_wine[1],
                        'original_name_2' => $column_wine[2],
                        'country_name' => $column_wine[5],
                        'image_url' => $column_wine[23],
                        'wine_flag' => '',
                        'imformation_image' => $column_wine[17] . ','. $column_wine[18] . ',' .  $column_wine[19]. ',' .  $column_wine[20]. ',' . $column_wine[21] . ',' . $column_wine[22],
                        'rakuten_url' => $column_wine[13],
                        'wine_unique_id' => $i . '_' . $column_wine[9],
                        'color' => $column_wine[8],
                        'average_price' => $column_wine[11],
                        'average_rate' => 0,
                        'rate_count' => 0,
                        'wine_type' => $column_wine[3],
                        'folder_code' => $column_wine[16],
                    );
                    
                    if ($wine['year'] == '' || $wine['year'] == 0) {
                        $wine['wine_unique_id'] = $i . '_' . $i;
                    }
                    Wine::create($wine);

                    $winery_data = array(
                        'brand_name' => $column_wine[12],
                        'country_id' => '',
                        'country_name' => $column_wine[6],
                        'year' => $column_wine[10],
                        'winery_url' => $column_wine[14],
                        'region' => $column_wine[7],
                        'description' => '',
                    );
                    Winery::create($winery_data);
                    $i++;
                } else {
                    $error = implode(",", $column_wine);
                    $file_error = app_path() . "/error.txt";
                    file_put_contents($file_error, $error."\n", FILE_APPEND | LOCK_EX);
                }
               
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        // die;
        // $data = fopen($file,'r');
        
        // while ($row = fgets($data)) {
        //     //$row is your line as a string
            
        //     //do something with it
        //     $data_wine[$c] = $row;
            
        //     if( $c < 10) {
                    
        //         $c ++;
        //     } else {
        //         break;

        //     } 
        // }


        //  Wine::create(array(
        //      'wine_id' => '1',
        //  	 'name' => 'Gnarly Head Authentic White',
        //      'year' => '2011',
        //      'winery_id' => '1',
        //      'image_url' => 'wines/1.jpg',
        //      'wine_unique_id' => '1_2011',
        //      'average_price' => '1200',
        //      'average_rate' => '3.0',
        //      'wine_type' => '2',
        //  ));

        //  Wine::create(array(
        //      'wine_id' => '2',
        //      'name' => 'Trimbach Riesling',
        //      'year' => '2010',
        //      'winery_id' => '2',
        //      'image_url' => 'wines/2.png',
        //      'wine_unique_id' => '2_2010',
        //      'average_price' => '1700',
        //      'average_rate' => '4.0',
        //      'wine_type' => '2',
        //  ));
        //  Wine::create(array(
        //      'wine_id' => '3',
        //      'name' => 'Gruner Love Featuring the Stadlmann Gruner Veltliner ',
        //      'year' => '2011',
        //      'winery_id' => '3',
        //      'image_url' => 'wines/3.png',
        //      'wine_unique_id' => '3_2011',
        //      'average_price' => '1600',
        //      'average_rate' => '2.5',
        //      'wine_type' => '1',
        //  ));
        //  Wine::create(array(
        //      'wine_id' => '4',
        //      'name' => 'Pine Ridge Chenin Blanc + Viognier ',
        //      'year' => '2013',
        //      'winery_id' => '4',
        //      'image_url' => 'wines/4.png',
        //      'wine_unique_id' => '4_2013',
        //      'average_price' => '1200',
        //      'average_rate' => '2.0',
        //      'wine_type' => '3',
        //  ));
        //  Wine::create(array(
        //      'wine_id' => '5',
        //      'name' => 'Frescobaldi Nipozzano Chianti Rufina Riserva',
        //      'year' => '2009',
        //      'winery_id' => '5',
        //      'image_url' => 'wines/5.png',
        //      'wine_unique_id' => '5_2009',
        //      'average_price' => '2200',
        //      'average_rate' => '4.5',
        //      'wine_type' => '1',
        //  ));
        // for ($i = 6; $i < 11; $i ++) {
        //     $data = array(
        //         'wine_id' => "$i",
        //         'name' => "Wine_$i",
        //         'year' => '2009',
        //         'winery_id' => "1",
        //         'wine_flag' => '',
        //         'image_url' =>  'wines/'.$i.'.png',
        //         'wine_unique_id' => $i . "_2009",
        //         'average_price' => '2200',
        //         'average_rate' => '4.5',
        //         'wine_type' => '1',
        //         'rate_count' => '3',
        //         );
        //     $winery_id = $data['winery_id'];
        //     $winery = Winery::where('id',$winery_id)->with('country')->first();
        //     if($winery) {
        //         $data['wine_flag'] = $winery->country->flag_url;
        //     } else {
        //         $data['wine_flag'] = '';
        //     }
        //     Wine::create($data);
        // }
//        Wine::create(array(
//        	'name' => 'カベルネ・ソーヴィニヨン',s
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/1',
//            'wine_unique_id' => '1_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'メルロー ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/2',
//            'wine_unique_id' => '2_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'カルメネール',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/3',
//            'wine_unique_id' => '3_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'シラー',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/4',
//            'wine_unique_id' => '4_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'ピノ・ノワール',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/5',
//            'wine_unique_id' => '5_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'ピノノワール・ロゼ ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/6',
//            'wine_unique_id' => '6_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => 'ロゼ',
//        ));
//        Wine::create(array(
//        	'name' => 'シャルドネ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/7',
//            'wine_unique_id' => '7_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ゲヴュルツトラミネール ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/8',
//            'wine_unique_id' => '8_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'リースリング ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/9',
//            'wine_unique_id' => '9_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ソーヴィニヨン・ブラン ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/10',
//            'wine_unique_id' => '10_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ヴィオニエ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/11',
//            'wine_unique_id' => '11_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));

    }
} 