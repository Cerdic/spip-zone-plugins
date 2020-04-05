<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/



if (!defined("_ECRIRE_INC_VERSION")) return;

// Declaration des tables

global $association_tables_principales;

//-- Table CATEGORIES COTISATION ------------------------------------------
$spip_asso_categories = array(
	"id_categorie" 	=> "INT NOT NULL",
	"valeur" 		=> "TINYTEXT NOT NULL",
	"libelle" 		=> "TEXT NOT NULL",
	"duree" 		=> "TEXT NOT NULL",
	"cotisation" 	=> "FLOAT NOT NULL DEFAULT '0'",
	"commentaires" 	=> "TEXT NOT NULL",
	"maj" 			=> "TIMESTAMP NOT NULL"
);

$spip_asso_categories_key = array(
	"PRIMARY KEY" => "id_categorie"
);

$association_tables_principales['spip_asso_categories'] = array(
	'field' => &$spip_asso_categories,
	'key' => &$spip_asso_categories_key
);

//-- Table DONS ------------------------------------------
$spip_asso_dons = array(
	"id_don" 		=> "BIGINT NOT NULL",
	"date_don" 		=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"bienfaiteur" 	=> "TEXT NOT NULL",
	"id_adherent" 	=> "INT NOT NULL",
	"argent" 		=> "TINYTEXT",
	"colis" 		=> "TEXT",
	"valeur" 		=> "TEXT NOT NULL",
	"contrepartie" 	=> "TINYTEXT",
	"commentaire" 	=> "TEXT",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_dons_key = array(
	"PRIMARY KEY" => "id_don"
);
$association_tables_principales['spip_asso_dons'] = array(
	'field' => &$spip_asso_dons,
	'key' => &$spip_asso_dons_key
);

//-- Table VENTES ------------------------------------------
$spip_asso_ventes = array(
	"id_vente" 		=> "BIGINT NOT NULL",
	"article"		=> "TINYTEXT NOT NULL",
	"code"			=> "TEXT NOT NULL",
	"acheteur" 		=> "TINYTEXT NOT NULL",
	"id_acheteur"	=> "BIGINT NOT NULL",
	"quantite" 		=> "TINYTEXT NOT NULL",
	"date_vente"	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"date_envoi" 	=> "DATE DEFAULT '0000-00-00'",
	"prix_vente" 	=> "TINYTEXT",
	"frais_envoi" 	=> "FLOAT NOT NULL DEFAULT '0'",
	"commentaire" 	=> "TEXT",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_ventes_key = array(
	"PRIMARY KEY" => "id_vente"
);
$association_tables_principales['spip_asso_ventes'] = array(
	'field' => &$spip_asso_ventes,
	'key' => &$spip_asso_ventes_key
);

//-- Table COMPTES ------------------------------------------
$spip_asso_comptes = array(
	"id_compte" 	=> "BIGINT NOT NULL",
	"date"			=> "DATE DEFAULT NULL",
	"recette" 		=> "FLOAT NOT NULL DEFAULT '0'",
	"depense" 		=> "FLOAT NOT NULL DEFAULT '0'",
	"justification" => "TEXT",
	"imputation" 	=> "TEXT",
	"journal" 		=> "TINYTEXT",
	"id_journal" 	=> "INT NOT NULL default '0'",
	"vu"			=> "BOOLEAN default 0",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_comptes_key = array(
	"PRIMARY KEY" => "id_compte"
);
$association_tables_principales['spip_asso_comptes'] = array(
	'field' => &$spip_asso_comptes,
	'key' => &$spip_asso_comptes_key
);

//-- Table PLAN COMPTABLE ------------------------------------------
$spip_asso_plan = array(
	"id_plan" 		=> "INT NOT NULL",
	"code" 			=> "TEXT NOT NULL",
	"intitule" 		=> "TEXT NOT NULL",
	"classe"		=> "TEXT NOT NULL",
	"type_op"		=> "ENUM('credit','debit', 'multi') NOT NULL DEFAULT 'multi'",
	"solde_anterieur"	=> "FLOAT NOT NULL DEFAULT '0'",
	"date_anterieure"	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"commentaire" 	=> "TEXT NOT NULL",
	"active"		=> "BOOLEAN DEFAULT 1",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_plan_key = array(
	"PRIMARY KEY" => "id_plan"
);
$association_tables_principales['spip_asso_plan'] = array(
	'field' => &$spip_asso_plan,
	'key' => &$spip_asso_plan_key
);

//-- Tables DESTINATION ----------------------------------------
$spip_asso_destination = array(
	"id_destination"	=> "INT NOT NULL",
	"intitule" 		=> "TEXT NOT NULL",
	"commentaire" 	=> "TEXT NOT NULL",
);
$spip_asso_destination_key = array(
	"PRIMARY KEY" => "id_destination"
);
$association_tables_principales['spip_asso_destination'] = array(
	'field' => &$spip_asso_destination,
	'key' => &$spip_asso_destination_key
);

$spip_asso_destination_op = array(
	"id_dest_op"	=> "INT NOT NULL",
	"id_compte" 	=> "INT NOT NULL",
	"id_destination"	=> "INT NOT NULL",
	"recette" 		=> "FLOAT NOT NULL DEFAULT '0'",
	"depense" 		=> "FLOAT NOT NULL DEFAULT '0'",
);
$spip_asso_destination_op_key = array(
	"PRIMARY KEY" => "id_dest_op"
);
$association_tables_principales['spip_asso_destination_op'] = array(
	'field' => &$spip_asso_destination_op,
	'key' => &$spip_asso_destination_op_key
);
//-- Table RESSOURCES ------------------------------------------
$spip_asso_ressources = array(
	"id_ressource"	=> "BIGINT NOT NULL",
	"code" 			=> "TEXT NOT NULL",
	"intitule" 		=> "TEXT NOT NULL",
	"date_acquisition"	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"pu" 			=> "FLOAT NOT NULL DEFAULT '0'",
	"statut"		=> "TEXT NOT NULL",
	"commentaire" 	=> "TEXT NOT NULL",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_ressources_key = array(
	"PRIMARY KEY" => "id_ressource"
);
$association_tables_principales['spip_asso_ressources'] = array(
	'field' => &$spip_asso_ressources,
	'key' => &$spip_asso_ressources_key
);

//-- Table PRETS ------------------------------------------
$spip_asso_prets = array(
	"id_pret"		=> "BIGINT NOT NULL",
	"id_ressource"	=> "VARCHAR(20) NOT NULL",
	"date_sortie" 	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"duree"			=> "INT NOT NULL default '0'",
	"date_retour" 	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"id_emprunteur" => "TEXT NOT NULL",
	"statut"		=> "TEXT NOT NULL",
	"commentaire_sortie"	=> "TEXT NOT NULL",
	"commentaire_retour"	=> "TEXT NOT NULL",
	"maj" 			=> "TIMESTAMP NOT NULL"
);
$spip_asso_prets_key = array(
	"PRIMARY KEY" => "id_pret"
);
$association_tables_principales['spip_asso_prets'] = array(
	'field' => &$spip_asso_prets,
	'key' => &$spip_asso_prets_key
);

//-- Table ACTIVITES ------------------------------------------
$spip_asso_activites = array(
	"id_activite"	=> "BIGINT NOT NULL",
	"id_evenement"	=> "BIGINT NOT NULL",
	"nom"			=> "TEXT NOT NULL",
	"id_adherent"	=> "BIGINT NOT NULL",
	"membres" 		=> "TEXT NOT NULL",
	"non_membres" 	=> "TEXT NOT NULL",
  	"inscrits"		=> "INT NOT NULL DEFAULT '0'",
	"date"			=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"telephone"		=> "TEXT NOT NULL",
	"adresse"		=> "TEXT NOT NULL",
	"email"			=> "TEXT NOT NULL",
	"commentaire"	=> "TEXT NOT NULL",
	"montant"		=> "FLOAT NOT NULL DEFAULT '0'",
	"DATE_paiement"	=> "DATE NOT NULL DEFAULT '0000-00-00'",
	"statut"		=> "TEXT NOT NULL",
	"maj"			=> "TIMESTAMP NOT NULL"

);
$spip_asso_activites_key = array(
	"PRIMARY KEY" => "id_activite"
);
$association_tables_principales['spip_asso_activites'] = array(
	'field' => &$spip_asso_activites,
	'key' => &$spip_asso_activites_key
);

//
$spip_asso_membres= array(
  "id_auteur"		=> "BIGINT NOT NULL",
  "id_asso"			=> "TEXT NOT NULL",
  "nom_famille"		=> "TEXT NOT NULL",
  "prenom"			=> "TEXT NOT NULL",
  "sexe"			=> "TINYTEXT NOT NULL",
  "fonction"		=> "TEXT",
  "email"			=> "TINYTEXT NOT NULL",
  "adresse"			=> "TEXT NOT NULL",
  "code_postal"		=> "TINYTEXT NOT NULL",
  "ville"			=> "TEXT NOT NULL",
  "telephone"		=> "TINYTEXT",
  "mobile"			=> "TINYTEXT",
  "categorie"		=> "TEXT NOT NULL",
  "statut_interne"	=> "TEXT NOT NULL",
  "commentaire"		=> "TEXT NOT NULL",
  "validite"		=> "DATE NOT NULL default '0000-00-00'"
);
$spip_asso_membres_key= array(
	"PRIMARY KEY" => "id_auteur"
);
$association_tables_principales['spip_asso_membres'] = array(
	'field' => &$spip_asso_membres,
	'key' => &$spip_asso_membres_key
);

global $association_tables_auxiliaires;

$spip_asso_metas = array(
		"nom"		=> "VARCHAR(255) NOT NULL",
		"valeur"	=> "TEXT DEFAULT ''",
		"impt"		=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		"maj"		=> "TIMESTAMP"
);
$spip_asso_metas_key = array(
		"PRIMARY KEY"	=> "nom"
);
$association_tables_auxiliaires['spip_association_metas'] = array(
	'field' => &$spip_asso_metas,
	'key' => &$spip_asso_metas_key
);

global $tables_principales;
include_spip('base/serial');
$tables_principales = array_merge($tables_principales,  $association_tables_principales);

global $tables_auxiliaires;
include_spip('base/auxiliaires');
$tables_auxiliaires = array_merge($tables_auxiliaires,  $association_tables_auxiliaires);

?>