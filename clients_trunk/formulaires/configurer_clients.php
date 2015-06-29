<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/config');

function formulaires_configurer_clients_saisies_dist($retour=''){
$civilite = charger_fonction('civilite','inc');

return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_elements',
				'label' => _T('clients:configurer_titre_elements')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('clients:texte_exp1')
						)
					),
				array(
					'saisie' => 'checkbox',
					'options' => array(
						'nom' => 'elm',
						'label' => _T('clients:label_elm'),
						'choix' => 'ligne',
						'defaut' => array('complement', 'pays', 'obli_pays'),
						'datas' => array(
									'civilite' => _T('contacts:label_civilite'),
									'obli_civilite' => _T('clients:label_obligatoire'),
									'numero' => _T('clients:label_tel'),
									'obli_numero' => _T('clients:label_obligatoire'),
									'portable' => _T('clients:label_portable'),
									'obli_portable' => _T('clients:label_obligatoire'),
									'fax' => _T('clients:label_fax'),
									'obli_fax' => _T('clients:label_obligatoire'),
									'complement' => _T('coordonnees:label_complement'),
									'pays' => _T('coordonnees:label_pays'),
									'obli_pays' => _T('clients:label_obligatoire')
									)
					)
				),
				array(
					'saisie' => 'radio',
					'editable' => (in_array('civilite',lire_config('clients/elm',array()))) ? true : false,
					'options' => array(
						'nom' => 'type_civ',
						'label' => _T('clients:label_type_civ'),
						//'defaut' => array(),
						'datas' => array(
									'i' => _T('clients:label_input'),
									'c' => _T('clients:label_checkbox')
									)
						)
				),
				array(
					'saisie' => 'checkbox',
					'editable' => (lire_config('clients/type_civ')=='c') ? true : false,
					'options' => array(
						'nom' => 'elm_civ',
						'label' => _T('clients:label_elm_civ'),
						'explication' => _T('clients:explication_type_civ'),
						//'defaut' => array('madame', 'monsieur'),
						'datas' => $civilite()
						)
				)
			)
		)
	);
}

function formulaires_configurer_clients_charger_dist($retour=''){
	$contexte = lire_config('clients');	
	if(!is_array($contexte))$contexte = array();

	return $contexte;
}

?>
