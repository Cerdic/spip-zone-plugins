<?php
/**
 * API d'édition du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Editer
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Distribuer une commande : pour chaque ligne de la commande on appelle l'api distribuer
 * si elle est implementee pour l'objet concerne
 *
 * @param int $id_commande
 */
function action_distribuer_commande_dist($id_commande){

	if ($id_commande = intval($id_commande)
	  and $commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande))) {

		// appeler un pipeline qui permet aux plugins peripheriques de gerer
		// exemple creer a la volee un compte client si on est arrive jusqu'ici avec id_auteur=0 (nouveau client, workflow simplifie)
		$commande = pipeline('commandes_pre_distribuer_commande',$commande);

		if ($details = sql_allfetsel("*","spip_commandes_details","id_commande=".intval($id_commande)) ){
			foreach ($details as $detail){
				$objet = $detail['objet'];
				if ($distribuer = charger_fonction($objet, "distribuer", true)){
					$distribuer($detail['id_objet'], $detail, $commande);
				}
			}
		}
	}

}
