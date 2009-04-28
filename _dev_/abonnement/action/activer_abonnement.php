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


function abo_traiter_activer_abonnement($id_abonnement, $id_auteur) {

	// abonnement non trouve ?
	$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = ' . $id_abonnement);
	if (!$abonnement) {
		spip_log("abonnement $id_abonnement inexistant");
		die("abonnement $id_abonnement inexistant");
	}

	// jour
	if ($abonnement['pediode'] == 'jours') {
		$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$abonnement['duree'],date('Y')));
	}
	// ou mois
	else {
		$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$abonnement['duree'],date('j'),date('Y')));
	}
	
	// lien deja cree
	if (!$id = sql_getfetsel('id_abonnement',"spip_auteurs_elargis_abonnements",array("id_auteur"=>$id_auteur, "id_abonnement"=>$id_abonnement))) {
		// on en cree un
		sql_insertq("spip_auteurs_elargis_abonnements", array(
			"id_auteur"=>$id_auteur,
			"id_abonnement"=>$id_abonnement,
			"montant" => $abonnement['montant'],
			"date" => date('Y-m-d H:i:s'),
			"validite" => $validite,
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
				'stade_relance'=>'',
			),
			array("id_auteur=".$id_auteur, "id_abonnement=".$id_abonnement));
	}
	return true;
}


?>
