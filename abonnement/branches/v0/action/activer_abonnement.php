<?php

/**
 * Plugin Abonnement pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');


function action_activer_abonnement_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	// id_abonnement-id_auteur
	$args = explode('-',$args);
	

	if (count($args)!=2) {
		spip_log("action_activer_abonnement_dist  pas compris");
		die("action_activer_abonnement_dist pas compris");
	}
	
	return abo_traiter_activer_abonnement(intval($args[0]), intval($args[1]));
	
}

function abo_traiter_activer_abonnement_hash($hash) {
	return abo_traiter_activer_abonnement(0, 0, $hash);
}

/* on passe soit
 * - id_abonnement + id_auteur
 * - hash
 */
function abo_traiter_activer_abonnement($id_abonnement, $id_auteur, $hash = false) {

	// si hash on le retrouve
	// s'il n'est pas la, on se tue.
	if ($hash) {
		if (!$abonnement_auteur = sql_fetsel('*', 'spip_auteurs_elargis_abonnements', 'hash = ' . sql_quote($hash))) {
			return false;
		}
		$id_abonnement = $abonnement_auteur['id_abonnement'];
		$id_auteur = $abonnement_auteur['id_auteur'];
	}
	
	// abonnement non trouve ?
	$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = ' . $id_abonnement);
	if (!$abonnement) {
		spip_log("abonnement $id_abonnement inexistant");
		die("abonnement $id_abonnement inexistant");
	}

	// jour
	if ($abonnement['periode'] == 'jours') {
		$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$abonnement['duree'],date('Y')));
	}
	// ou mois
	else {
		$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$abonnement['duree'],date('j'),date('Y')));
	}

	// S'il y a un hash de verification
	// (on provient alors certainement d'un formulaire de paiement)
	// et qu'il n'existe pas dans la base, on s'en va !

	$where = array("id_auteur=".$id_auteur, "id_abonnement=".$id_abonnement);
	if ($hash) $where["hash"] = sql_quote($hash);
	
	// lien deja cree
	if (!$id = sql_getfetsel('id_abonnement',"spip_auteurs_elargis_abonnements",$where)) {
		// si hash, c'est qu'on a pas trouve la valeur : dehors !
		if ($hash) {
			return false;
		}
		
		// sinon on en cree un
		sql_insertq("spip_auteurs_elargis_abonnements", array(
			"id_auteur"=>$id_auteur,
			"id_abonnement"=>$id_abonnement,
			"montant" => $abonnement['montant'],
			"date" => date('Y-m-d H:i:s'),
			"validite" => $validite,
			'statut_paiement'=>'ok',
			'stade_relance'=>'',
		));
	}
	// sinon on met a jour
	else {
		sql_updateq(
			"spip_auteurs_elargis_abonnements",
			array(
				"montant" => $abonnement['montant'],
				"date" => date('Y-m-d H:i:s'),
				"validite" => $validite,
				'statut_paiement' => 'ok',
				'stade_relance' => '',
			),
			$where);
	
	}
	
	// ouvir des zones pour acces restreint selon l'abonnement (action a faire)
	// envoyer le mail de confirmation (action a faire)		
	$produit = "abonnement";
	$validation_paiement = "ok" ;
	$libelle = $abonnement['libelle'] ;
	
	
	// signaler un changement
	spip_log("abonnement: appel action/envoyer_mail_confirmation abo $libelle pour auteur $id_auteur","abonnement");
	
	include_spip('action/envoyer_mail_confirmation');
			if (!abonnement_envoyer_mails_confirmation($validation_paiement,$id_auteur,$libelle,$produit)) {
				spip_log("Erreur de traitement mail (abonnement)", 'abonnement');
				$message = "erreur_site";
			}
			
	return true;
}


?>
