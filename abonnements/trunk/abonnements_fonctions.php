<?php
/**
 * Fonctions utiles au plugin Abonnements
 *
 * @plugin     Abonnements
 * @copyright  2012-2020
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Abonnements\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Afficher la durée d'un abonnement en fonction d'une période
 *
 * Simplifie dans la mesure du possible, ex. :
 * 12 mois → 1 an
 * 24 heures → 1 jour
 *
 * @param Integer|String $duree
 * @param String $periode
 *     heure | jour | mois | an
 * @return void
 */
function filtre_abonnements_afficher_duree_dist($duree, $periode) {

	$texte = '';

	// Simplifions certaines durées
	$simplifier = array(
		'heure' => array('modulo' => 24, 'periode' => 'jour'),
		'mois' => array('modulo' => 12, 'periode' => 'an'),
	);
	if (
		isset($simplifier[$periode])
		and (($duree % $simplifier[$periode]['modulo']) === 0)
	) {
		$duree /= $simplifier[$periode]['modulo'];
		$periode = $simplifier[$periode]['periode'];
	}

	// Singulier ou pluriel
	if ($duree == 1) {
		$texte = _T("abonnementsoffre:info_1_$periode");
	} else {
		if (substr($periode, -1, 1) !== 's') {
			$periode .= 's';
		}
		$texte = _T("abonnementsoffre:info_nb_$periode");
	}

	return $texte;
}