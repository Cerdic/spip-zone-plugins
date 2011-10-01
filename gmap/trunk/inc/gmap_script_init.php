<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Inclusion des ent�tes n�cessaires � la fois dans l'espace priv� et l'espace public
function inc_gmap_script_init_dist()
{
    // Verifier qu'il n'y a pas double inclusion
	static $deja_insere = false;
	if ($deja_insere)
		return "";
	$deja_insere = true;

	// V�rifier que le plugin est actif
	if (!gmap_est_actif())
		return "";
	
	// Init du retour
	$out = "";
	
	// R�cup�rer l'API
	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	$script_init = charger_fonction("script_init", "mapimpl/".$api."/public");
	$out .= $script_init();

	// Inclure les scripts suppl�mentaires ind�pendants de l'impl�mentation
	$js_public = _DIR_PLUGIN_GMAP . 'javascript/gmap_public.js';
	$out .= '<script type="text/javascript" src="'.$js_public.'"></script>' . "\n";
	
	// Inclusion des urls de base du site
	// Il faut le faire apr�s les scripts, et en utilisant l'objet MapWrapper, car, lorsque la compression des
	// scripts est activ�, le .js concat�n� est inclu tout au d�but...
	$out .= '<script type="text/javascript">'."\n".'//<![CDATA[';
	$out .= '
	SiteInfo.pluginRoot = "' . _DIR_PLUGIN_GMAP . '";
	SiteInfo.pluginGmapImages = SiteInfo.pluginRoot + "images/";';
	$imageFile = _DIR_PLUGIN_GMAP . "images/marker.png";
	$imageInfo = @getimagesize($imageFile);
	$imageWidth = $imageInfo[0] ? $imageInfo[0] : 32;
	$imageHeight = $imageInfo[1] ? $imageInfo[1] : 32;
	$out .= '
	SiteInfo.defaultIcon = "' . $imageFile . '";
	SiteInfo.defaultIconWidth = '.$imageWidth.';
	SiteInfo.defaultIconHeight = '.$imageHeight.';';
	$imageFile = _DIR_PLUGIN_GMAP . "images/shadow.png";
	$imageInfo = @getimagesize($imageFile);
	$imageWidth = $imageInfo[0] ? $imageInfo[0] : 32;
	$imageHeight = $imageInfo[1] ? $imageInfo[1] : 32;
	$out .= '
	SiteInfo.defaultShadow = "' . $imageFile . '";
	SiteInfo.defaultShadowWidth = '.$imageWidth.';
	SiteInfo.defaultShadowHeight = '.$imageHeight.';'."\n";
	$out .= '//]]>'."\n".'</script>'."\n";

	return $out;
}

?>