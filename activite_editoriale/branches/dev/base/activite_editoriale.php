<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_declarer_champs_extras($champs = array()) {
	$champs['spip_rubriques']['activite_editoriale'] = array(
		'saisie' => 'fieldset',//Type du champ (voir plugin Saisies)
			'options' => array(
			'nom' => _T('activite_editoriale:activite_editoriale'),
			'label' => _T('activite_editoriale:activite_editoriale_label')
		),
		'saisies' => array(
			'extras_delai' => array(
				'saisie' => 'input', // Type du champs (voir plugin Saisies)
				'options' => array(
					'nom'=>'extras_delai',
					'sql' => 'tinytext NOT NULL DEFAULT ""', // declaration sql
					'rechercher'=>false,
					'label' => _T('activite_editoriale:extras_delai_label'), // chaine de langue 'mon_plug:mon_label
					'explication' => _T('activite_editoriale:extras_delai_explications'), // precisions sur le champ
					'obligatoire' => false, // 'true', 'false' ou ''
			)),
			'extras_identifiants' => array(
				'saisie' => 'input', // Type du champs (voir plugin Saisies)
				'options' => array(
					'nom'=>'extras_identifiants',
					'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
					'rechercher'=>false,
					'label' => _T('activite_editoriale:extras_identifiants_label'), // chaine de langue 'mon_plug:mon_label'
					'explication' => _T('activite_editoriale:extras_identifiants_explications'), // precisions sur le champ
					'obligatoire' => false, // 'true', 'false' ou ''
			)),
			'extras_frequence' => array(
				'saisie' => 'input', // Type du champs (voir plugin Saisies)
				'options' => array(
					'nom'=>'extras_frequence',
					'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
					'rechercher'=>false,
					'label' => _T('activite_editoriale:extras_frequence_label'), // chaine de langue 'mon_plug:mon_label'
					'explication' => _T('activite_editoriale:extras_frequence_explications'), // precisions sur le champ
					'obligatoire' => false, // 'true', 'false' ou ''
			))
		)
	);
	return $champs;
}
