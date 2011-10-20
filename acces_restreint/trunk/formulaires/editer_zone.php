<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_zone_charger_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('zone',$id_zone,0,0,$retour,$config_fonc,$row,$hidden);
	include_spip('inc/accesrestreint');
	// charger les rubriques associees a la zone
	if ($id_zone = intval($id_zone)) {
		$valeurs['rubriques'] = accesrestreint_liste_contenu_zone_rub_direct($id_zone);
	} {
		// cas d'une creation, regardons si l'url propose deja des rubriques
		if ($r = _request('rubriques')) {
			if (is_numeric($r)) {
				$r = array($r);
			}
			if (is_array($r)) {
				$valeurs['rubriques'] = $r;
			}
		}
	}
	
	return $valeurs;
}

function zones_edit_config(){
	return array();
}

function formulaires_editer_zone_verifier_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('zone',$id_zone,array('titre'));

	return $erreurs;
}

function formulaires_editer_zone_traiter_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){
	if (_request('publique')!=='oui')
		set_request('publique','non');
	if (_request('privee')!=='oui')
		set_request('privee','non');

	$res = formulaires_editer_objet_traiter('zone',$id_zone,0,0,$retour,$config_fonc,$row,$hidden);

	if ($retour AND $res['id_zone']) {
		$res['redirect'] = parametre_url($retour,'id_zone',$res['id_zone']);
	}
	return $res;
}

?>
