<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('action/editer_liens');

/**
 * @param $objet
 * @param $id_objet
 *
 * @return
 */
function formulaires_editer_seoptimizr_charger_dist($objet, $id_objet) {

	// on a le type d'objet et l'id_objet (par exemple "article" et "1" ) ... cherchons l'id_seobjet associé
	$id_seobjet = sql_getfetsel('id_seobjet', 'spip_seobjets_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));

	if ($id_seobjet) {
		// on a un SEObjet sur cet objet
		// on utilise la fonctions formulaires_editer_objet_charger
		// formulaires_editer_objet_charger($type, $id='new', $id_parent=0, $lier_trad=0, $retour='', $config_fonc='articles_edit_config', $row=array(), $hidden=''){
		$valeurs = formulaires_editer_objet_charger('seobjet', $id_seobjet, 0, '', $retour, $config_fonc, $row, $hidden);
	} else {
		// on initialise le tableau à vide ou avec les valeurs par défaut
		$valeurs = array(
			'url_redir' => '',
			'meta_robots' => '',
			'logo_alt' => '',
		);
	}

	return $valeurs;
}

/**
 * @return
 */
function formulaires_editer_seoptimizr_verifier_dist($objet, $id_objet) {
	$retour = array();
	// on va dire que tout est ok ... à la limite tester sur le champs keywords
	// le nombre maxi de cracteres et la présence des virgules
	// $retour['message_erreur'] = "Boom" ;
	return $retour;
}

/**
 * @param $objet
 * @param $id_objet
 *
 * @return array
 */
function formulaires_editer_seoptimizr_traiter_dist($objet, $id_objet) {
	$res = array();

	// on a le type d'objet et l'id_objet (par exemple "article" et "1" ) ... cherchons l'id_seobjet associé
	// oui c'est le même commentaire que dans la fonction charger mais c'est normal, on est obligé de
	// refaire cette requête pour deux raisons : 1/ on ne peux pas passer de variable entre charger vérifier et traiter
	// 2: il peut se passer plusieurs minutes entre cahrger et traiter et le résultat n'est ptet plus le même
	$id_seobjet = sql_getfetsel('id_seobjet', 'spip_seobjets_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));

	if ($id_seobjet) {
		// on est donc dans le cas d'un update

		// on utilise la fonction sql sql_updateq http://programmer3.spip.org/sql_insertq,590
		// le nom de la table comprends toujours "spip_" c'est spip qui se débrouille après si le prefix est différent

		$resultat_requete = sql_updateq(
			'spip_seobjets',
			array(
				 'url_redir' => _request('url_redir'),
				 'meta_robots' => _request('meta_robots'),
				 'logo_alt' => _request('logo_alt'),
				 'maj' => date('Y-m-d H:i:s'),
			),
			'id_seobjet='.intval($id_seobjet)
		);
		if ($resultat_requete) {
			$res['message_ok'] = $res['message_ok'].'Merci vos infos sont à jour';
		} else {
			$res['message_erreur'] = $res['message_erreur'].'Problème lors de la mise à jour';
		}
	} else {
		$resultat_requete = sql_insertq(
			'spip_seobjets',
			array(
				 'url_redir' => _request('url_redir'),
				 'meta_robots' => _request('meta_robots'),
				 'logo_alt' => _request('logo_alt'),
				 'maj' => date('Y-m-d H:i:s'),
			)
		);
		if ($resultat_requete) {
			$res['message_ok'] = $res['message_ok'].'infos enregistrées';

			// cette fonction permet de faire la liaison en spip_seobjets et spip_seobjets_lien
			objet_associer(
				array('seobjet' => $resultat_requete),
				array("$objet" => $id_objet)
			);
		} else {
			$res['message_erreur'] = $res['message_erreur']."Problème lors de l'insert  mise à jour";
		}
	}
	$res['editable'] = true;

	return $res;
}
