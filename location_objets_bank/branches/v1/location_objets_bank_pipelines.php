<?php
/**
 * Utilisations de pipelines par Location d’objets - paiements
 *
 * @plugin     Location d’objets - paiements
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets_bank\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Intervient au traitement d'un formulaire CVT
 *
 * @pipeline formulaire_traiter
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function location_objets_bank_formulaire_traiter($flux) {
	$form = $flux['args']['form'];

	// Affiche le formulaire de paiement au retour du formulaire réservation
	if ($form == 'editer_objets_location') {
		$id_objets_location = $flux['data']['id_objets_location'];
		lob_chercher_transaction($id_objets_location);

		if (
			!_request('espace_prive') and
			!_request('gratuit')) {
				$flux['data']['redirect'] = generer_url_public(
					'paiement_location',
					[
						'id_objets_location' => $id_objets_location,
						'lang' => _request('lang'),
					]);
		}
	}

	return $flux;
}

/**
 * Permet de compléter ou modifier le résultat de la compilation d’un squelette donné.
 *
 * @pipeline recuperer_fond
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function location_objets_bank_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];
	$contexte = $flux['data']['contexte'];

	// Ajoute le message de paiement à la notification de réservation.
	if ($fond == 'inclure/location' and
		$id_objets_location = $flux['data']['contexte']['id_objets_location'] and
		$statut = sql_getfetsel('statut', 'spip_objets_locations', 'id_objets_location=' . $id_objets_location) and
		(in_array($statut, array('attente', 'paye')))) {
			$qui = $flux['data']['contexte']['qui'];
			$transaction = sql_fetsel(
				'mode, id_transaction, transaction_hash, message, tracking_id',
				'spip_transactions',
				'id_objets_location=' . $id_objets_location,
				'',
				'date_transaction DESC');
			$mode = $transaction['mode'];
			$id_transaction = $transaction['id_transaction'];
			if ($qui == 'client') {
				if ($statut == 'attente') {
					$pattern = array(
						'|<p class="titre h4">|',
						'|</p>|'
					);
					$replace = array(
						'<h3>',
						'</h3>'
					);
					$texte = preg_replace(
							$pattern,
							$replace,
							bank_afficher_attente_reglement($mode, $id_transaction, $transaction['transaction_hash'], '')
						);
				}
				else {
					$texte = '<p>' . $transaction['message'] . '</p>';
				}
			}
			elseif ($qui == 'vendeur') {
				$url = generer_url_ecrire('transaction', 'id_transaction=' . $id_transaction);
				$texte = '<h2>' . _T('location_objets_bank:titre_paiement_vendeur') . '</h2>';
				$texte .= '<p>' . _T('location_objets_bank:message_paiement_vendeur', array(
					'mode' => $mode,
					'url' => $url
				)) . '</p>';
			}

			$flux['data']['texte'] .= $texte;
		}

		// Ajouter le message pour la référence du paiement par virement.
		if ($fond == 'presta/virement/payer/attente' and
			$tracking_id = sql_getfetsel(
				'tracking_id',
				'spip_transactions',
				'id_transaction=' . $contexte['id_transaction']) and
			$id_objets_location = sql_getfetsel('id_objets_location', 'spip_objets_locations', 'reference LIKE ' . sql_quote($tracking_id))) {

			$texte = '<strong>' . _T('location_objets_bank:location_paiement_reference', array(
				'reference' => $tracking_id
			)) . '</strong>';
			$flux['data']['texte'] = str_replace('</div>', $texte . '</div>', $flux['data']['texte']);
		}

	return $flux;
}

/**
 * Enregistrer le bon reglement d'une commande liee a une transaction du plugin bank
 *
 * @pipeline bank_traiter_reglement
 *
 * @param array $flux
 * @return array mixed
 */
function location_objets_bank_bank_traiter_reglement($flux) {
	// Si on est dans le bon cas d'un paiement de location et qu'il y a un id_objets_location et que la location existe toujours

	if ($id_transaction = $flux['args']['id_transaction'] and
		$transaction = sql_fetsel("*", "spip_transactions", "id_transaction=" . intval($id_transaction)) and
		$id_objets_location = $transaction['id_objets_location'] and
		$location = sql_fetsel('statut, reference,montant_paye', 'spip_objets_locations', 'id_objets_location=' . intval($id_objets_location))) {
		include_spip('action/editer_objet');

		$fonction = charger_fonction('objets_location', 'prix');
		$prix = $fonction($id_objets_location);
		$montant = $transaction['montant'];
		$montant_paye = $transaction['montant_regle'] + $location['montant_paye'];

		objet_modifier('objets_location', $id_objets_location, array(
			'date_paiement' => $transaction['date_transaction'],
			'montant_paye' => $montant_paye,
		));

		$statut = 'paye';
		if($montant_paye < $prix) {
			$statut = 'partiel';
			lob_chercher_transaction($id_objets_location);
		}

		objet_instituer('objets_location', $id_objets_location, array(
			'statut' => $statut,
		));

		// un message gentil pour l'utilisateur qui vient de payer, on lui rappelle son numero de commande
		$flux['data'] .= "<br><br>" . _T('location_objets_bank:merci_de_votre_location_paiement', array(
			'reference' => $location['reference']
		));

	}

	return $flux;
}

/**
 * Changer de statut si transaction en attente
 *
 * @pipeline trig_bank_reglement_en_attente
 *
 * @param array $flux
 * @return array
 */
function location_objets_bank_trig_bank_reglement_en_attente($flux) {
	if ($id_objets_location = sql_getfetsel(
			'id_objets_location',
			'spip_transactions',
			'id_transaction=' . $flux['args']['id_transaction'])) {
		include_spip('action/editer_objet');

		objet_instituer('objets_location', $id_objets_location, array(
			'statut' => 'attente'
		));
	}

	return $flux;
}

/**
 * Ajout ou de modification le contenu des listes présentant les enfants d’un objet.
 * Il reçoit dans args le nom de la page en cours et l’identifiant de l’objet,
 * et dans data le code HTML présentant les enfants de l’objet.
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_objets_bank_affiche_enfants($flux) {

	// Objets_informations sur les objets choisis.
	if ($flux['args']['exec'] == 'objets_location') {
		$texte .= recuperer_fond('prive/objets/liste/transactions_locations', array(
			'id_objets_location' => $flux['args']['id_objets_location'],
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

