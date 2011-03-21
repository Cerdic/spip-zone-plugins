<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_produits_saisies_dist(){
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('produits:configurer_taxe_defaut_label'),
				'defaut' => 0,
			),
			'verifier' => array(
				'type' => 'decimal'
			)
		)
	);
}

?>
