<?php

if (!defined("_ECRIRE_INC_VERSION"))
	return;

	function action_rb_instituer_prestataire_dist(){
		include_spip('inc/config');
		include_spip('inc/bank');
		include_spip('reservation_bank_fonctions');

		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		$config = lire_config('bank');

		list($id_reservation, $presta) = explode('-', $arg);

		$id_transaction = rb_inserer_transaction($id_reservation);

		$response =	sql_fetsel(
				'id_transaction,transaction_hash,autorisation_id',
				'spip_transactions',
				'id_transaction=' . $id_transaction);

		$response['autorisation_id'] = 'wait';
		$config['presta'] = $presta;
		$response['autorisation_id'] = 'wait';
		if ($presta == 'gratuit') {
			$response['autorisation_id'] = 'ok';
		}

		bank_simple_call_response($config, $response);

	}
