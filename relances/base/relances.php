<?php
/**
* Plugin abonnement
*
* Copyright (c) 2013
* Collectif SPIP
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

function relance_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['relances'] = 'relances';
	$interface['table_des_tables']['relances_archives'] = 'relances_archives';

	$interface['table_des_traitements']['TEXTE']['relances'] = _TRAITEMENT_RACCOURCIS; // + raccourcis spip
	
	$interface['table_date']['relances_archives'] = 'date';
	
	return $interface;
}

function relance_declarer_tables_principales($tables_principales){
 
	//table spip_relances
	$relances = array(
		'id_relance' => 'bigint(21) not null',
		'titre' => "tinytext DEFAULT '' NOT NULL",
		'texte' => "text DEFAULT '' NOT NULL",
		'duree'	=> 'int(11) NOT NULL DEFAULT 0',
		'periode' => 'varchar(25) NOT NULL DEFAULT ""',
		'quand' => "ENUM( 'avant', 'apres' ) NOT NULL DEFAULT 'avant'",
		);
	
	$relances_key = array(
                'PRIMARY KEY' => 'id_relance',
                );
        
	$tables_principales['spip_relances'] =  array(
		'field' => &$relances, 
		'key' => &$relances_key,
		'join'=> array(
		'id_commande' => 'id_commande'
		)
		);
	
	//table spip_relances_archives
	$relances_archives = array(
		'id_relances_archive'	=> 'bigint(21) not null',
		'id_relance' => 'bigint(21) DEFAULT 0 NOT NULL',
		'id_abonnement' => 'bigint(21) DEFAULT 0 NOT NULL',
		'id_auteur' => 'bigint(21) DEFAULT 0 NOT NULL',
		'date' => 'datetime not null default "0000-00-00 00:00:00"',
		);
	
	$relances_archives_key = array(
                'PRIMARY KEY' => 'id_relances_archive',
                'KEY id_relance' => 'id_relance'
                );
        
	$tables_principales['spip_relances_archives'] =  array(
		'field' => &$relances_archives, 
		'key' => &$relances_archives_key,
		'join'=> array(
			'id_relances_archive' => 'id_relances_archive',
			'id_relance' => 'id_relance',
			'id_auteur' => 'id_auteur'
		)
	);
	
	return $tables_principales;
}

?>
