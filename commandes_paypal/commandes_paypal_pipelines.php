<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function commandes_paypal_traitement_paypal($flux){
	// Si on est dans le bon cas d'un paiement de commande et qu'il y a une référence et que la commande existe toujours
	if (
		$flux['args']['paypal']['custom'] == 'payer_commande'
		and $reference = $flux['args']['paypal']['invoice']
		and $id_commande = sql_getfetsel('id_commande', 'spip_commandes', array('reference = '.sql_quote($reference), 'statut = '.sql_quote('encours')))
	){
		// On change le statut de la commande
		$ok = sql_updateq(
			'spip_commandes',
			array(
				'statut' => 'paye'
			),
			'id_commande = '.$id_commande
		);
		
		// On change le statut de tous les détails
		if ($ok)
			$ok = sql_updateq(
				'spip_commandes_details',
				array(
					'statut' => 'paye'
				),
				'id_commande = '.$id_commande
			);
	}
	
	return $flux;
}

?>
