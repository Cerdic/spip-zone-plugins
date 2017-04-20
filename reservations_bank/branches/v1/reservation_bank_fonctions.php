<?php
/**
 * Fonctions utiles au plugin Réservations Bank
 *
 * @plugin     Réservations Bank
 * @copyright  2015
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_bank\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Crée une transaction
 *
 * @param  integer $id_reservation id_reservation
 * @return $id_transaction  Id de la transaction crée
 */
function rb_inserer_transaction($id_reservation) {

	// Voir si on peut récupérer une transaction, sino on crée une.
	if (!$id_transaction = sql_getfetsel(
			'id_transaction',
			'spip_transactions',
			'id_reservation=' . $id_reservation . ' AND statut LIKE ("commande")')){
		$inserer_transaction = charger_fonction("inserer_transaction", "bank");
		$donnees = unserialize(recuperer_fond(
				'inclure/paiement_reservation',
				array(
					'id_reservation' => $id_reservation,
					'cacher_paiement_public' => TRUE,
					)
				)
			);
		$id_transaction = $inserer_transaction($donnees['montant'], $donnees['options']);
	}

	return $id_transaction;
}

/**
 * Retourne les prestataires simple (pas besoin d'une application externe à spip).
 *
 * @return array
 */
function rb_prestataires_simples_actives() {
	// Les prestas coonfigurés.
	include_spip('inc/bank');

	$prestas_actifs = bank_lister_configs();

	// Les types de prestas nécessitanpt pas de callbacj du fournisseur.
	$prestas_simple = array('cheque', 'virement');
	$prestataires_simples_actives= array();

	foreach ($prestas_simple AS $presta) {
		if (isset($prestas_actifs[$presta]) and $prestas_actifs[$presta]['actif']) {
			$prestataires_simples_actives[$presta] = _T('bank:label_presta_' . $presta);
		}
	}

	return $prestataires_simples_actives;
}