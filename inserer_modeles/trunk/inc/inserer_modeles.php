<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister les formulaires de modeles disponibles dans les dossiers modeles/
 *
 * @staticvar array $liste_formulaires_modeles
 * @return array
 */
function inserer_modeles_lister_formulaires_modeles() {
	static $liste_formulaires_modeles = false;

	if ($liste_formulaires_modeles === false) {
		$liste_formulaires_modeles = array();
		$match = '[^-]*[.]yaml$';
		$liste = find_all_in_path('modeles/', $match);

		if (count($liste)) {
			include_spip('inc/yaml');
			foreach ($liste as $formulaire => $chemin) {
				$yaml_data = yaml_charger_inclusions(yaml_decode_file($chemin));
				if (is_array($yaml_data)) {
					$liste_formulaires_modeles[$formulaire] = yaml_charger_inclusions(yaml_decode_file($chemin));
				}
			}
		}
	}

	return $liste_formulaires_modeles;
}

/**
 * Charger les informations d'un formulaire de modele
 *
 * @staticvar array $infos_formulaires_modeles
 * @return array
 */
function charger_infos_formulaire_modele($formulaire) {
	static $infos_formulaires_modeles = array();

	if (!isset($infos_formulaires_modeles[$formulaire])) {
		if (substr($formulaire, -5) != '.yaml') {
			$formulaire .= '.yaml';
		}
		if ($chemin = find_in_path($formulaire, 'modeles/')) {
			include_spip('inc/yaml');
			$infos_formulaires_modeles[$formulaire] = yaml_charger_inclusions(yaml_decode_file($chemin));
		}
	}

	return $infos_formulaires_modeles[$formulaire];
}
