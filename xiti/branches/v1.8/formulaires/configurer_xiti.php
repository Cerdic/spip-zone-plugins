<?php

/**
 * Formulaire de configuration XITI
 *
 * @plugin     Xiti
 * @copyright  2014
 * @author     France diplomatie - Vincent
 * @license    GNU/GPL
 * @package    SPIP\Xiti\Formulaires\Configurer_xiti
 */

/**
 * Vérification du formulaire
 *
 * Forcer la validation des champs obligatoires
 */
function formulaires_configurer_xiti_verifier_dist() {
	$erreurs = array();
	if (_request('activer_xiti') == 'oui') {
		foreach (array('xtsd_xiti', 'xtsite_xiti', 'xtdmc_xiti') as $obligatoire) {
			if (!_request($obligatoire)) {
				$erreurs[$obligatoire] = _T('info_obligatoire');
			}
		}
	}
	return $erreurs;
}
