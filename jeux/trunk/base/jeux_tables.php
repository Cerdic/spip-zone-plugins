<?php

// declaration des tables du plugin jeux //
global $table_des_tables;
global $tables_principales;
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_jointures;

$table_des_tables['jeux'] = 'jeux';
$table_des_tables['jeux_resultats'] = 'jeux_resultats';

function jeux_declarer_tables_objets_sql($table){
    $table['spip_jeux'] = array(
    'principale' =>"oui",
    'field'     => array(
	       'id_jeu'        => 'bigint(21) NOT NULL',
	       'date'          => 'timestamp',
	       'type_jeu'      => 'TEXT NOT NULL',
	       'titre'         => 'TEXT NOT NULL',
	       'texte'       => 'TEXT NOT NULL',
	       'statut'        => "varchar(255) DEFAULT '0' NOT NULL",
	       'type_resultat' =>"varchar(10) DEFAULT '0' NOT NULL",
	       'resultat_unique'=>"VARCHAR(10) NOT NULL DEFAULT 'non'"
        ),
     'date'     => 'date',
     'titre'    => "titre, '' AS lang",
     'key'      =>  array('PRIMARY KEY' =>'id_jeu'),
     'statut_textes_instituer' => 	array(
	       'prepa' => 'texte_statut_en_cours_redaction',
	       'prop' => 'texte_statut_propose_evaluation',
	       'publie' => 'texte_statut_publie',
	       'refuse' => 'texte_statut_refuse',
	       'poubelle' => 'texte_statut_poubelle',
      ),
    'statut'=> array(
	   array(
		'champ' => 'statut',
		'publie' => 'publie',
		'previsu' => 'publie,prop,prepa',
		'post_date' => 'date',	
		'exception' => array('statut','tout')
	       )
        ),
     'texte_changer_statut'=>'jeu:texte_changer_statut_jeu',
     'champs_editables'  => array('titre', 'texte','type_resultat'),
     'champs_versionnes' => array('titre', 'texte'),
     'champs_contenu' => array('texte')
     );
  
    return $table;
}

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