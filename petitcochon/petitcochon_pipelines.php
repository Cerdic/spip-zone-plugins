<?php

/**
 * Fonctions pour Petit Cochon
 *
 * @plugin     Petit Cochon
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\petitcochon\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function petitcochon_affiche_gauche($flux) {
	return petitcochon_boite_info($flux, 'affiche_gauche');
}
function petitcochon_affiche_droite($flux) {
	return petitcochon_boite_info($flux, 'affiche_droite');
}

/**
 * Afficher le bouton de peuplage du fichier json
 * @param array $flux
 * @return array
 */
function petitcochon_boite_info($flux, $pipeline) {
	include_spip('inc/presentation');

	$flux['args']['pipeline'] = $pipeline;

	if (trouver_objet_exec($flux['args']['exec'] == 'liste_petitcochon')) {
		$texte = recuperer_fond('prive/squelettes/navigation/vider_petitcochon');

		$flux['data'] .= $texte;
	}

	return $flux;
}
