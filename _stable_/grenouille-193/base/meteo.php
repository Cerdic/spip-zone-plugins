<?php 
	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
 
	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;
 
	$table_des_tables['meteo'] = 'meteo';
	$table_des_tables['previsions'] = 'previsions';



	$spip_meteo = array(
						"id_meteo"		=> "BIGINT(21) NOT NULL",
						"ville"			=> "VARCHAR(255) NOT NULL",
						"code"			=> "VARCHAR(255) NOT NULL",
						"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"statut"		=> "ENUM('publie','en_erreur') DEFAULT 'en_erreur' NOT NULL",
						"idx"			=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_meteo_key = array(
						"PRIMARY KEY" 	=> "id_meteo"
					);

	$spip_previsions = array(
						"id_prevision"	=> "BIGINT(21) NOT NULL",
						"id_meteo"		=> "BIGINT(21) NOT NULL",
						"date"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"minima"		=> "VARCHAR(255) DEFAULT '' NOT NULL",
						"maxima"		=> "VARCHAR(255) DEFAULT '' NOT NULL",
						"id_temps"		=> "BIGINT(21) DEFAULT '48' NOT NULL",
						"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
					);
	$spip_previsions_key = array(
						"PRIMARY KEY" 	=> "id_prevision"
					);

	$tables_principales['spip_meteo'] =
		array('field' => &$spip_meteo, 'key' => &$spip_meteo_key);
	$tables_principales['spip_previsions'] =
		array('field' => &$spip_previsions, 'key' => &$spip_previsions_key);




?>