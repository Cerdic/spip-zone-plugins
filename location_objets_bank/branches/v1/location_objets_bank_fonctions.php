<?php
/**
 * Fonctions utiles au plugin Location d’objets
 *
 * @plugin     Location d’objets - paiements
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Reservation_bank\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Crée ou update de transaction
 *
 * @param integer $id_objets_location
 *        	identifian de la location
 * @return $id_transaction Id de la transaction crée
 */
function lob_chercher_transaction($id_objets_location) {
	$donnees = unserialize(recuperer_fond('inclure/paiement_location', array(
		'id_objets_location' => $id_objets_location,
		'tableau' => TRUE
	)));

	// Voir si il y a une transaction ouverte pour la location
	if ($id_transaction = sql_getfetsel(
		'id_transaction',
		'spip_transactions', 'statut IN ("commande","attente") AND id_objets_location=' . $id_objets_location,
		'',
		'id_transaction DESC')) {
		// Si ouverte on l'actualise.
		$set = array('montant' => $donnees['montant']);
		foreach($donnees['options'] as $cle => $valeur) {
			if ($valeur and $cle != 'champs') {
				$set[$cle] = $valeur;
			}
		}
		sql_updateq('spip_transactions', $set, 'id_transaction=' .$id_transaction);

	}
	//Sinon on en crée une.
	else {
		$inserer_transaction = charger_fonction("inserer_transaction", "bank");
		$id_transaction = $inserer_transaction($donnees['montant'], $donnees['options']);
	}

	return $id_transaction;
}
