<?php
/**
 * Plugin Guestbook
 * (c) 2013 Yohann Prigent (potter64), Stephane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_guestreponse_identifier_dist($id_guestreponse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_guestreponse), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_guestreponse_charger_dist($id_guestreponse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('guestreponse',$id_guestreponse,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	$valeurs['id_guestmessage']=_request('id_guestmessage');
	$valeurs['id_auteur']=  session_get('id_auteur');
  $valeurs['date']=  date( 'Y-m-d');
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_guestreponse_verifier_dist($id_guestreponse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('guestreponse',$id_guestreponse);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_guestreponse_traiter_dist($id_guestreponse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('guestreponse',$id_guestreponse,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_guestreponse = $res['id_guestreponse']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('guestreponse' => $id_guestreponse), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_guestreponse, '&');
			}
		}
	}
	return $res;

}


?>