<?php

// action/spiplistes_listes_abonner_auteur.php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_afficher_auteurs');
include_spip('inc/spiplistes_api_presentation');

//CP-20080603
// principalement utilise par exec/spiplistes_liste_gerer.php
function action_spiplistes_listes_abonner_auteur_dist () {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = intval($securiser_action());
	$id_liste = intval(_request('id_liste'));
	$id_auteur = max(_request('select_abo'), _request('nouv_auteur'));
	$action = _request('action');
	$tri = urldecode(_request('tri'));
	$redirect = urldecode(_request('redirect'));

	$cherche_auteur = trim(urldecode(_request('cherche_auteur')));

	if($id_liste > 0) {
	
		$elligibles = null;
		$nb_elligibles = 0;

		spiplistes_log("tri: $tri ");

		$statut_liste = sql_getfetsel('statut', 'spip_listes', "id_liste=".sql_quote($id_liste), '', '', 1);
		
		if($id_auteur > 0) {
			spiplistes_abonnements_ajouter($id_auteur, $id_liste);
		}
	
		if(!empty($cherche_auteur) && in_array($statut_liste, explode(';', _SPIPLISTES_LISTES_STATUTS_OK))) {
			$sql_from = "spip_auteurs AS a";
			$sql_where = array(
				"a.nom LIKE '%$cherche_auteur%'"
				, "LENGTH(a.email)"
				, "(statut=".sql_quote('0minirezo')." OR statut=".sql_quote('1comite')
					// si pas une liste privée, complète le where
					. (($statut_liste != _SPIPLISTES_PRIVATE_LIST) ? " OR statut=".sql_quote('6forum') : "")
					. ")"
				, "NOT EXISTS (SELECT NULL FROM spip_auteurs_listes AS l WHERE l.id_auteur = a.id_auteur AND l.id_liste = ".sql_quote($id_liste).")"
				);
			/*
			 * la requete ci-dessus en + clair ;-)
			 *//*
			$sql_query = "SELECT id_auteur,nom,statut FROM spip_auteurs AS a
				WHERE nom LIKE '%$cherche_auteur%'
					AND LENGTH(a.email)
					AND (statut='0minirezo' OR statut='1comite' OR statut='6forum')
					AND NOT EXISTS (SELECT NULL FROM spip_auteurs_listes AS l WHERE l.id_auteur = a.id_auteur AND l.id_liste = $id_liste)";
			*/
			
			// demande la liste des elligibles
			$sql_result = sql_select("id_auteur,nom,statut", $sql_from, $sql_where, '', array('statut','nom'));
	
			if($sql_result) {
				$elligibles = array();
				while($row = spip_fetch_array($sql_result)) {
					if(!isset($elligibles[$row['statut']])) {
						$elligibles[$row['statut']] = array();
					}
					$elligibles[$row['statut']][$row['id_auteur']] = $row['nom'];
				}
			}
			else {
				spiplistes_log("DATABASE ERROR: [" . spip_sql_errno() . "] " . spip_sql_error());
			}
			$nb_elligibles = count($elligibles);
		}
		
		include_spip('inc/spiplistes_listes_selectionner_auteur');
		echo(spiplistes_listes_boite_abonnements($id_liste, $statut_liste, $tri, $debut, $redirect, $elligibles, $nb_elligibles));
	}
	exit(0);
}

?>