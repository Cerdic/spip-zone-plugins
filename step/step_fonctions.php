<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!function_exists('img_absolue')){
function img_absolue($img){
	$src = extraire_attribut($img,'src');
	$src = url_absolue($src);
	$img = inserer_attribut($img,'src',$src);
	return $img;
}
}

?>
