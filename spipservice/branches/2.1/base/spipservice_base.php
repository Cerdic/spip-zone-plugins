<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 2.1                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

// Pipelines.
// Objectifs : 
//	- Declarer et ajouter des tables dans la base de donnees
// Voir la doc suivante : http://code.spip.net/@Ajouter-des-tables-et-des-boucles
//
// Attention, il est imperatif de distinguer :
//	- ce qu'on appelle une *table* : son id ou son nom complet (ex: spip_evenements)
//	- ce qu'on appelle le *nom* d'une table : son diminutif (ex: evenements)
//	- ce qu'on appelle un *objet* : nom de la table sans suffixe et au singuler (ex: evenement)

//spip_log("- Fichier 'base/spipservice_base.php' lu.", "spipservice");


if (!defined("_ECRIRE_INC_VERSION")) return;



// Declaration des tables principales
function spipservice_declarer_tables_principales($tables_principales){

	//spip_log('---> delcaration des tables principales','spipservice');
	
	// Table 'spip_spipservice'
	$spipservice = array(
        "id_spipservice" 	=> "bigint(21) NOT NULL auto_increment",
        "id"   				=> "bigint(21) NULL",
        "type"    			=> "varchar(25) NULL",
        "id_auteur"    		=> "bigint(21) NULL",
        "action"          	=> "varchar(255) NULL",
        "date"          	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
	);
    
    // champs qui possede les cles
	$spipservice_key = array(
        "PRIMARY KEY"     => "id_spipservice"
	);

    // champs candidats a la jointure
	$spipservice_join = array(
	    "id_spipservice" => "id_spipservice"
	);
	
	// Table des tables
	$tables_principales['spip_spipservice'] = array(
		'field' => &$spipservice,
		'key' => &$spipservice_key,
		'join' => &$spipservice_join
	);	

	//spip_log('---> delcaration des tables principales OK','spipservice');

	return $tables_principales;
}

function spipservice_declarer_tables_objets_surnoms($surnoms) {
	//spip_log('---> delcaration des surnoms','spipservice');
	
	// Le type 'spipservice' correspond a la table nommee 'spipservice'
	$surnoms['spipservice'] = 'spipservice';
	
	//spip_log('---> delcaration des surnoms OK','spipservice');
	
	return $surnoms;
}


?>
