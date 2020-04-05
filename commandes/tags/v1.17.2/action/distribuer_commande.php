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
 * permet les distributions des produits dematerialises : par email, activation abonnement etc.
 * le statut du detail doit etre passe a 'envoye' apres distribution, pour ne pas risquer une double distribution
 * mais on ne gere pas ici, c'est a chaque fonction distribuer de decider (ie cas des retour ou exotiques)
 *
 * @param int $id_commande
 */
function action_distribuer_commande_dist($id_commande){

	if ($id_commande = intval($id_commande)
	  and $commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande))) {

		// appeler un pipeline qui permet aux plugins peripheriques de gerer
		// exemple creer a la volee un compte client si on est arrive jusqu'ici avec id_auteur=0 (nouveau client, workflow simplifie)
		// un plugin peut aussi annuler la distribution pour la remettre a plus tard en retournant false
		$commande = pipeline('commandes_pre_distribuer_commande',$commande);

		spip_log("action_distribuer_commande_dist distribuer la commande #$id_commande",'commandes');

		if ($commande and $id_commande = $commande['id_commande']) {
			if ($details = sql_allfetsel("*","spip_commandes_details","id_commande=".intval($id_commande)) ){
				foreach ($details as $detail){
					$objet = $detail['objet'];
					if ($distribuer = charger_fonction($objet, "distribuer", true)){
						$s = $distribuer($detail['id_objet'], $detail, $commande);
						spip_log("action_distribuer_commande_dist distribuer commande #$id_commande detail : $objet #".$detail['id_objet']." -> $s",'commandes');
						if ($s and in_array($s, array('attente','envoye','retour'))) {
							sql_updateq('spip_commandes_details',array('statut' => $s), 'id_commandes_detail='.intval($detail['id_commandes_detail']));
						}
					}
				}
			}
		}

	}

}
