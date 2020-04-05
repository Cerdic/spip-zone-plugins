<?php
/*
 * Plugin COG
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_DIR_PLUGIN_COG'))
{
    $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
    define('_DIR_PLUGIN_COG',(_DIR_PLUGINS.end($p)));
}





function cog_affiche_milieu($flux){

$texte = "";
$e     = trouver_objet_exec($flux['args']['exec']);

if (
	$e !== false // page d'un objet éditorial
	AND $e['edition'] === false // pas en mode édition
	AND $id_objet=$flux['args'][$e['id_table_objet']]
	AND autoriser('ajoutercommune',$e['type'],$id_objet)
) {
	include_spip('inc/cog_boitier');
	$flux['data'] .= cog_boitier_cog($id_objet, $e['type']);

}

	return $flux;
}




function cog_rechercher_liste_des_champs($tables){
	  $tables['cog_commune']['nom'] = 3;
	  return $tables;
	}


function cog_declarer_tables_objets_surnoms($surnoms) {
$surnoms['cog_commune'] = 'cog_communes';
return $surnoms;
}


function cog_header_prive($texte){

$texte .= '
<script type="text/javascript">
var cog_url_ville=\''.generer_url_public('ville').'\'
</script>
<script src="'.find_in_path('javascript/cog_prive.js').'" type="text/javascript"></script>'."\n";

return $texte;
}



function cog_jqueryui_plugins($scripts){
	$scripts[] = "jquery.ui.autocomplete";
	return $scripts;
}



?>
