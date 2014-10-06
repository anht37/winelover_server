<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/09/2014
 * Time: 07:41
 */

class Wine2TableSeeder extends Seeder {
    public function run() {
        $count = Wine::where('group_id',0)->count();
        echo "Operating wines number : $count wines".PHP_EOL;
        Wine::where('group_id', 0)->chunk(100, function($wines) {
            foreach($wines as $wine) {
                echo "Handling: $wine->rakuten_id";
                $start = time();
                $request = curl_init('http://54.64.177.240:8888/api/match');

                curl_setopt($request, CURLOPT_POST, true);
                curl_setopt(
                    $request,
                    CURLOPT_POSTFIELDS,
                    array(
                        //'img' => new CurlFile($wine->local_path),
                        'img' => "@".$wine->local_path
                    ));

                curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                $result =  json_decode(curl_exec($request));
                $matched_wines = $result->matches;
                $matched_rakuten_ids = array();
                foreach ($matched_wines as $matched_wine) {
                    $matched_id = $matched_wine->id;
                    $num_of_match = $matched_wine->numMatches;
                    if($num_of_match < 10) continue;
                    $rakuten_paths = explode('_',$matched_id);
                    if(count($rakuten_paths) < 2) continue;
                    $matched_rakuten_ids[] = join('_',array('rakuten', $rakuten_paths[0], $rakuten_paths[1]));
                }
                if(count($matched_rakuten_ids) == 0) {
                    echo "No Matched Wines to group".PHP_EOL;
                    continue;
                }
                echo "Num of matched wines : ".count($matched_rakuten_ids).". Now updating group".PHP_EOL;
                $group_wines = Wine::where('rakuten_id', $matched_rakuten_ids)->get();
                $group_id = 0;
                foreach($group_wines as $group_wine) {
                    if($group_wine->group_id > 0) {
                        $group_id = $group_wine->group_id;
                        break;
                    }
                }

                if($group_id == 0) {
                    $group_id = $wine->wine_id;
                }
                $wine->group_id = $group_id;
                $wine->save();
                foreach($group_wines as $group_wine) {
                    $group_wine->group_id = $group_id;
                    $group_wine->save();
                }
                echo "Grouping Done. Group id is $group_id".PHP_EOL;
                echo "Cost : ".(time() - $start)." seconds.".PHP_EOL;
                curl_close($request);
            }
        });
    }
} 