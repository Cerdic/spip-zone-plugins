<?php
/**
 * Terraeco Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2013 - Distribué sous licence GNU/GPL
 *
 * Formulaire d'édition de jeux de données
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_infographies_data_charger_dist($id_infographies_data='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_datas_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('infographies_data',$id_infographies_data,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_infographies_data_identifier_dist($id_infographies_data='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_datas_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_infographies_data),$associer_objet));
}

// Choix par defaut des options de presentation
function infographies_datas_edit_config($row){
	return array();
}

function formulaires_editer_infographies_data_verifier_dist($id_infographies_data='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_datas_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('infographies_data',$id_infographies_data,array('titre'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_infographies_data_dist
function formulaires_editer_infographies_data_traiter_dist($id_infographies_data='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_datas_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('infographies_data',$id_infographies_data,0,$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien auteur a prendre en compte ?
	if ($associer_objet AND $id_infographies_data=$res['id_infographies_data']){
		$objet = '';
		if(preg_match(',^\w+\|[0-9]+$,',$associer_objet)){
			list($objet,$id_objet) = explode('|',$associer_objet);
		}
		if ($objet AND $id_objet AND autoriser('modifier',$objet,$id_objet)){
			include_spip('action/editer_infographies_data');
			infographies_data_associer($id_infographies_data, array($objet => $id_objet));
			if (isset($res['redirect']))
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_infographies_data, '&');
		}
	}

	return $res;
}

?>
