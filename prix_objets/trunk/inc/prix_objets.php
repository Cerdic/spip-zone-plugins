<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Retourne les déclarations des champs inséré via la pipeline "prix_objets_extensions"
 *
 * @param array $valeurs
 * @return array
 */
function prix_objets_extensions_declaration($valeurs = array()) {

	return pipeline(
			'prix_objets_extensions', array(
				'data' => array(),
				'args' => $valeurs,
			)
		);
}