<?php
/*
 * Plugin Momo pour Spip 2.0
 * Code tiré en grande partie de Spip Bisous
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function momo_declarer_tables_interfaces($tables_interface){
	//-- Alias
	$tables_interface['table_des_tables']['momo'] = 'momo';
	$tables_interface['tables_jointures']['spip_mots'][]= 'momo';
	$tables_interface['tables_jointures']['spip_momo'][]= 'mots';
	$tables_interface['exceptions_des_jointures']['id_mot_parent'] = array('spip_momo', 'id_parent');

	return $tables_interface;
}

function momo_declarer_tables_auxiliaires($tables_auxiliaires){
        $spip_momo = array(
            'id_parent' => 'bigint(21) DEFAULT "0" NOT NULL',
            'id_mot' => 'bigint(21) DEFAULT "0" NOT NULL',
        );
       
        $spip_momo_cles = array(
            'PRIMARY KEY' => 'id_parent, id_mot',
            'KEY id_mot' => 'id_mot',
            'KEY id_parent' => 'id_parent'
        );
       
        $tables_auxiliaires['spip_momo'] = array(
            'field' => &$spip_momo,
            'key' => &$spip_momo_cles,
            'join' => array ('id_parent' => 'id_parent','id_mot' => 'id_mot')
        );
       
        return $tables_auxiliaires;
    }


?>
