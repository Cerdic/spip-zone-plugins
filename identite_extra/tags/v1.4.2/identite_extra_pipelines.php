<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter des champs supplémentaires sur configurer_identite
 * 
 * Les champs sont définis dans une fonction extensible par le pipeline "identite_extra_champs"
 *
 * @param array $flux
 * @return array
 */
function identite_extra_formulaire_fond($flux) {
	# formulaire : configurer_identite
	if (
		$flux['args']['form'] == 'configurer_identite'
		and ($p = strpos($flux['data'], '<!--extra-->'))
		and identite_extra_champs()
	) {
		$ajout = recuperer_fond('prive/formulaires/configurer_identite_extra', $flux['args']['contexte']);
		$flux['data'] = substr_replace($flux['data'], $ajout, $p, 0);
	}
	
	return $flux;
}

// Charger les valeurs déjà existantes dans la méta
function identite_extra_formulaire_charger($flux) {
	# formulaire : configurer_identite
	if ( $flux['args']['form'] == 'configurer_identite' ) {
		$valeurs = array();
		
		foreach (identite_extra_champs() as $champ) {
			$valeurs['identite_extra'][$champ] = lire_config('identite_extra/' . $champ, '');
		}
		
		$flux['data'] = array_merge($flux['data'], $valeurs);
	}
	
	return $flux;
}


// Mettre à jour la méta
function identite_extra_formulaire_traiter($flux) {
	# formulaire : configurer_identite
	if ($flux['args']['form'] == 'configurer_identite' and $config = _request('identite_extra')) {
		ecrire_config('identite_extra', $config);
	}

	return $flux;
}
