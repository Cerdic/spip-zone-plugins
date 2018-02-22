<?php
/**
 * Fonctions utiles au plugin API de vérification
 *
 * @plugin     verifier
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Verifier\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Liste toutes les vérifications possibles
 * 
 * @uses verifier_lister_disponibles()
 * @filtre verifier_lister_disponibles
 *
 * @param string $repertoire
 *     Dans quel repertoire chercher les yaml.
 * @return array
 *     Un tableau listant les vérifications et leurs options
 */
function filtre_verifier_lister_disponibles_dist($repertoire = 'verifier') {
	include_spip('inc/verifier');

	return verifier_lister_disponibles($repertoire);
}