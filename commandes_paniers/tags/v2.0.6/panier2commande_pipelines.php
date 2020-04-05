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
 * Sur une transformation de commande en attente
 * on supprime le panier source si besoin
 * @param $flux
 */
function panier2commande_post_edition($flux){

	// Si on est dans le cas d'une commande qui passe de attente/en cours=>paye/livre/erreur
	if ($flux['args']['table']=='spip_commandes'
	  AND $id_commande=$flux['args']['id_objet']
	  AND $flux['args']['action']=='instituer'
	  AND isset($flux['data']['statut'])
    AND !in_array($flux['data']['statut'],array('attente','encours'))
	  AND in_array($flux['args']['statut_ancien'],array('attente','encours'))
	  AND $commande = sql_fetsel('id_commande, source', 'spip_commandes', 'id_commande='.intval($id_commande))){

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
