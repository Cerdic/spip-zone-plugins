<?php
/**
 * Fonction du plugin Commandes de paniers
 *
 * @plugin     Commandes de Paniers
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Panier2commande\pipelines
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Sur un evenement de paiement commande (succes/echec/attente)
 * on supprime le panier source si besoin
 * @param $flux
 */
function panier2commande_bank_reglement_succes_attente_echec($flux){

	// Si on est dans le bon cas d'un paiement de commande et qu'il y a un id_commande et que la commande existe toujours
	if (
		$id_transaction = $flux['args']['id_transaction']
		and $transaction = sql_fetsel("*","spip_transactions","id_transaction=".intval($id_transaction))
		and $id_commande = $transaction['id_commande']
		and $commande = sql_fetsel('id_commande, source', 'spip_commandes', 'id_commande='.intval($id_commande))
	){
		if (preg_match(",^panier#(\d+)$,",$commande['source'],$m)){
			$id_panier = intval($m[1]);
			$supprimer_panier = charger_fonction('supprimer_panier', 'action/');
			$supprimer_panier($id_panier);

			// nettoyer une eventuelle double commande du meme panier
			sql_updateq("spip_commandes",array('source'=>''),"source=".sql_quote($commande['source']));
			#spip_log('suppression panier '.$id_panier,'paniers');
		}
	}

	return $flux;
}