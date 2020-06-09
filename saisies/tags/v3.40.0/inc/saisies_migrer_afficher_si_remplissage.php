<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *  Parcours un tableau de saisies, 
 *  regarde si on a un champ afficher_si_remplissage 
 *  et le transforme en champs
 *  afficher_si + 
 *  afficher_si_remplissage_uniquement 
 * @param array $saisies
 *		Les saisies initiales
 * @return array $saisies
 *		Les saisies modifiées
 */
function saisies_migrer_afficher_si_remplissage($saisies) {
	// Parcourir le tableau de saisie
	foreach($saisies as &$saisie) {
		$options = &$saisie['options'];

		if (isset($options['afficher_si_remplissage'])
				and $options['afficher_si_remplissage'] != '') {
			// si a tout hasard la personne avait rempli les deux champs afficher_si, on fusionne les conditions
			if (isset($options['afficher_si']) 
					and $options['afficher_si'] != '') {
					$options['afficher_si'] = 
						"("
						. $options['afficher_si'] 
						. ")"
						. " && " //emploi de && pour pouvoir fonctionner en js
						. "("
						. $options['afficher_si_remplissage'] 
						.")";
			} else {
				$options['afficher_si'] = $options['afficher_si_remplissage'];
			}

			$options['afficher_si_remplissage_uniquement'] = 'on';
			unset($options['afficher_si_remplissage']);
		}

		// appliquer recursivement si on a des saisies filles
		if (isset($saisie['saisies']) and is_array($saisie['saisies'])) {
			$saisie['saisies'] = saisies_migrer_afficher_si_remplissage($saisie['saisies']); 
		}
	}

	return $saisies;
}
