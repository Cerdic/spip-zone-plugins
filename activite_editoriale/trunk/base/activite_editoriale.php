<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_declarer_champs_extras($champs = array()){
	$champs['spip_rubriques']['extras_delai']=array(
		'saisie'=>'input',
		'options'=>array(
			'nom'=>'extras_delai',
			'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>false,
            'label' => _T('activite_editoriale:delai'), // chaine de langue 'mon_plug:mon_label'
            'explication' => 'activite_editoriale:delai_precisions', // precisions sur le champ
            'obligatoire' => false, // 'true', 'false' ou ''
            )
			);
	$champs['spip_rubriques']['extras_identifiants'] = array(
	'saisie'=>'input',
		'options'=>array(
			'nom'=>'extras_identifiants',
			'rechercher'=>false,
            'label' => _T('activite_editoriale:identifiants'), // chaine de langue 'mon_plug:mon_label'
            'explication' => 'activite_editoriale:identifiants_precisions', // precisions sur le champ
            'obligatoire' => false, // 'true', 'false' ou ''
			'sql' => "tinytext NOT NULL DEFAULT ''") // declaration sql
	);
	return $champs;
}

?>
