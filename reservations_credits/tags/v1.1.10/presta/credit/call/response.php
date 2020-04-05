<?php
/*
 * Paiement Bancaire
 * module de paiement bancaire multi prestataires
 * stockage des transactions
 *
 * Auteurs :
 * Cedric Morin, Nursit.com
 * (c) 2012-2015 - Distribue sous licence GNU/GPL
 *
 */
if (! defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * il faut avoir un id_transaction et un transaction_hash coherents
 * pour se premunir d'une tentative d'appel exterieur
 *
 * @param array $config
 * @param null|array $response
 * @return array
 */
function presta_credit_call_response_dist($config, $response = null) {
	$mode = $config['presta'];
	// recuperer la reponse en post et la decoder, en verifiant la signature
	if (!$response)
		$response = bank_response_simple($mode);

	if (!isset($response['id_transaction']) or !isset($response['transaction_hash'])) {
		return bank_transaction_invalide(0, array(
			'mode' => $mode,
			'erreur' => "id_transaction ou transaction_hash absent",
			'log' => bank_shell_args($response)
		));
	}

	$id_transaction = $response['id_transaction'];
	$transaction_hash = $response['transaction_hash'];

	if (!$row = sql_fetsel('*', 'spip_transactions', 'id_transaction=' . intval($id_transaction))) {
		return bank_transaction_invalide($id_transaction, array(
			'mode' => $mode,
			'erreur' => "transaction inconnue",
			'log' => bank_shell_args($response)
		));
	}
	if ($transaction_hash != $row['transaction_hash']) {
		return bank_transaction_invalide($id_transaction, array(
			'mode' => $mode,
			'erreur' => "id_transaction $id_transaction, hash $transaction_hash non conforme",
			'log' => bank_shell_args($response)
		));
	}

	// Obtenir la devise.
	$auteur = $row['auteur'];
	include_spip('reservations_credits_fonctions');

	if ($id_reservation = $row['id_reservation']) {
		$donnees = sql_fetsel('spip_reservations_details.devise,reference,email,id_auteur',
				'spip_reservations LEFT JOIN spip_reservations_details USING (id_reservation)',
				'spip_reservations.id_reservation=' . $id_reservation);
		$devise = $donnees['devise'];
		$descriptif = _T('reservation_bank:paiement_reservation', array (
				'id_reservation' => $id_reservation
			));

		if (!$montant_reservations_detail_total = _request('montant_reservations_detail_total')) {
			include_spip('inc/reservation_bank');
			$montant_reservations_detail_total = montant_reservations_detail_total($id_reservation);
		}


		$paiement_detail = array ();
		foreach ( array_keys($montant_reservations_detail_total) as $id_reservations_detail ) {
			$paiement_detail[$id_reservations_detail] = _request('montant_reservations_detail_' . $id_reservations_detail);
		}
		$montant_regle = array_sum($paiement_detail);

	}
	elseif ($id_commande = $row['id_commande']) {
		$devise = 'EUR';
		$descriptif = _T('reservation_bank:paiement_commande', array (
			'id_commande' => $id_commande,
		));
		$id_objet = $id_commande;
		$objet = 'commande';
		$montant_regle = $row['montant'];
	}
	else {
		return bank_transaction_invalide($id_transaction, array(
			'mode' => $mode,
			'erreur' => "id_transaction $id_transaction, hash $transaction_hash objet non connu",
			'log' => bank_shell_args($response)
		));
	}

	// Si on trouve un crédit
	if (isset($row['auteur'])
			and $email = $row['auteur']
			and $credit = credit_client('', $row['auteur'], $devise)
			and (intval($credit) >= 0 or floatval($var) >= 0.00)) {

		$set = array(
			"mode" => $mode,
			"montant_regle" => $montant_regle,
			"date_paiement" => date('Y-m-d H:i:s'),
			"statut" => 'ok',
			"reglee" => 'oui'
		);

		if (intval($credit) >= intval($row['montant'])
				or floatval($credit) >= floatval($row['montant'])) {
			// OK, on peut accepter le reglement
			$statut = 'reglée';
			$res = true;
		}
		else {
			// Le crédit n'est pas suffisant

			$set['montant_regle'] = $montant_regle ;
			$set['statut'] = 'attente';
			$set['reglee'] = 'par';
			$statut = 'reglée acompte';
			$res = 'wait';

		}

		sql_updateq("spip_transactions", $set, "id_transaction=" . intval($id_transaction));
		spip_log("call_response : id_transaction $id_transaction, $statut", $mode);

		// Enregistrer un mouvement crédit

		$action = charger_fonction('editer_objet', 'action');

		$reference = $donnes['reference'];

		$set = array (
			'type' => 'debit',
			'email' => $email,
			'descriptif' => $descriptif,
			'id_reservation' => $id_reservation,
			'id_objet' => $id_objet,
			'objet' => $objet,
			'montant' => $montant_regle,
			'devise' => $devise
		);

		$action('new', 'reservation_credit_mouvement', $set);
	}
	// Pas de crédit trouvé.
	else {
		return bank_transaction_invalide($id_transaction, array(
			'mode' => $mode,
			'erreur' => "id_transaction $id_transaction, montant " . $row['montant'] . "> pas de crédit diponible",
			'log' => bank_shell_args($response)
		));
	}

	$regler_transaction = charger_fonction('regler_transaction', 'bank');
	$regler_transaction($id_transaction, array(
		'row_prec' => $row
	));

	return array(
		$id_transaction,
		$res
	);
}

