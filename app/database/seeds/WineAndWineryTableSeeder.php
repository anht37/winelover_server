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
            $parser = \KzykHys\CsvParser\CsvParser::fromFile($file,array("encoding" => "UTF-8"));
            foreach ($parser as $column_wine) {

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
    }
} 