<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

// Declaration des tables evenements

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;
		
//-- Table ACTIVITES ------------------------------------------
$spip_asso_activites = array(
					"id_activite"		=> "bigint(20) NOT NULL auto_increment",
					"id_evenement"	=> "bigint(20) NOT NULL",
					"nom"				=> "text NOT NULL",
					"id_adherent"		=> "bigint(20) NOT NULL",
					"accompagne"	=> "text NOT NULL",
					"inscrits"			=> "int(11) NOT NULL default '0'",
					"date"				=> "date NOT NULL default '0000-00-00'",
					"telephone"		=> "text NOT NULL",
					"adresse"			=> "text NOT NULL",
					"email"			=> "text NOT NULL",
					"commentaire"	=> "text NOT NULL",
					"montant"			=> "float NOT NULL default '0'",
					"date_paiement"	=> "date NOT NULL default '0000-00-00'",
					"statut"			=> "text NOT NULL",
					"maj"				=> "timestamp(14) NOT NULL"
					);						
$spip_asso_activites_key = array(
						"PRIMARY KEY" => "id_activite"
						);

$tables_principales['spip_asso_activites'] = array(
		'field' => &$spip_asso_activites, 
		'key' => &$spip_asso_activites_key);

//-- Table des tables ----------------------------------------------------

global $table_des_tables;
	$table_des_tables['asso_activites'] = 'asso_activites';
?>