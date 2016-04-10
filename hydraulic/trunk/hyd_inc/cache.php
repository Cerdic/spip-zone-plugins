<?php

define('_HYD_CACHE_DIRECTORY', _NOM_TEMPORAIRES_INACCESSIBLES.'hydraulic/');
define('_HYD_CACHE_MAX_SIZE', 1024*1024);

function WriteCacheFile($file_name, $file_content) {
	if(!is_dir(_HYD_CACHE_DIRECTORY)) {
		mkdir(_HYD_CACHE_DIRECTORY);
	}
	if(is_dir(_HYD_CACHE_DIRECTORY)) {
		if(mt_rand(0,5)==0) {
			if(CacheSize()>_HYD_CACHE_MAX_SIZE) {
				CacheCleanAll();
			}
		}
		$file_name =_HYD_CACHE_DIRECTORY.$file_name;
		if($fichier_cache = fopen($file_name,'w')) {
			fwrite($fichier_cache,serialize($file_content));
			fclose($fichier_cache);
		}
	}
}


function ReadCacheFile($FileName) {
	$FileName = _HYD_CACHE_DIRECTORY.$FileName;
	$aRetour = @unserialize(file_get_contents($FileName));
	return $aRetour;
}

/**
 * Get the directory size
 * @param directory $directory
 * @return integer
 */
function CacheSize() {
	$directory=_HYD_CACHE_DIRECTORY;
	$size = 0;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
		$size+=$file->getSize();
	}
	return $size;
}


function CacheCleanAll() {
	$dp = opendir(_HYD_CACHE_DIRECTORY);
	while($file = readdir($dp)) {
		if($file !== '.' and $file != '..') {
			unlink(_HYD_CACHE_DIRECTORY."/".$file);
		}
	}
}

function format_nombre($nombre,$dec) {
	if($nombre === false) {
		return _T('hydraulic:non_calcule');
	} else {
		if($nombre=='') $nombre=0;
		return number_format($nombre, $dec, '.', ' ');
	}
}

?>