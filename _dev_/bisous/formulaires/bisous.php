<?php
/*
 * Plugin SPIP Bisous pour Spip 2.0
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_bisous_charger_dist($id_auteur){

	if (
		$GLOBALS['visiteur_session']['id_auteur'] > 0
		and !sql_fetsel(
			'date',
			'spip_bisous',
			array(
				array('=', 'id_donneur', intval($GLOBALS['visiteur_session']['id_auteur'])),
				array('=', 'id_receveur', intval($id_auteur))
			)
		)
	)
		return array();
	else
		return false;

}

function formulaires_bisous_verifier_dist($id_auteur){

	return array();

}

function formulaires_bisous_traiter_dist($id_auteur){

	// On teste si ya pas déjà un bisou
	$bisou = sql_fetsel(
		'date',
		'spip_bisous',
		array(
			array('=', 'id_donneur', intval($GLOBALS['visiteur_session']['id_auteur'])),
			array('=', 'id_receveur', intval($id_auteur))
		)
	);
	
	// On ajoute un bisou si c'est pas déjà fait
	if (!$bisou)
		sql_insertq(
			'spip_bisous',
			array(
				'id_donneur' => intval($GLOBALS['visiteur_session']['id_auteur']),
				'id_receveur' => intval($id_auteur),
				'date' => date('Y-m-d H:i:s')
			)
		);
	
	// Relance la page
	include_spip('inc/headers');
	redirige_formulaire(self());

}

?>
