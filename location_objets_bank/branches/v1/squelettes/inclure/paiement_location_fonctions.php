<?php
/**
 * Fonctions utiles au plugin Location d’objets
 *
 * @plugin     Location d’objets - paiements
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets_bank\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Calcule le montant d'un acompte.
 *
 * @param array $donnees
 *  les données de la transaction.
 * @return array
 *  les données de la transaction.
 */
function lob_calculer_prix_acompte($donnees) {
	$montant = $donnees['montant'];
	$montant_ht = $donnees['options']['montant_ht'];
	$acompte = ($montant_ht / 100) * $donnees['options']['acompte'];

	// Adapter les montants
	$donnees['options']['montant_ht'] = $acompte;
	$donnees['montant'] = $acompte;

	// Tenir compte d'éventuels taxes.
	if ($montant > $montant_ht) {
		$taxe = $montant - $montant_ht;
		$donnees['montant'] = $acompte + $taxe;
	}

return $donnees;
}