<?php

include_spip('inc/filtres');

// dans de core en 2.1 !
// et aussi dans Bonux pour 2.0
if (!function_exists('balise_img')){
/**
 * une fonction pour generer une balise img a partir d'un nom de fichier
 *
 * @param string $img
 * @param string $alt
 * @param string $class
 * @return string
 */
function balise_img($img,$alt="",$class=""){
	$taille = taille_image($img);
	list($hauteur,$largeur) = $taille;
	if (!$hauteur OR !$largeur)
		return "";
	return
	"<img src='$img' width='$largeur' height='$hauteur'"
	  ." alt='".attribut_html($alt)."'"
	  .($class?" class='".attribut_html($class)."'":'')
	  .' />';
}
}

if (!function_exists('img_absolue')){
function img_absolue($img){
	$src = extraire_attribut($img,'src');
	$src = url_absolue($src);
	$img = inserer_attribut($img,'src',$src);
	return $img;
}
}

?>
