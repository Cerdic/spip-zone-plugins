<?php
/**
 * Fonctions du plugin Commandes relatives aux échéances attendues
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 **/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Lister un à un les montants des échéances prévus
 * 
 * @param array $echeances
 * 		Tableau des échéances décrites suivant ce qu'on attend pour les commandes :
 * 		```
 * 		array(
 * 			array('montant' => 40, 'nb' => 2),
 * 			array('montant' => 50),
 * 		)
 * 		```
 * @return array
 * 		Retourne un tableau listant uniquement les montants un par un :
 * 		```
 * 		array(40, 40, 50)
 * 		```
 **/
function commandes_lister_montants_echeances($echeances) {
	$montants_echeances = array();
	
	// Pour chaque montant d'échéance, s'il y a un nombre on rajoute le montant N fois
	foreach ($echeances as $echeance) {
		$montant = floatval($echeance['montant']);
		
		if ($nb = intval($echeance['nb'])) {
			$montants_echeances = array_merge($montants_echeances, array_fill(0, $nb, $montant));
		}
		else {
			array_push($montants_echeances, $montant);
		}
	}
	
	return $montants_echeances;
}

function commandes_nb_echeances_payees($id_commande) {
	$nb_paiements = 0;
	
	if ($transactions_commande = intval(sql_countsel(
		'spip_transactions',
		array('id_commande = '.$id_commande, 'statut = "ok"')
	))) {
		$nb_paiements += $transactions_commande;
	}
	
	return $nb_paiements;
}

/**
 * Trouver la prochaine échéance à payer pour une commande
 * 
 * @param int $id_commande
 * 		Identifiant de la commande
 * @param array|float $echeances
 * 		Montant unique ou tableau décrivant des échéances complexes
 * @return float
 * 		Retourne le montant de la prochaine échéance
 **/
function commandes_trouver_prochaine_echeance($id_commande, $echeances=null) {
	static $montants = array();
	$id_commande = intval($id_commande);
	
	// Si on a déjà la réponse dans ce hit PHP, on retourne
	if (isset($montants[$id_commande]) and $montants[$id_commande]) {
		return $montants[$id_commande];
	}
	
	// S'il n'y a pas d'échéances, on va les chercher
	if (
		is_null($echeances)
		and $echeances = sql_getfetsel('echeances', 'spip_commandes', 'id_commande = '.$id_commande)
	) {
		$echeances = unserialize($echeances);
	}
	
	// Si on a bien des échéances au final
	if ($echeances) {
		// Si les échéances sont uniques, toujours les mêmes, c'est facile
		if (!is_array($echeances)) {
			$montant = floatval($echeances);
		}
		// Sinon on va chercher a combien de paiements payés on en est déjà
		// afin de trouver le montant de la prochaine échéance
		else {
			// On cherche le nombre de paiements valides pour cette commande
			$nb_paiements = commandes_nb_echeances_payees($id_commande);
			
			// On liste les montants
			$montants_echeances = commandes_lister_montants_echeances($echeances);
			
			// Si le nombre déjà payé est supérieur à la liste on prend le dernier
			if ($nb_paiements >= count($montants_echeances)) {
				$montant = array_pop($montants_echeances);
			}
			else {
				$montant = $montants_echeances[$nb_paiements];
			}
			
			// Histoire d'être sûr
			$montant = floatval($montant);
		}
		
		// Si on a un montant correct
		if ($montant and $montant > 0) {
			$montants[$id_commande] = $montant;
			return $montant;
		}
	}
	
	// On a rien trouvé avant
	return false;
}
