<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function commandes_paypal_traitement_paypal($flux){
	// Si on est dans le bon cas d'un paiement de commande et qu'il y a une référence et que la commande existe toujours
	if (
		$flux['args']['paypal']['custom'] == 'payer_commande'
		and $reference = $flux['args']['paypal']['invoice']
		and $commande = sql_fetsel('id_commande, statut, id_auteur', 'spip_commandes', 'reference = '.sql_quote($reference))
	){
		$id_commande = $commande['id_commande'];
		$statut_commande = $commande['statut'];
		$statut_paypal = $flux['args']['paypal']['payment_status'];
		$prix_paypal = $flux['args']['paypal']['mc_gross'];
		
		// Si le statut Paypal est "Pending" on passe juste la commande en attente et on verra plus tard pour le reste
		if ($statut_paypal == 'Pending'){
			$statut_nouveau = 'attente';
		}
		// Si Paypal est "Completed" on vérifie que le montant correspond au prix de cette commande
		elseif ($statut_paypal == 'Completed'){
			$fonction_prix = charger_fonction('prix', 'inc/');
			$prix_commande = $fonction_prix('commande', $id_commande);
			 
			 // Si on a pas assez payé
			 if ($prix_paypal < $prix_commande){
			 	$statut_nouveau = 'partiel';
			 }
			 // Sinon c'est bon
			 else{
			 	$statut_nouveau = 'paye';
			 }
		}
		// Sinon on dit que c'est en erreur
		else{
			$statut_nouveau = 'erreur';
		}

		spip_log("envoi vers instituer $id_commande-$statut_nouveau",'commandes_paypal_traitement');
		
		//on institue la commande
		$action = charger_fonction('instituer_commande', 'action/');
		$action('$id_commande-$statut_nouveau');

	}
	
	return $flux;
}

?>
