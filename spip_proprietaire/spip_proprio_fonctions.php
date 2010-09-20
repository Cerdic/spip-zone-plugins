<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Délimiteurs pour découpage, notamment des contacts
 */
global $spip_proprio_usual_delimiters;
$spip_proprio_usual_delimiters = array(" ", "-", "_", "/", ".","#","\\","@");

/**
 * @param string $str La chaîne à analyser
 * @return boolean/string Le délimiteur trouvé (en plus grand nombre), FALSE sinon
 */
function spip_proprio_usual_delimiters($str){
	global $spip_proprio_usual_delimiters;
	$delim = false;
	foreach($spip_proprio_usual_delimiters as $delimiter) {
		if(strpos($str, $delimiter)) $delim = $delimiter;
	}
	return $delim;
}

/**
 * fonction qui transforme les noms de fichiers
 * @todo decouper le nom du fichier pour enlever l'extension avant traitement, puis la remettre avant retour
 */
function spip_proprio_formater_nom_fichier($string, $spacer='_') {
	$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[^a-zA-Z0-9]@');
	$replace = array ('e','a','i','u','o','c',' ');
	$string =  preg_replace($search, $replace, $string);
	$string = strtolower($string);
	$string = str_replace(" ",$spacer,$string);
	$string = preg_replace('#\-+#',$spacer,$string);
	$string = preg_replace('#([-]+)#',$spacer,$string);
	trim($string,$spacer);
	return $string;
}

function spip_proprio_recuperer_extension($str){
	return( substr(strrchr($str, '.'), 1) );
}

function spip_proprio_formater_telephone($str){
	return str_replace(array('(+33)',' ','.'), '', $str);
}

/**
 * Fonction mettant une apostrophe si nécessaire
 * Cette fonction ne traite pas les cas particuliers (nombreux ...) ni les 'h' muet
 */
function apostrophe($str='', $article='', $exception=false){
	$voyelles = array('a', 'e', 'i', 'o', 'u');
	$article = trim($article);

	$str_deb = substr(spip_proprio_formater_nom_fichier($str), 0, 1);
	$article_fin = substr($article, -1, 1);

	if(in_array($str_deb, $voyelles) OR $exception)
		return( substr($article, 0, strlen($article)-1)."'".$str );
	return $article.' '.$str;
}

function modifier_guillemets($str){
	return( str_replace("'", '"', $str) );
}

// ----------------------
// FILTRE GENERATEUR D'IMAGE
// ----------------------

// Avec l'aide inestimable de Paris-Bayrouth (http://www.paris-beyrouth.org/)
function spip_proprio_image_alpha($img, $alpha='', $src=false){
	if(!$alpha OR !strlen($alpha) OR $alpha == '0') return $img;
	include_spip("inc/filtres_images");
	$image = _image_valeurs_trans($img, "one","png");
//var_export($image);
	$img = image_alpha($img, $alpha);
	if($src) return( extraire_attribut($img, 'src') );
	return($img);
}

?>