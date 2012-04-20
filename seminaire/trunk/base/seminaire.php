<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function seminaire_declarer_champs_extras($champs = array()){
	$champs['spip_evenements']['name'] = array(
		'saisie' => 'input',// type de saisie
		'options' => array(
			'nom' => 'name',
			'label' => _T('seminaire:name'), 
			'sql' => "varchar(256) NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',	
	));
	$champs['spip_evenements']['origin'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'origin', // nom sql
			'label' => _T('seminaire:origin'), 
			'sql' => "varchar(256) NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',	
	));
	$champs['spip_evenements']['abstract'] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'abstract', // nom sql
			'label' => _T('seminaire:abstract'), 
			'sql' => "text NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',	
			'rows' => 4,
			'traitements' => '_TRAITEMENT_RACCOURCIS',
			'class'	=>'inserer_barre_edition',
	));	
	$champs['spip_evenements']['notes'] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'notes', // nom sql
			'label' => _T('seminaire:notes'), 
			'sql' => "text NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',	
			'rows' => 4,
			'traitements' => '_TRAITEMENT_RACCOURCIS',
			'class'	=>'inserer_barre_edition',
	));
	
	return $champs;
}
?>