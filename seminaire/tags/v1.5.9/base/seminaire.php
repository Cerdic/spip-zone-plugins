<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function seminaire_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'name', // nom sql
		'label' => 'seminaire:name', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_name', //precisions sur le champ name
		'type' => 'ligne', // type de saisie
		'sql' => "varchar(144) NOT NULL DEFAULT ''", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'origin', // nom sql
		'label' => 'seminaire:origin', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_origin', //precisions sur le champ origin
		'type' => 'ligne', // type de saisie
		'sql' => "varchar(144) NOT NULL DEFAULT ''", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'abstract', // nom sql
		'label' => 'seminaire:abstract', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_abstract', //precisions sur le champ abstract		
		'type' => 'bloc', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
		'saisie_parametres' => array(
		'class' => 'inserer_barre_edition'
		)
	));	
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'notes', // nom sql
		'label' => 'seminaire:notes', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_notes', //precisions sur le champ namenotes
		'type' => 'bloc', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
		'saisie_parametres' => array(
		'class' => 'inserer_barre_edition'
		),
	));
	return $champs;
}
?>