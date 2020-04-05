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
 * Lister un à un les paiements des échéances prévus
 * 
 * @param array $echeances
 * 		Tableau des échéances décrites suivant ce qu'on attend pour les commandes :
 * 		```
 * 		array(
 * 			array('montant' => 40, 'montant_ht' => 33.33, 'nb' => 2),
 * 			array('montant' => 50),
 * 		)
 * 		```
 * @return array
 * 		Retourne un tableau listant uniquement les montants un par un :
 * 		```
 * 		array(array('montant' => 40, 'montant_ht' => 33.33), array('montant' => 40, 'montant_ht' => 33.33), array('montant' => 50, 'montant_ht' => 50))
 * 		```
 **/
function commandes_lister_paiements_echeances($echeances) {
	$paiements_echeances = array();
	
	// Pour chaque montant d'échéance, s'il y a un nombre on rajoute le montant N fois
	foreach ($echeances as $echeance) {
		$montant = floatval($echeance['montant']);
		$montant_ht = $montant;
		if (isset($echeance['montant_ht'])) {
			$montant_ht = floatval($echeance['montant_ht']);
		}
		$paiement = array('montant' => $montant, 'montant_ht' => $montant_ht);

		if (!isset($echeance['nb']) or !$nb = intval($echeance['nb'])){
			$nb = 1;
		}
		while ($nb-->0) {
			$paiements_echeances[] = $paiement;
		}
	}

	return $paiements_echeances;
}

/**
 * Lister un à un les montants des échéances prévus
 * idem commandes_lister_paiements_echeances mais retourne uniquement le montant (ttc) pour chaque echeance, et pas un tableau
 * @deprecated
 *
 * @param array $echeances
 * @return array
 * 		Retourne un tableau listant uniquement les montants un par un :
 * 		```
 * 		array(40, 40, 50)
 * 		```
 **/
function commandes_lister_montants_echeances($echeances){
	$paiements = commandes_lister_paiements_echeances($echeances);
	$montants_echeances = array();
	foreach ($paiements as $paiement) {
		$montants_echeances[] = $paiement['montant'];
	}
	return $montants_echeances;
}

function commandes_nb_echeances_payees($id_commande) {
	$nb_paiements = 0;
	
	if (
		defined('_DIR_PLUGIN_BANK')
		and $transactions_commande = intval(sql_countsel('spip_transactions',array('id_commande = '.$id_commande, 'statut = "ok"')))
	) {
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
 * @return array|bool
 * 		Retourne la description de la prochaine échéance
 **/
function commandes_trouver_prochaine_echeance_desc($id_commande, $echeances=null, $ignorer_derniere=false) {
	static $prochaines_echeances = array();
	$id_commande = intval($id_commande);
	
	// Si on a déjà la réponse dans ce hit PHP, on retourne
	if (isset($prochaines_echeances[$id_commande][$ignorer_derniere])
		and $prochaines_echeances[$id_commande][$ignorer_derniere]) {
		return $prochaines_echeances[$id_commande][$ignorer_derniere];
	}
	
	// S'il n'y a pas d'échéances, on va les chercher
	if (
		is_null($echeances)
		and $echeances = sql_getfetsel('echeances', 'spip_commandes', 'id_commande = ' . intval($id_commande))
	) {
		$echeances = unserialize($echeances);
	}
	
	// Si on a bien des échéances au final
	if ($echeances) {
		// Si les échéances sont uniques, toujours les mêmes, sans taxe, c'est facile
		if (!is_array($echeances)) {
			$prochaine = array('montant' => floatval($echeances));
			$prochaine['montant_ht'] = $prochaine['montant'];
		}
		// Sinon on va chercher a combien de paiements payés on en est déjà
		// afin de trouver le montant de la prochaine échéance
		else {
			// On cherche le nombre de paiements valides pour cette commande
			$nb_paiements = commandes_nb_echeances_payees($id_commande);
			// Si on cherche à tester la dernière transaction, il faut l'ignorer
			if ($ignorer_derniere) {
				$nb_paiements = $nb_paiements - 1;
			}
			
			// On liste les montants
			$paiements_echeances = commandes_lister_paiements_echeances($echeances);
			
			// Si le nombre déjà payé est supérieur à la liste on prend le dernier
			if ($nb_paiements >= count($paiements_echeances)) {
				$prochaine = end($paiements_echeances);
			}
			else {
				$prochaine = $paiements_echeances[$nb_paiements];
			}
			
			// Histoire d'être sûr
			$prochaine = array_map('floatval', $prochaine);
		}
		
		// Si on a un montant correct
		if ($prochaine and isset($prochaine['montant']) and $prochaine['montant'] > 0) {
			$prochaines_echeances[$id_commande][$ignorer_derniere] = $prochaine;
			return $prochaine;
		}
	}
	
	// On a rien trouvé avant
	return false;
}

/**
 * @deprecated
 * @param $id_commande
 * @param null $echeances
 * @param bool $ignorer_derniere
 * @return bool|mixed
 */
function commandes_trouver_prochaine_echeance($id_commande, $echeances=null, $ignorer_derniere=false) {
	$echeance = commandes_trouver_prochaine_echeance_desc($id_commande, $echeances, $ignorer_derniere);
	if ($echeance and isset($echeance['montant'])) {
		return $echeance['montant'];
	}
	return false;
}

