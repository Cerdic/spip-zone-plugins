<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_album_identifier_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_album), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_album_charger_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('album',$id_album,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Si on cree l'album sur un objet, on le publie et eventuellement titre par defaut
	if ($associer_objet){
		// Statut
		$valeurs['statut'] = 'publie';
		// titre ?
		include_spip('inc/config');
		if (lire_config('albums/utiliser_titre_defaut') == 'on'
		AND list($objet, $id_objet) = explode('|', $associer_objet)){
			$valeurs['titre_defaut'] = generer_info_entite($id_objet, $objet, 'titre');
		}
	}

	return $valeurs;

}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_album_verifier_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('album',$id_album,intval($id_album)?array('titre'):array());
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_album_traiter_dist($id_album='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('album',$id_album,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// peupler le titre a posteriori si vide lors de la creation (creation rapide d'un album)
	if (!intval($id_album='new') AND !_request('titre') AND $res['id_album']){
		objet_modifier("album",$res['id_album'],array('titre' => _T('album:info_nouvel_album')." "._T('info_numero_abbreviation').$res['id_album']));
	}

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_album = $res['id_album']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('album' => $id_album), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_album', '', '&');
			}
		}

	}
	return $res;

}


?>
