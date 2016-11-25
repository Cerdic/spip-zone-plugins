<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_declarer_champs_extras($champs = array()) {
	$champs['spip_rubriques']['extras_delai']=array(
		'saisie'=>'input',
		'options'=>array(
			'nom'=>'extras_delai',
			'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>false,
			'label' => _T('activite_editoriale:extras_delai_label'), // chaine de langue 'mon_plug:mon_label'
			'explication' => _T('activite_editoriale:extras_delai_explications'), // precisions sur le champ
			'obligatoire' => false, // 'true', 'false' ou ''
		)
	);
	$champs['spip_rubriques']['extras_identifiants'] = array(
	'saisie'=>'input',
		'options'=>array(
			'nom'=>'extras_identifiants',
			'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>false,
			'label' => _T('activite_editoriale:extras_identifiants_label'), // chaine de langue 'mon_plug:mon_label'
			'explication' => _T('activite_editoriale:extras_identifiants_explications'), // precisions sur le champ
			'obligatoire' => false, // 'true', 'false' ou ''
		)
	);
	$champs['spip_rubriques']['extras_frequence'] = array(
	'saisie'=>'input',
		'options'=>array(
			'nom'=>'extras_frequence',
			'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>false,
			'label' => _T('activite_editoriale:extras_frequence_label'), // chaine de langue 'mon_plug:mon_label'
			'explication' => _T('activite_editoriale:extras_frequence_explications'), // precisions sur le champ
			'obligatoire' => false, // 'true', 'false' ou ''
		)
	);
	return $champs;
}
