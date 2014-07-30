<?php 
$files = dir("/Applications/MAMP/htdocs/winelovers-server/public/flags");
$i = 0;
while($file = $files->read()){
  $i++;
   echo $file."\n";
}

echo $i;
