<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_grappe_charger_dist($id_grappe='new',$retour='', $config_fonc='grappes_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('grappe',$id_grappe,'','',$retour,$config_fonc,$row,$hidden);

	$valeurs['liaisons'] = explode(',',$valeurs['liaisons']);
	$valeurs['options'] = @unserialize($valeurs['options']);
	$valeurs['acces'] = is_array($a = $valeurs['options']['acces']) ? $a : array();

	// par defaut a la creation de groupe
	if (!intval($id_grappe)) {
		$valeurs['liaisons'] = array(); //array('auteurs');
	}

	return $valeurs;
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@articles_edit_config
function grappes_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['afficher_barre'] = true;
	$config['langue'] = $spip_lang;
	return $config;
}

function formulaires_editer_grappe_verifier_dist($id_grappe='new',$retour='', $config_fonc='grappes_edit_config', $row=array(), $hidden=''){
	// le id 0 est voulu, on ne souhaite pas controler le contenu des champs qui postent un array !
	return formulaires_editer_objet_verifier('grappe',0,array('titre'));
}

// http://doc.spip.org/@inc_editer_groupe_mot_dist
function formulaires_editer_grappe_traiter_dist($id_grappe='new',$retour='', $config_fonc='grappes_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('grappe',$id_grappe,'','',$retour,$config_fonc,$row,$hidden);
}


?>
