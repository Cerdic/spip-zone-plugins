<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Retourne les champs spécifique d'un type de restriction.
 *
 * @param string $type_restriction
 *        	Le type de restriction.
 * @param array $valeurs
 *        	Des valeurs par défaut.
 * @param
 *        	array options:
 *        	- champs_specifiques: si oui filtre le tableau pour obtenir uniquement les champs
 *        	spécifiques.
 *
 * @return array Les champs de la restriction.
 */
function lor_definition_saisies($type_restriction, $valeurs = [], $options = []) {
	$restrictions = chercher_definitions_restrictions($valeurs);

	if (is_array($restrictions)) {
		// Chercher les fichiers restrictions
		$type_restrictions_noms = [];
		$restrictions_saisies = [];
		foreach ($restrictions as $nom => $definition) {
			if (isset($definition['saisies'])) {
				$restrictions_saisies[$nom] = [
					[
						'saisie' => 'fieldset',
						'options' => [
							'nom' => 'specifique',
							'label' => _T('restriction:label_parametres_specifiques'),
						],
						'saisies' => $definition['saisies']
					]
				];
			}
		}
	}

	// Obtenir les champs spécifiques
	if ($type_restriction and isset($restrictions_saisies[$type_restriction])) {
		$saisies = $restrictions_saisies[$type_restriction];
	}

	return $saisies;
}

/**
 * Cherche les définitions des restrictions disponibles.
 *
 * @param array $valeurs
 *
 * @return array]
 */
function chercher_definitions_restrictions($valeurs = []) {
	$definitions_restrictions = find_all_in_path("restrictions/", '^');
	$restrictions = [];
	if (is_array($definitions_restrictions)) {
		foreach ($definitions_restrictions as $fichier => $chemin) {
			list ($nom, $extension) = explode('.', $fichier);
			// Charger la définition des champs

			if ($defs = charger_fonction($nom, "restrictions", true)) {
				if (is_string($valeurs)) {
					$valeurs = unserialize($valeurs);
				}
				$restriction = $defs($valeurs);
				$restrictions[$nom] = $restriction;
			}
		}
	}
	return $restrictions;
}

/**
 * Vérifie les trestrictions
 *
 * @param array $erreurs
 *   Les erreurs.
 *
 * @return array
 *   Les erreurs.
 */
function lor_verifier($erreurs = [], $type) {
	include_spip('inc/locations_objets_restrictions');
	$definitions_saisies = chercher_definitions_restrictions();
	$verifier = charger_fonction('verifier', 'inc');
	$objet = _request('location_objet');
	$id_objet = _request('id_location_objet');


	// On détermine les restrictions attachées à l'objet de location.
	$sql = sql_select(
		'type_restriction,valeurs_restriction',
		'spip_restrictions_liens,spip_restrictions ',
		'objet Like' . sql_quote($objet) . ' AND id_objet=' . $id_objet,
		 '',
		 'rang_lien ASC');

	// Pour chaque restriction on vérifie si les valeurs des champs à tester contiennent des erreurs.
	while ($row = sql_fetch($sql)) {

		$type_restriction = $row['type_restriction'];
		$definitions_saisie = $definitions_saisies[$type_restriction];
		$type_verification = $definitions_saisie['verifier']['type'];

		if (isset($definitions_saisie['verifier']['champs']) AND $type_verification == $type) {
			foreach ($definitions_saisie['verifier']['champs'] AS $champ) {
				// S'il n'existe pas déjà d'erreur pour le champ en question,
				// on verifie via la vérification correspondante au type de restriction.
				if (!isset($erreurs[$champ]) AND
					$erreur = $verifier(
						_request($champ),
						$type_restriction . '_' . $champ,
						[
							'valeurs_restriction'=> json_decode($row['valeurs_restriction'], TRUE)
						]
						)) {
					$erreurs[$champ] = $erreur;
				}
			}
		}
	}
	return $erreurs;
}
