<?php

function perzo_insert_head($flux){
		$flux .= "\r\n".'<link rel="stylesheet" href="'.find_in_path('perso.css').'" type="text/css" media="all" />'."\r\n";
	  return $flux;
}

?>