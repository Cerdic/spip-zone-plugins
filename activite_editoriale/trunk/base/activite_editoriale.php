<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_declarer_champs_extras($champs = array()) {
	$champs['spip_rubriques']['activite_editoriale'] = array(
		'saisie' => 'fieldset',
			'options' => array(
			'nom' => _T('activite_editoriale:activite_editoriale'),
			'label' => _T('activite_editoriale:activite_editoriale_label')
		),
		'saisies' => array(
			'extras_delai' => array(
				'saisie' => 'input',
				'options' => array(
					'nom'=>'extras_delai',
					'sql' => "tinytext NOT NULL DEFAULT ''",
					'rechercher' => false,
					'label' => _T('activite_editoriale:extras_delai_label'), 
					'explication' => _T('activite_editoriale:extras_delai_explications'),
					'obligatoire' => false, // 'true', 'false' ou ''
			)),
			'extras_identifiants' => array(
				'saisie' => 'input',
				'options' => array(
					'nom'=>'extras_identifiants',
					'sql' => "tinytext NOT NULL DEFAULT ''",
					'rechercher' => false,
					'label' => _T('activite_editoriale:extras_identifiants_label'),
					'explication' => _T('activite_editoriale:extras_identifiants_explications'),
					'obligatoire' => false, // 'true', 'false' ou ''
			)),
			'extras_emails' => array(
				'saisie' => 'input',
				'options' => array(
					'nom'=>'extras_emails',
					'sql' => "tinytext NOT NULL DEFAULT ''",
					'rechercher' => false,
					'label' => _T('activite_editoriale:extras_emails_label'),
					'explication' => _T('activite_editoriale:extras_emails_explications'),
					'obligatoire' => false, // 'true', 'false' ou ''
			)),
			'extras_frequence' => array(
				'saisie' => 'input',
				'options' => array(
					'nom'=>'extras_frequence',
					'sql' => "tinytext NOT NULL DEFAULT ''",
					'rechercher' => false,
					'label' => _T('activite_editoriale:extras_frequence_label'),
					'explication' => _T('activite_editoriale:extras_frequence_explications'),
					'obligatoire' => false, // 'true', 'false' ou ''
			))
		)
	);
	return $champs;
}