<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_geoloc');

// Inclusion des entêtes nécessaires à la fois dans l'espace privé et l'espace public
function inc_gmap_script_init_dist()
{
    // Verifier qu'il n'y a pas double inclusion
	static $deja_insere = false;
	if ($deja_insere)
		return "";
	$deja_insere = true;

	// Vérifier que le plugin est actif
	if (!gmap_est_actif())
		return "";
	
	// Init du retour
	$out = "";
	
	// Récupérer l'API
	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	$script_init = charger_fonction("script_init", "mapimpl/".$api."/public");
	$out .= $script_init();

	// Inclure les scripts supplémentaires indépendants de l'implémentation
	$js_public = _DIR_PLUGIN_GMAP . 'javascript/gmap_public.js';
	$out .= '<script type="text/javascript" src="'.$js_public.'"></script>' . "\n";
	
	// Inclusion des urls de base du site
	// Il faut le faire après les scripts, et en utilisant l'objet MapWrapper, car, lorsque la compression des
	// scripts est activé, le .js concaténé est inclu tout au début...
	$out .= '<script type="text/javascript">'."\n".'//<![CDATA[';
	$out .= '
	SiteInfo.pluginRoot = "' . _DIR_PLUGIN_GMAP . '";';
	
	// Icone par défaut
	$out .= '
	SiteInfo.iconDef = '.gmap_definition_icone('system').';';
	
	// Patch d'un bug IE dont la source est inconnue...
	// (Lors du premier accès à document.namespaces, ça plante, sauf s'il a été utilisé auparavant...)
	$out .= '
// Il y a une erreur "undefined" sous IE, pour GoogleMaps et Yahoo, faire un appel précoce à document.namespaces semble règler le problème...
var IE8NamespaceHack = document.namespaces;';

	$out .= "\n".'//]]>'."\n".'</script>'."\n";
	return $out;
}

?>