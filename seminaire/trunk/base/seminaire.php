<?php
/**
 * Plugin Séminaires
 * 
 * @package SPIP\Seminaires\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline declarer_champs_extras (Plugin Champs Extras)
 * 
 * On ajoute trois champs à la table spip_evenements :
 * -* attendee
 * -* origin
 * -* notes
 * 
 * @param array $champs
 * 	La liste des champs extras
 * @return array $champs
 * 	La liste des champs extras complétés
 */
function seminaire_declarer_champs_extras($champs = array()){
	$champs['spip_articles']['seminaire'] = array(
		'saisie' => 'oui_non',// type de saisie
		'options' => array(
			'nom' => 'seminaire',
			'label' => _T('seminaire:label_seminaire'), 
			'sql' => "varchar(3) NOT NULL DEFAULT 'non'", // declaration sql
			'rechercher'=>false
	));
	$champs['spip_evenements']['attendee'] = array(
		'saisie' => 'input',// type de saisie
		'options' => array(
			'nom' => 'attendee',
			'label' => _T('seminaire:attendee'), 
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