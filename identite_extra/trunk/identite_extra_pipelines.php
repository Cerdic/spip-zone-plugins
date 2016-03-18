<?php

if (!defined('_ECRIRE_INC_VERSION')) 
	return;

/**
 * Ajouter des champs supplémentaires sur configurer_identite
 * les champs sont définis dans une globale facilement surchargeable dans le fichier mes_options.php
 *
 * exemple:
 * $GLOBALS['identite_extra'] = array('champs1','champs2','champs3',...)
 *
 * @param array $flux
 * @return array
 */
function identite_extra_formulaire_fond($flux) {

	# formulaire : configurer_identite
	if ( $flux['args']['form'] == 'configurer_identite'
		AND ( $p = strpos($flux['data'], '<!--extra-->') )
		AND isset( $GLOBALS['identite_extra'] )
		AND is_array( $GLOBALS['identite_extra'] ) ) {

		$ajout = recuperer_fond("prive/formulaires/configurer_identite_extra", $flux['args']['contexte'] );
		$flux['data'] = substr_replace($flux['data'], $ajout, $p, 0);
	}

	return $flux;
}

// Charger les valeurs déjà existantes dans la méta
function identite_extra_formulaire_charger($flux) {

	# formulaire : configurer_identite
	if ( $flux['args']['form'] == 'configurer_identite' ) {

		$valeurs = array();
		foreach ($GLOBALS['identite_extra'] as $k)
			$valeurs['identite_extra'][$k] = lire_config('identite_extra/' . $k, '');

		$flux['data'] = array_merge($flux['data'],$valeurs);
	}
	return $flux;
}


// Mettre à jour la méta
function identite_extra_formulaire_traiter($flux) {

	# formulaire : configurer_identite
	if ( $flux['args']['form'] == 'configurer_identite' AND $config = _request('identite_extra') ) {
			ecrire_config('identite_extra', $config);
	}
	return $flux;
}
