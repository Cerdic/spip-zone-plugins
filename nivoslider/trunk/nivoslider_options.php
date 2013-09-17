<?php
/**
 * Plugin NivoSlider pour Spip 3.0
 * Licence GPL (c)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function nivoslider_insert_head_css($flux){
	include_spip('inc/filtres');
	$css = produire_fond_statique("css/nivoslider.css");
	$flux .= '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />';
	return $flux;
}

function nivoslider_affichage_final($texte){
	if (($GLOBALS['html'] OR test_espace_prive())
	  AND strpos($texte,"nivoSlider")!==false
		AND stripos($texte,"</script>")
	  AND $p = stripos($texte,"</head>")
	){
		$ins = '<script src="'.find_in_path('js/jquery.nivo.slider.pack.js').'" type="text/javascript"></script>';
		$texte = substr_replace($texte,$ins,$p,0);
	}
	return $texte;
}


function nivoslider_img_display_first_only($img, $compteur){
	if ($compteur==1) return $img;

	$src = extraire_attribut($img,"src");
	$img = inserer_attribut($img,"data-src",$src);
	// gif transparent 1px
	// http://proger.i-forge.net/The_smallest_transparent_pixel/eBQ
	$img = inserer_attribut($img,"src","data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	return $img;
}

?>
