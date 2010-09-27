<?php

function clean_fichier($fichier){
	$dirimg=_DIR_IMG;
if (preg_match(",^$dirimg([a-z]+/)?((.*)\.[a-z]+)$,", $fichier, $r)) {
	 return $r[2];
}
else {
 return $fichier;
}
}

?>
