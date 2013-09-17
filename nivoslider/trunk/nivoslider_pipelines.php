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


?>
