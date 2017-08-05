<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * @param       $noisette
 * @param array $options
 *
 * @return array
 */
function noisette_phraser($noisette, $options = array()) {

	// Initialisation de la description
	$description = array();

	// Initialiser le contexte de chargement
	if (!isset($options['dossier'])) {
		$options['dossier'] = 'noisettes/';
	}
	if (!isset($options['recharger'])) {
		$options['recharger'] = false;
	}
	if (!isset($options['md5']) or $options['recharger']) {
		$options['md5'] = '';
	}

	// Initialiser les composants de l'identifiant de la noisette:
	// - type-noisette si la noisette est dédiée uniquement à une page
	// - type-composition-noisette si la noisette est dédiée uniquement à une composition
	// - noisette sinon
	$type = '';
	$composition = '';
	$identifiants = explode('-', $noisette);
	if (isset($identifiants[1])) {
		$type = $identifiants[0];
	}
	if (isset($identifiants[2])) {
		$composition = $identifiants[1];
	}

	// Initialisation de la description par défaut de la page
	$description_defaut = array(
		'noisette'       => $noisette,
		'type'           => $type,
		'composition'    => $composition,
		'nom'            => $noisette,
		'description'    => '',
		'icon'           => 'noisette-24.png',
		'necessite'      => array(),
		'contexte'       => array(),
		'ajax'           => 'defaut',
		'inclusion'      => 'statique',
		'parametres'     => array(),
		'signature'      => '',
	);

	// Recherche des noisettes par leur fichier YAML uniquement.
	$md5 = '';
	$fichier = isset($options['yaml']) ? $options['yaml'] : find_in_path("{$options['dossier']}${noisette}.yaml");
	if ($fichier) {
		// il y a un fichier YAML de configuration, on vérifie le md5 avant de charger le contenu.
		$md5 = md5_file($fichier);
		if ($md5 != $options['md5']) {
			include_spip('inc/yaml');
			$description = yaml_charger_inclusions(yaml_decode_file($fichier));
			// Traitements des champs pouvant être soit une chaine soit un tableau
			if (!empty($description['necessite']) and is_string($description['necessite'])) {
				$description['necessite'] = array($description['necessite']);
			}
			if (!empty($description['contexte']) and is_string($description['contexte'])) {
				$description['contexte'] = array($description['contexte']);
			}
		}
	}

	// Si la description est remplie c'est que le chargement a correctement eu lieu.
	// Sinon, si la noisette n'a pas changée on renvoie une description limitée à un indicateur d'identité pour
	// distinguer ce cas avec une erreur de chargement qui renvoie une description vide.
	if ($description) {
		// Mise à jour du md5
		$description['signature'] = $md5;
		// Complétude de la description avec les valeurs par défaut
		$description = array_merge($description_defaut, $description);
		// Sérialisation des champs necessite, contexte et parametres qui sont des tableaux
		$description['necessite'] = serialize($description['necessite']);
		$description['contexte'] = serialize($description['contexte']);
		$description['parametres'] = serialize($description['parametres']);
	} elseif ($md5 == $options['md5']) {
		$description['identique'] = true;
	}

	return $description;
}
