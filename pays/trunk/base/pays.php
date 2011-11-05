<?php
/**
 * Plugin Pays pour Spip 2.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pays_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['pays'] = 'pays';
	$interface['table_des_tables']['geo_pays'] = 'pays'; // en attendant une meilleure collaboration avec 'geographie'
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}

function pays_declarer_tables_objets_sql($tables){

$tables['spip_pays'] = array(
	
		'principale' => "oui",
		'field'=> array(
			"id_pays"		=> "smallint(6) NOT NULL auto_increment",
			"code"			=> "varchar(2) NOT NULL default ''",
			"nom"			=> "text NOT NULL default ''",
			"maj"			=> "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_pays",
			"UNIQUE KEY code"	=> "code"
		),
		'champs_editables' => array(
			"code", "nom"
		),
		'titre' => "nom AS titre, '' AS lang",
		'type' => "pay",

		'texte_modifier' => "pays:icone_modifier_pays",
		'texte_creer' => "pays:icone_creer_pays",
		'texte_objet' => "pays:titre_pays",
		'texte_objets' => "pays:titre_pays",
		'info_aucun_objet' => "pays:info_aucun_pays",
		'info_1_objet' => "pays:info_1_pays",
		'info_nb_objets' => "pays:info_nb_pays",
		'texte_logo_objet' => "pays:texte_logo_pays"
		
	
	);
	
	return $tables;


}

?>
