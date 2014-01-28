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
	  AND $p = stripos($texte,"</body>")
	){
		$js = find_in_path('javascript/jquery.nivo.slider.pack.js');
		lire_fichier(find_in_path('javascript/nivoslider.init.js'),$init);
		$ins = '<script type="text/javascript">/*<![CDATA[*/
jQuery.getScript("'.$js.'",function(){'.$init.'});/*]]>*/</script>';
		$texte = substr_replace($texte,$ins,$p,0);
	}
	return $texte;
}


/**
 * Mise en forme de la balise img : src direct ou gif transparent selon le cas
 * evite de charger toutes les images directement
 * @param string $img
 * @param int $compteur
 * @param bool|string $controlNavThumbs
 * @return string
 */
function nivoslider_img_display_first_only($img, $compteur, $controlNavThumbs){
	// charger l'image directement si c'est la premiere ou si on a active la navigation par vignette
	if ($compteur==1 OR $controlNavThumbs===true OR $controlNavThumbs==="true") return $img;

	$src = extraire_attribut($img,"src");
	$img = inserer_attribut($img,"data-src",$src);
	// gif transparent 1px
	// http://proger.i-forge.net/The_smallest_transparent_pixel/eBQ
	$img = inserer_attribut($img,"src","data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	return $img;
}

?>
