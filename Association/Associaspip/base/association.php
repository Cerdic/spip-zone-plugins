<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/



if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Declaration des tables
function association_declarer_tables_principales($tables_principales)
{

//-- Table CATEGORIES COTISATION ------------------------------------------
$spip_asso_categories = array(
	"id_categorie" => "INT UNSIGNED NOT NULL auto_increment",
	"valeur" => "TINYTEXT NOT NULL",
	"libelle" => "TEXT NOT NULL",
	"duree" => "INT UNSIGNED NOT NULL",
	"cotisation" => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
	"commentaires" => "TEXT NOT NULL",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_categories_key = array(
	"PRIMARY KEY" => "id_categorie"
);
$tables_principales['spip_asso_categories'] = array(
	'field' => &$spip_asso_categories,
	'key' => &$spip_asso_categories_key
);

//-- Table DONS ------------------------------------------
$spip_asso_dons = array(
	"id_don" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"date_don" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"bienfaiteur" => "TEXT NOT NULL",
	"id_adherent" => "BIGINT UNSIGNED NOT NULL",
	"argent" => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
	"colis" => "TINYTEXT NOT NULL",
	"valeur" => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // ??
	"contrepartie" => "TINYTEXT NOT NULL",
	"commentaire" => "TEXT",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_dons_key = array(
	"PRIMARY KEY" => "id_don"
);
$tables_principales['spip_asso_dons'] = array(
	'field' => &$spip_asso_dons,
	'key' => &$spip_asso_dons_key
);

//-- Table VENTES ------------------------------------------
$spip_asso_ventes = array(
	"id_vente" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"article" => "TINYTEXT NOT NULL",
	"code" => "TINYTEXT NOT NULL",
	"acheteur" => "TINYTEXT NOT NULL",
	"id_acheteur" => "BIGINT UNSIGNED NOT NULL",
	"quantite" => "FLOAT NOT NULL", // ??
	"date_vente" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"date_envoi" => "DATE DEFAULT '0000-00-00'",
	"prix_vente" => "DECIMAL(19,2) NOT NULL default '0'",
	"frais_envoi" => "DECIMAL(19,2) NOT NULL default '0'",
	"commentaire" => "TEXT",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_ventes_key = array(
	"PRIMARY KEY" => "id_vente"
);
$tables_principales['spip_asso_ventes'] = array(
	'field' => &$spip_asso_ventes,
	'key' => &$spip_asso_ventes_key
);

//-- Table COMPTES ------------------------------------------
$spip_asso_comptes = array(
	"id_compte" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"date" => "DATE default NULL",
	"recette" => "DECIMAL(19,2) NOT NULL default '0'",
	"depense" => "DECIMAL(19,2) NOT NULL default '0'",
	"justification" => "TEXT",
	"imputation" => "TINYTEXT",
	"journal" => "TINYTEXT",
	"id_journal" => "BIGINT UNSIGNED NOT NULL default '0'",
	"vu" => "BOOLEAN default 0",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_comptes_key = array(
	"PRIMARY KEY" => "id_compte"
);
$tables_principales['spip_asso_comptes'] = array(
	'field' => &$spip_asso_comptes,
	'key' => &$spip_asso_comptes_key
);

//-- Table PLAN COMPTABLE ------------------------------------------
$spip_asso_plan = array(
	"id_plan" => "INT UNSIGNED NOT NULL auto_increment",
	"code" => "TINYTEXT NOT NULL",
	"intitule" => "TINYTEXT NOT NULL",
	"classe" =>"TEXT NOT NULL",
	"type_op" => "ENUM('credit','debit', 'multi') NOT NULL default 'multi'",
	"solde_anterieur" => "DECIMAL(19,2) NOT NULL default '0'",
	"date_anterieure" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"commentaire" => "TEXT NOT NULL",
	"active" => "BOOLEAN default 1",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_plan_key = array(
	"PRIMARY KEY" => "id_plan"
);
$tables_principales['spip_asso_plan'] = array(
	'field' => &$spip_asso_plan,
	'key' => &$spip_asso_plan_key
);

//-- Tables DESTINATION ----------------------------------------
$spip_asso_destination = array(
	"id_destination" => "INT UNSIGNED NOT NULL auto_increment",
	"intitule" => "TINYTEXT NOT NULL",
	"commentaire" => "TEXT NOT NULL",
);
$spip_asso_destination_key = array(
	"PRIMARY KEY" => "id_destination"
);
$tables_principales['spip_asso_destination'] = array(
	'field' => &$spip_asso_destination,
	'key' => &$spip_asso_destination_key
);
$spip_asso_destination_op = array(
	"id_dest_op" => "INT UNSIGNED NOT NULL auto_increment",
	"id_compte" => "BIGINT UNSIGNED NOT NULL",
	"id_destination" => "INT UNSIGNED NOT NULL",
	"recette" => "DECIMAL(19,2) NOT NULL default '0'",
	"depense" => "DECIMAL(19,2) NOT NULL default '0'",
);
$spip_asso_destination_op_key = array(
	"PRIMARY KEY" => "id_dest_op"
);
$tables_principales['spip_asso_destination_op'] = array(
	'field' => &$spip_asso_destination_op,
	'key' => &$spip_asso_destination_op_key
);

//-- Table RESSOURCES ------------------------------------------
$spip_asso_ressources = array(
	"id_ressource" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"code" => "TINYTEXT NOT NULL",
	"intitule" => "TINYTEXT NOT NULL",
	"date_acquisition" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"prix_acquisition" => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // Il s'agit du cout total d'acquisition (pour toutes les quantites acquises, avec --pour simplifier-- les frais accessoires --transports et renumeration d'intermediaires-- et taxes --de valeur ajourtee ou assimilables--)
	"prix_caution" => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // pour le depot de garanti... http://lexinter.net/JF/cautionnement.htm
	"pu" => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // prix unitaire (par tranche de temps) de la location
	"ud" => "CHAR(1) NULL DEFAULT 'D'", // unite des durees (de tranches) de location/pret : ce champ pourrait etre un ENUM('Y','M','W','D','H','S') mais un CHAR(1) est plus portable, d'autant que les caracteres geres le sont par la fonction association_formater_duree(); ce qui est ca de moins a gerer en base.
	"statut" => "TINYINT NULL", // utiliser un entier permet de pouvoir associer la quantite acquise ...assez reduite (d'ou du TinyInt et non autre : il ne s'agit pas non plus de gerer un entrepot de grossiste... mais 2-3 unites sans devoir forcement creer des codes distincts --ce qui est recommande meme si on reste flexible)
	"commentaire" => "TEXT NOT NULL",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_ressources_key = array(
	"PRIMARY KEY" => "id_ressource"
);
$tables_principales['spip_asso_ressources'] = array(
	'field' => &$spip_asso_ressources,
	'key' => &$spip_asso_ressources_key
);

//-- Table PRETS ------------------------------------------
$spip_asso_prets = array(
	"id_pret" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"id_ressource" => "BIGINT UNSIGNED NOT NULL",
	"date_reservation" => "DATETIME DEFAULT NULL ", // reservation prealable, sinon la plus ancienne des dates de sortie ou de depot de caution ; on ne peut helas pas mettre comme valeur par defaut NOW() ou CURRENT_TIME dans la definition
	"date_sortie" => "DATETIME NOT NULL DEFAULT '0000-00-00T00:00:00'", // prise de la ressource
	"date_retour" => "DATETIME NOT NULL DEFAULT '0000-00-00T00:00:00'", // retour de la ressource
	"date_caution1" => "DATE NOT NULL DEFAULT '0000-00-00' ", // depot (encaissement/engagement) de la caution
	"date_caution0" => "DATE NOT NULL DEFAULT '0000-00-00' ", // retrait (decaissement/restitution) de la caution
	"duree" => "FLOAT UNSIGNED NOT NULL DEFAULT '0'", // quantite finale facturee
	"prix_unitaire" => "DECIMAL(19,2) NOT NULL DEFAULT 0", // prix de base facturee
	"prix_caution" => "DECIMAL(19,2) NOT NULL DEFAULT 0", // montant de la garantie deposee
	"id_emprunteur" => "BIGINT UNSIGNED NOT NULL",
	"commentaire_sortie" => "TEXT NOT NULL",
	"commentaire_retour" => "TEXT NOT NULL",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_prets_key = array(
	"PRIMARY KEY" => "id_pret"
);
$tables_principales['spip_asso_prets'] = array(
	'field' => &$spip_asso_prets,
	'key' => &$spip_asso_prets_key
);

//-- Table ACTIVITES ------------------------------------------
$spip_asso_activites = array(
	"id_activite" => "BIGINT UNSIGNED NOT NULL auto_increment",
	"id_evenement" => "BIGINT UNSIGNED NOT NULL",
	"nom" => "TEXT NOT NULL",
	"id_adherent" => "BIGINT UNSIGNED NOT NULL",
  	"inscrits" => "INT NOT NULL DEFAULT '0'", // Ce pourrait etre un FLOAT si c'est utilise comme "quantite" appliquee a un montant unique (equivaut alors au "nombre de tarifs"...) ici il s'agit bien du nombre d'invites (ce champ aurait du s'appeler ainsi d'ailleurs), l'adherent(e) exclu(e) (donc peut valeur 0 tandis que "id_adherent" ou "nom" aura toujours une valeur)
	"date_inscription" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"commentaire" => "TEXT NOT NULL",
	"montant" => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
	"date_paiement" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_activites_key = array(
	"PRIMARY KEY" => "id_activite"
);
$tables_principales['spip_asso_activites'] = array(
	'field' => &$spip_asso_activites,
	'key' => &$spip_asso_activites_key
);

//-- Table groupes de membres: deux tables: groupe et liaison -----------------
$spip_asso_groupes= array(
	"id_groupe" => "INT UNSIGNED NOT NULL auto_increment",
	"nom" => "VARCHAR(128) NOT NULL",
	"commentaires" => "TEXT",
	"affichage" => "TINYINT NOT NULL DEFAULT 0",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_groupes_key = array(
	"PRIMARY KEY" => "id_groupe"
);
$tables_principales['spip_asso_groupes'] = array(
	'field' => &$spip_asso_groupes,
	'key' => &$spip_asso_groupes_key
);
$spip_asso_groupes_liaisons= array(
	"id_groupe" => "BIGINT UNSIGNED NOT NULL",
	"id_auteur" => "BIGINT UNSIGNED NOT NULL",
	"fonction" => "VARCHAR(128) NOT NULL",
#	"date_debut" => "DATE NOT NULL DEFAULT '0000-00-00'",
#	"date_fin" => "DATE NOT NULL DEFAULT '0000-00-00'",
	"commentaires" => "TEXT",
	"maj" => "TIMESTAMP NOT NULL"
);
$spip_asso_groupes_liaisons_key = array(
	"PRIMARY KEY" => "id_groupe,id_auteur"
);
$tables_principales['spip_asso_groupes_liaisons'] = array(
	'field' => &$spip_asso_groupes_liaisons,
	'key' => &$spip_asso_groupes_liaisons_key
);

$spip_asso_membres= array(
  "id_auteur" => "BIGINT UNSIGNED NOT NULL auto_increment",
  "id_asso" => "TINYTEXT NOT NULL",
  "nom_famille" => "TEXT NOT NULL",
  "prenom" => "TEXT NOT NULL",
  "sexe" => "TINYTEXT NOT NULL",
  "categorie" => "INT", // ce champ contient la cle spip_asso_categories.id_categorie
  "statut_interne" => "TINYTEXT NOT NULL",
  "commentaire" => "TEXT NOT NULL",
  "validite" => "DATE NOT NULL DEFAULT '0000-00-00'",
  "date_adhesion" => "DATE NOT NULL", // r51602
);
$spip_asso_membres_key= array(
	"PRIMARY KEY" => "id_auteur"
);
$tables_principales['spip_asso_membres'] = array(
	'field' => &$spip_asso_membres,
	'key' => &$spip_asso_membres_key
);

//-- Tables EXERCICES ----------------------------------------
$spip_asso_exercices = array(
	"id_exercice" => "INT UNSIGNED NOT NULL auto_increment",
	"intitule" => "TINYTEXT NOT NULL",
	"commentaire" => "TEXT NOT NULL",
	"debut" => "DATE NOT NULL default '0000-00-00'",
	"fin" => "DATE NOT NULL default '0000-00-00'"
);
$spip_asso_exercices_key = array(
	"PRIMARY KEY" => "id_exercice"
);
$tables_principales['spip_asso_exercices'] = array(
	'field' => &$spip_asso_exercices,
	'key' => &$spip_asso_exercices_key
);

return $tables_principales;

}

function association_declarer_tables_auxiliaires($tables_auxiliaires)
{

	$spip_asso_metas = array(
		"nom" => "VARCHAR(255) NOT NULL",
		"valeur" => "TEXT NOT NULL DEFAULT ''",
		"impt" => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		"maj" => "TIMESTAMP"
	);
	$spip_asso_metas_key = array(
		"PRIMARY KEY" => "nom"
	);
	$tables_auxiliaires['spip_association_metas'] = array(
	'field' => &$spip_asso_metas,
	'key' => &$spip_asso_metas_key
	);
	return $tables_auxiliaires;
}


function association_declarer_tables_interfaces($tables_interfaces)
{
	$tables_interfaces['table_des_tables']['asso_dons'] = 'asso_dons';
	$tables_interfaces['table_des_tables']['asso_ventes'] = 'asso_ventes';
	$tables_interfaces['table_des_tables']['asso_comptes'] = 'asso_comptes';
	$tables_interfaces['table_des_tables']['comptes'] = 'asso_comptes';
	$tables_interfaces['table_des_tables']['asso_categories'] = 'asso_categories';
	$tables_interfaces['table_des_tables']['asso_plan'] = 'asso_plan';
	$tables_interfaces['table_des_tables']['asso_ressources'] = 'asso_ressources';
	$tables_interfaces['table_des_tables']['asso_prets'] = 'asso_prets';
	$tables_interfaces['table_des_tables']['asso_activites'] = 'asso_activites';
	$tables_interfaces['table_des_tables']['asso_membres'] = 'asso_membres';
	$tables_interfaces['table_des_tables']['association_metas'] = 'association_metas';
	$tables_interfaces['table_des_tables']['asso_destination'] = 'asso_destination';
	$tables_interfaces['table_des_tables']['asso_destination_op'] = 'asso_destination_op';
	$tables_interfaces['table_des_tables']['asso_groupes'] = 'asso_groupes';
	$tables_interfaces['table_des_tables']['asso_groupes_liaisons'] = 'asso_groupes_liaisons';
	$tables_interfaces['table_des_tables']['asso_exercices'] = 'asso_exercices';

	// Pour que les raccourcis ci-dessous heritent d'une zone de clic pertinente
	$tables_interfaces['table_titre']['asso_membres']= "nom_famille AS titre, '' AS lang";
	$tables_interfaces['table_titre']['asso_dons']= "CONCAT('don ', id_don) AS titre, '' AS lang";
	/* jointures */
	$tables_interfaces['tables_jointures']['spip_asso_membres']['id_auteur'] = 'asso_groupes_liaisons';
	return  $tables_interfaces;
}

?>