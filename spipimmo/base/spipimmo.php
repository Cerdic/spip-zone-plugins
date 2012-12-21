<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

function spipimmo_declarer_tables_interfaces($interface)
{
	$interface['table_des_tables']['spipimmo_annonces']='spipimmo_annonces';
	$interface['table_des_tables']['spipimmo_documents_annonces']='spipimmo_documents_annonces';
	$interface['table_des_tables']['spipimmo_types_offres']='spipimmo_types_offres';
	$interface['table_des_traitements']['LIBELLE_OFFRE'][] = _TRAITEMENT_RACCOURCIS;
	return $interface;
}

function spipimmo_declarer_tables_principales($tables_principales)
{
	//Table des spipimmo_annonces
	$spip_spipimmo_annonces_field=array(
		"id_annonce"	=>	"int(10) unsigned NOT NULL auto_increment",
		"vente_location"=>	"varchar(8)",
		"numero_mandat"		=>	"varchar(255)",
		"type_mandat"	=>	"varchar(50)",
		"negociateur"	=>	"varchar(255)",
		"honoraires"	=>	"int(30)",
		"publier"		=>	"bool",
		"adresse_1"	=>	"longtext",
		"adresse_2"	=>	"longtext",
		"code_postal"		=>	"varchar(10)",
		"ville"	=>	"varchar(255)",
		"prix_loyer"	=>	"int(30)",
		"taxe_habitation"=>"int(10)",
		"taxe_fonciere"	=>	"int(10)",
		"travaux"		=>	"int(10)",
		"charges"		=>	"int(10)",
		"dpe"			=>	"varchar(3)",
		"depot_garantie"=>	"int(10)",
		"date_annonce"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_modif"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_dispo"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"quartier"		=>	"varchar(255)",
		"secteur"		=>	"varchar(255)",
		"residence"		=>	"varchar(255)",
		"transport"		=>	"varchar(255)",
		"proximite"		=>	"varchar(255)",
		"annee_construction"	=>	"varchar(255)",
		"nbr_pieces"		=>	"int(2)",
		"nbr_chambres"	=>	"int(2)",
		"nbr_etage"		=>	"int(2)",
		"nbr_wc"			=>	"int(2)",
		"nbr_sdb"		=>	"int(2)",
		"nbr_sde"		=>	"int(2)",
		"nbr_balcon"		=>	"int(2)",
		"nbr_terrasse"		=>	"int(2)",
		"nbr_mur_mit"	=>	"int(1)",
		"nbr_park_int"	=>	"int(2)",
		"nbr_park_ext"	=>	"int(2)",
		"nbr_garage"	=>	"int(2)",
		"nbr_cave"	=>	"int(2)",
		"surf_facade"	=>	"int(6)",
		"surf_habitable"	=>	"int(6)",
		"surf_sejour"	=>	"int(6)",
		"surf_terrain"	=>	"int(6)",
		"surf_total"	=>	"int(6)",
		"etage"			=>	"int(2)",
		"etage_code"			=>	"int(2)",
		"type_cuisine"	=>	"varchar(255)",
		"type_soussol"	=>	"varchar(255)",
		"type_chauffage"	=>	"varchar(255)",
		"nat_chauffage"		=>	"varchar(255)",
		"prestige"		=>	"bool",
		"ascenseur"		=>	"bool",
		"piscine"		=>	"bool",
		"acces_handicape"	=>	"bool",
		"annonce"	=>	"longtext",);
		
	$spip_spipimmo_annonces_key=array(
		"PRIMARY KEY" => "id_annonce");

	$tables_principales['spip_spipimmo_annonces']=array(
		'field' => &$spip_spipimmo_annonces_field,
		'key' => &$spip_spipimmo_annonces_key);

	//Table des documents
	$spip_spipimmo_documents_annonces_field=array(
		"id_document"	=>	"int(10) unsigned NOT NULL auto_increment",
		"numero_dossier"=>	"int(50)",
		"fichier"		=>	"varchar(255)",
		"taille"		=>	"int(11)",
		"type"			=>	"bool");

	$spip_spipimmo_documents_annonces_key=array(
		"PRIMARY KEY" => "id_document");

	$tables_principales['spip_spipimmo_documents_annonces']=array(
		'field' => &$spip_spipimmo_documents_annonces_field,
		'key' => &$spip_spipimmo_documents_annonces_key);

	//Table du type d'offre
	$spip_spipimmo_types_offres_field=array(
		"id_type_offre"	=>	"smallint(6) NOT NULL auto_increment",
		"libelle_offre"	=>	"text NOT NULL default ''");

	$spip_spipimmo_types_offres_key=array(
		"PRIMARY KEY" => "id_type_offre");

	$tables_principales['spip_spipimmo_types_offres']=array(
		'field' => &$spip_spipimmo_types_offres_field,
		'key' => &$spip_spipimmo_types_offres_key);

	return $tables_principales;
}
?>
