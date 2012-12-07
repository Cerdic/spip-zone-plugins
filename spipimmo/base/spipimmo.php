<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
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
	return $interface;
}

function spipimmo_declarer_tables_principales($tables_principales)
{
	//Table des spipimmo_annonces
	$spip_spipimmo_annonces_field=array(
		"id_annonce"	=>	"int(10) unsigned NOT NULL auto_increment",
		"publier"		=>	"bool",
		"type_offre"	=>	"varchar(255)",
		"vente_location"=>	"varchar(8)",
		"n_mandat"		=>	"varchar(255)",
		"type_mandat"	=>	"varchar(50)",
		"date_offre"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_modif"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_dispo"	=>	"datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"negociateur"	=>	"varchar(255)",
		"prix_loyer"	=>	"int(30)",
		"honoraires"	=>	"int(30)",
		"travaux"		=>	"int(10)",
		"charges"		=>	"int(10)",
		"depot_garantie"=>	"int(10)",
		"taxe_habitation"=>"int(10)",
		"taxe_fonciere"	=>	"int(10)",
		"adr_bien_1"	=>	"longtext",
		"adr_bien_2"	=>	"longtext",
		"cp_bien"		=>	"varchar(10)",
		"ville_bien"	=>	"varchar(255)",
		"cp_internet"	=>	"varchar(10)",
		"ville_internet"=>	"varchar(255)",
		"quartier"		=>	"varchar(255)",
		"residence"		=>	"varchar(255)",
		"transport"		=>	"varchar(255)",
		"proximite"		=>	"varchar(255)",
		"secteur"		=>	"varchar(255)",
		"categorie"		=>	"varchar(255)",
		"nb_pieces"		=>	"int(2)",
		"nb_chambres"	=>	"int(2)",
		"surf_habit"	=>	"int(6)",
		"surf_carrez"	=>	"int(6)",
		"surf_sejour"	=>	"int(6)",
		"surf_terrain"	=>	"int(6)",
		"etage"			=>	"int(2)",
		"code_etage"	=>	"int(6)",
		"nb_etage"		=>	"int(2)",
		"annee_cons"	=>	"varchar(255)",
		"type_cuisine"	=>	"varchar(255)",
		"nb_wc"			=>	"int(2)",
		"nb_sdb"		=>	"int(2)",
		"nb_sde"		=>	"int(2)",
		"nb_park_int"	=>	"int(2)",
		"nb_park_ext"	=>	"int(2)",
		"nb_garages"	=>	"int(2)",
		"type_soussol"	=>	"varchar(255)",
		"nb_caves"		=>	"int(2)",
		"type_chauf"	=>	"varchar(255)",
		"nat_chauf"		=>	"varchar(255)",
		"ascenseur"		=>	"int(2)",
		"balcon"		=>	"int(4)",
		"terrasse"		=>	"int(5)",
		"piscine"		=>	"bool",
		"acces_handi"	=>	"bool",
		"nb_murs_mit"	=>	"int(1)",
		"facade_terrain"=>	"int(3)",
		"texte_annonce"	=>	"longtext",
		"dpe"			=>	"varchar(3)",
		"prestige"		=>	"bool");

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
		"id_type_offre"	=>	"int(10) unsigned NOT NULL auto_increment",
		"libelle_offre"	=>	"varchar(255)");

	$spip_spipimmo_types_offres_key=array(
		"PRIMARY KEY" => "id_type_offre");

	$tables_principales['spip_spipimmo_types_offres']=array(
		'field' => &$spip_spipimmo_types_offres_field,
		'key' => &$spip_spipimmo_types_offres_key);

	return $tables_principales;
}
?>
