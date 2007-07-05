<?php
/**
* Plugin Abonnement
*
* Copyright (c) 2007
* BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

$table_des_tables['abonnements'] = 'abonnements';

//-- Table CATEGORIES COTISATION ------------------------------------------
$spip_abonnements = array(
						"id_abonnement" 	=> "int(10) unsigned NOT NULL auto_increment",
						"libelle" 			=> "text NOT NULL",
						"duree" 			=> "text NOT NULL",
						"montant" 		=> "float NOT NULL default '0'",
						"commentaires" 	=> "text NOT NULL",
						"maj" 				=> "timestamp(14) NOT NULL"
						);

$spip_abonnements_key = array(
						"PRIMARY KEY" => "id_abonnement"
						);	

$tables_principales['spip_abonnements'] = array(
		'field' => &$spip_abonnements, 
		'key' => &$spip_abonnements_key);

?>