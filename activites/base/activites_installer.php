<?php

// Declaration des tables evenements

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;


//creer la table auteurs_elargis si besoin
$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
$spip_auteurs_elargis['prenom'] = "text";
$spip_auteurs_elargis['naissance'] = "date NOT NULL default 0000-00-00";
$spip_auteurs_elargis['sexe'] = "text";
$spip_auteurs_elargis['adresse'] = "text";
$spip_auteurs_elargis['code_postal'] = "text";
$spip_auteurs_elargis['ville'] = "text";
$spip_auteurs_elargis['telephone'] = "text";
$spip_auteurs_elargis['mobile'] = "text";
$spip_auteurs_elargis['publication'] = "text";
$spip_auteurs_elargis['`spip_pref_contact`'] = "VARCHAR( 21 ) DEFAULT 'aucune' NOT NULL";
$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id_auteur");
$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	


//-- Table ACTIVITES ------------------------------------------
$spip_activites = array(
					"id_activite"		=> "bigint(20) NOT NULL auto_increment",
					"id_evenement"		=> "bigint(20) NOT NULL default '0'",
					"renseigenements"	=> "text",
					"nbre_inscrits"		=> "int(11) NOT NULL default '0'",
					"delais_inscription"	=> "date NOT NULL default '0000-00-00'",
					"prix"				=> "float NOT NULL default '0'",
					"delais_paiement"	=> "date NOT NULL default '0000-00-00'",
					"statut"			=> "enum('a_confirmer', 'a_lieu', 'annule') NOT NULL default 'a_confirmer'",
					"maj"				=> "timestamp(14) NOT NULL"
					);						
$spip_activites_key = array(
						"PRIMARY KEY" => "id_activite",
						"KEY id_evenement" => "id_evenement",
						"KEY id_article" =>"id_article"
						);

$tables_principales['spip_activites'] = 
	array('field' => &$spip_activites, 'key' => &$spip_activites_key);



//-- Table INSCRIPTIONS ------------------------------------------
$spip_inscriptions = array(
					"id_inscription"	=> "bigint(20) NOT NULL auto_increment", //cette ligne est-elle nécessaire ?
					"id_auteur"			=> "bigint(21) NOT NULL default '0'",
					"id_activite" 		=> "bigint(21) NOT NULL default '0'",
					"contact"			=> "enum('courriel','courrier','tel') NOT NULL default 'courriel'"
					"date_inscription"	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
					"statut"			=> "enum('a_valider','valide') NOT NULL default 'a_valider'",
					"date_paiement"		=> "date NOT NULL default '0000-00-00'"
				);
$spip_inscriptions_key = array(
					"PRIMARY KEY" => "id_auteur, id_activite"
				);


$tables_principales['spip_inscriptions'] = 
	array('field' => &$spip_inscriptions, 'key' => &$spip_inscriptions_key);

//-- Table des tables ----------------------------------------------------

	global $table_des_tables;
	$table_des_tables['activites'] = 'activites';
	$table_des_tables['inscriptions'] = 'inscriptions';



//lier les tables auteurs et activites et les tables articles et activites

?>