<?php
                               
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_configurer_clients_saisies_dist($retour=''){
return array(
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
				'defaut' => array('complement', 'pays', 'obli_pays'),
				'datas' => array(
							'civilite' => _T('contacts:label_civilite'),
							'obli_civilite' => _T('clients:label_obligatoire'),
							'numero' => _T('clients:label_tel'),
							'obli_numero' => _T('clients:label_obligatoire'),
							'complement' => _T('coordonnees:label_complement'),
							'pays' => _T('coordonnees:label_pays'),
							'obli_pays' => _T('clients:label_obligatoire')
							)
			)
		),
		array(
			'saisie' => 'checkbox',
			'editable' => (!in_array('civilite',lire_config('clients/elm',array()))) ? false : true,
			'options' => array(
				'nom' => 'elm_civ',
				'label' => _T('clients:label_elm_civ'),
				//'defaut' => array('m', 'mme'),
				'datas' => array(
							'm' => _T('clients:label_monsieur'),
							'mme' => _T('clients:label_madame'),
							'melle' => _T('clients:label_mademoiselle'),
							'dr' => _T('clients:label_docteur')
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
