<?php
/**
 * Fonctions pour la compilation des squelettes (filtres, etc)
 *
 * @plugin     Ayants droit
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Ayantsdroit\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Remplacer certaines valeurs pré-définies
 **/
function ayantsdroit_traiter_credits($texte) {
	$valeurs = array(
		'annee' => date('Y'),
		'vide' => '',
	);
	
	$texte = _L($texte, $valeurs);
	
	return $texte;
}
