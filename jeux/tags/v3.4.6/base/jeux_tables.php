<?php
// declaration des tables du plugin jeux //
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function jeux_declarer_tables_objets_sql($table){
	$table['spip_jeux'] = array(
		'principale' => 'oui',
		'type'=>'jeu',
		'field' => array(
			'id_jeu'          => 'bigint(21) NOT NULL',
			'date'            => 'timestamp',
			'type_jeu'        => 'TEXT NOT NULL',
			'titre_prive'     => 'TEXT NOT NULL',
			'texte'           => 'TEXT NOT NULL',
			'statut'          => 'varchar(255) DEFAULT "0" NOT NULL',
			'type_resultat'   => 'varchar(10) DEFAULT "0" NOT NULL',
			'resultat_unique' => 'VARCHAR(10) NOT NULL DEFAULT "non"'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_jeu',
			'KEY id_jeu'  => 'id_jeu',
		),
		'join' => array(
			'id_jeu' => 'id_jeu',
		),
		'date' => 'date',
		'titre' => 'titre_prive AS titre, "" AS lang',
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date',	
				'exception' => array('statut', 'tout')
			)
		),
		'texte_changer_statut'=>'jeu:texte_changer_statut_jeu',
		'champs_editables'  => array('titre_prive', 'texte','type_resultat'),
		'champs_versionnes' => array('titre_prive', 'texte'),
		'champs_contenu' => array('type_jeu','texte','type_resultat')
	);

	return $table;
}


function jeux_declarer_tables_principales($tables_principales){
	$jeux_resultats = array(
	   'id_resultat'    => 'bigint(21) NOT NULL',
	   'id_jeu'		    => 'bigint(21) NOT NULL',
	   'id_auteur'		=> 'bigint(21) NOT NULL',
	   'date'			=> 'timestamp',
	   'resultat_court' => 'int(12)',
	   'resultat_long'  => 'text NOT NULL',
	   'total'			=> 'int(12) NOT NULL'
	);
	$jeux_resultats_key = array(
		'PRIMARY KEY'   => 'id_resultat',
		'KEY id_jeu'    => 'id_jeu',
		'KEY id_auteur' => 'id_auteur',
	);
	$jeux_resultats_join = array(
		'id_jeu'    => 'id_jeu',
		'id_auteur' => 'id_auteur',
	);
	
	$tables_principales['spip_jeux_resultats'] = array(
		'field' => $jeux_resultats,
		'key'   => $jeux_resultats_key,
		'join'  => $jeux_resultats_join,
	);
	
	return $tables_principales;
}

function jeux_declarer_tables_interfaces($tables){
	$tables['table_des_tables']['jeux'] = 'jeux';
	$tables['table_des_tables']['jeux_resultats'] = 'jeux_resultats';

	$table_des_traitements = &$tables['table_des_traitements'];
	// $tables['table_des_traitements']['TEXTE']['jeux']= 'propre(%s)';
	
	if (!isset($table_des_traitements['TITRE_PUBLIC'])) {
		$table_des_traitements['TITRE_PUBLIC'] = $table_des_traitements['TITRE'];
	}
	if (!isset($table_des_traitements['TITRE_PRIVE'])) {
		$table_des_traitements['TITRE_PRIVE'] = $table_des_traitements['TITRE'];
	}
	if (!isset($table_des_traitements['TEXTE_JEU'])) {
		$table_des_traitements['TEXTE_JEU'] = $table_des_traitements['TEXTE'];
	}
	
	return $tables;
}
