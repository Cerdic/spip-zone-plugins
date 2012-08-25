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
if(lire_config('cog/afficher_bloc_cog'))
{
	$tab_page_bloc=array('articles'=>'article','naviguer'=>'rubrique');
	if (in_array($flux['args']['exec'],array_keys($tab_page_bloc))){

		$objet=$tab_page_bloc[$flux['args']['exec']];

		$tab_rubriques_cog=lire_config("cog/rubriques_cog",array(0,-1));
		$id_objet = $flux['args']["id_".$objet];
		switch($objet)
		{
		case 'article':
			//on cherche la rubrique de l'article
			$id_rubrique=sql_getfetsel('id_rubrique','spip_articles','id_article='.$id_objet);
		break;
		case 'rubrique':
			$id_rubrique=$id_objet;
		break;
		}
		if (!$id_rubriques){
			if (!(in_array(-1,$tab_rubriques_cog) OR in_array($id_rubrique, $tab_rubriques_cog))) {
				return $flux;
			}
		include_spip('inc/prive');
		$flux['data'].= cog_boitier_cog($id_objet,$objet);
		}
	}
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



function cog_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.autocomplete";
	return $scripts;
}



?>
