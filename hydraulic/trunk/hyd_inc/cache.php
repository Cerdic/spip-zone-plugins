<?php

define(HYD_CACHE_DIRECTORY, _NOM_TEMPORAIRES_INACCESSIBLES.'hydraulic/');
define(HYD_CACHE_MAX_SIZE, 1024*1024);

function WriteCacheFile($file_name, $file_content) {
   if(!is_dir(HYD_CACHE_DIRECTORY)) {
      mkdir(HYD_CACHE_DIRECTORY);
   }
   if(is_dir(HYD_CACHE_DIRECTORY)) {
      if(mt_rand(0,5)==0) {
         if(CacheSize()>HYD_CACHE_MAX_SIZE) {
            CacheCleanAll();
         }
      }
      $file_name =HYD_CACHE_DIRECTORY.$file_name;
      if($fichier_cache = fopen($file_name,'w')) {
         fwrite($fichier_cache,serialize($file_content));
         fclose($fichier_cache);
      }
   }
}


function ReadCacheFile($FileName) {
   $FileName = HYD_CACHE_DIRECTORY.$FileName;
   $aRetour = @unserialize(file_get_contents($FileName));
   return $aRetour;
}

/**
 * Get the directory size
 * @param directory $directory
 * @return integer
 */
function CacheSize() {
   $directory=HYD_CACHE_DIRECTORY;
    $size = 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
        $size+=$file->getSize();
    }
    return $size;
}


function CacheCleanAll() {
   $dp = opendir(HYD_CACHE_DIRECTORY);
   while($file = readdir($dp)) {
      if($file !== '.' and $file != '..') {
         unlink(HYD_CACHE_DIRECTORY."/".$file);
      }
   }
}

function format_nombre($nombre,$dec) {
   return number_format($nombre, $dec, ',', ' ');
}

?>
