<?php

// declaration des tables du plugin jeux //
global $table_des_tables;
global $tables_principales;
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_jointures;

$table_des_tables['jeux'] = 'jeux';
$table_des_tables['jeux_resultats'] = 'jeux_resultats';
$jeux = array(
	'id_jeu' => 'bigint(21) NOT NULL',
	'date'		=> 'timestamp');
if (isset($GLOBALS['meta']['jeux_base_version'])) {
	$jeux = array_merge($jeux, array(
		'type_jeu' => 'text NOT NULL',
		'titre_prive' => 'text NOT NULL',
	));
}
$jeux = array_merge($jeux, array(
	'contenu' => 'text NOT NULL',
	'statut' => "varchar(10) DEFAULT '0' NOT NULL",
	'type_resultat'=>"varchar(10) DEFAULT '0' NOT NULL"
	)
);

$jeux_key = array(
	'PRIMARY KEY' =>'id_jeu');
$jeux_resultats = array(
	'id_resultat' => 'bigint(21) NOT NULL',
	'id_jeu'		=> 'bigint(21) NOT NULL',
	'id_auteur'		=> 'bigint(21) NOT NULL',
	'date'			=>	'timestamp',
	'resultat_court' =>	'int(12)',
	'resultat_long' =>	'text NOT NULL',
	'total'			=>	'int(12) NOT NULL'
	);
$jeux_resultats_key=array('PRIMARY KEY' =>'id_resultat',
	'KEY id_jeu' =>'id_jeu',
	'KEY id_auteur' =>'id_auteur'
);

$tables_principales['spip_jeux'] =
	array('field' => &$jeux, 'key' => &$jeux_key);
$tables_principales['spip_jeux_resultats'] =
	array('field' => &$jeux_resultats, 'key' => &$jeux_resultats_key);


global $table_des_traitements;
$table_des_traitements['CONTENU'][]= 'propre(%s)';

// Declarations pour la corbeille (plugin Corbeille, ou Couteau Suisse)
global $corbeille_params;
$corbeille_params['jeux'] = array (
	"statut" => 'poubelle',
	"tableliee"=> array('spip_jeux_resultats'),
	"libelle" => 'jeux:jeux',
);

?>