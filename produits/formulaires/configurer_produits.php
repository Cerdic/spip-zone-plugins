<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_produits_saisies_dist(){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('produits:configurer_taxe_defaut_label'),
				'defaut' => lire_config('produits/taxe', 0),
			),
			'verifier' => array(
				'type' => 'decimal'
			)
		)
	);
}

?>
