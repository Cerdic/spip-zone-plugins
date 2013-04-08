<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// http://programmer.spip.net/declarer_tables_principales
function association_declarer_tables_principales($tables_principales) {

	//-- Table CATEGORIES COTISATION ------------------------------------------
	$spip_asso_categories = array(
		'id_categorie' => "INT UNSIGNED NOT NULL",
		'valeur' => "TINYTEXT NOT NULL", // non utilise...
		'libelle' => "TEXT NOT NULL",
		'duree' => "INT UNSIGNED NOT NULL",
		'prix_cotisation' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'commentaire' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_categories_key = array(
		'PRIMARY KEY' => 'id_categorie'
	);
	$tables_principales['spip_asso_categories'] = array(
		'field' => &$spip_asso_categories,
		'key' => &$spip_asso_categories_key
	);

	//-- Table DONS ------------------------------------------
	$spip_asso_dons = array(
		'id_don' => "BIGINT UNSIGNED NOT NULL",
		'date_don' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'nom' => "TINYTEXT NOT NULL",
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'argent' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'colis' => "TINYTEXT NOT NULL",
		'valeur' => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // estimation du colis
		'contrepartie' => "TINYTEXT NOT NULL",
		'commentaire' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_dons_key = array(
		'PRIMARY KEY' => 'id_don'
	);
	$tables_principales['spip_asso_dons'] = array(
		'field' => &$spip_asso_dons,
		'key' => &$spip_asso_dons_key
	);

	//-- Table VENTES ------------------------------------------
	$spip_asso_ventes = array(
		'id_vente' => "BIGINT UNSIGNED NOT NULL",
		'article' => "TINYTEXT NOT NULL",
		'code' => "TINYTEXT NOT NULL",
		'nom' => "TINYTEXT NOT NULL",
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'quantite' => "FLOAT UNSIGNED NOT NULL DEFAULT 0",
		'date_vente' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'date_envoi' => "DATE DEFAULT '0000-00-00'",
		'prix_unitaire' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'frais_envoi' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'commentaire' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_ventes_key = array(
		'PRIMARY KEY' => 'id_vente'
	);
	$tables_principales['spip_asso_ventes'] = array(
		'field' => &$spip_asso_ventes,
		'key' => &$spip_asso_ventes_key
	);

	//-- Table COMPTES ------------------------------------------
	$spip_asso_comptes = array(
		'id_compte' => "BIGINT UNSIGNED NOT NULL",
		'date_operation' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'recette' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'depense' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'justification' => "TEXT NOT NULL",
		'imputation' => "TINYTEXT NOT NULL",
		'journal' => "TINYTEXT NOT NULL",
		'id_journal' => "BIGINT UNSIGNED NOT NULL DEFAULT '0'",
		'vu' => "BOOLEAN DEFAULT 0",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_comptes_key = array(
		'PRIMARY KEY' => 'id_compte'
	);
	$tables_principales['spip_asso_comptes'] = array(
		'field' => &$spip_asso_comptes,
		'key' => &$spip_asso_comptes_key
	);

	//-- Table PLAN COMPTABLE ------------------------------------------
	$spip_asso_plan = array(
		'id_plan' => "INT UNSIGNED NOT NULL",
		'code' => "TINYTEXT NOT NULL",
		'intitule' => "TINYTEXT NOT NULL",
		'classe' =>"TEXT NOT NULL",
		'type_op' => "ENUM('credit','debit', 'multi') NOT NULL DEFAULT 'multi'",
		'solde_anterieur' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'date_anterieure' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'commentaire' => "TEXT NOT NULL",
		'active' => "BOOLEAN DEFAULT 1",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_plan_key = array(
		'PRIMARY KEY' => 'id_plan'
	);
	$tables_principales['spip_asso_plan'] = array(
		'field' => &$spip_asso_plan,
		'key' => &$spip_asso_plan_key
	);

	//-- Table DESTINATION ----------------------------------------
	$spip_asso_destination = array(
		'id_destination' => "INT UNSIGNED NOT NULL",
		'intitule' => "TINYTEXT NOT NULL",
		'commentaire' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_destination_key = array(
		'PRIMARY KEY' => 'id_destination'
	);
	$tables_principales['spip_asso_destination'] = array(
		'field' => &$spip_asso_destination,
		'key' => &$spip_asso_destination_key
	);

	$spip_asso_destination_op = array(
		'id_dest_op' => "INT UNSIGNED NOT NULL",
		'id_compte' => "BIGINT UNSIGNED NOT NULL",
		'id_destination' => "INT UNSIGNED NOT NULL",
		'recette' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
		'depense' => "DECIMAL(19,2) NOT NULL DEFAULT '0'",
	);
	$spip_asso_destination_op_key = array(
		'PRIMARY KEY' => 'id_dest_op' // pourrait etre id_compte+id_destination qui en tout cas devrait etre unique
	);
	$tables_principales['spip_asso_destination_op'] = array(
		'field' => &$spip_asso_destination_op,
		'key' => &$spip_asso_destination_op_key
	);

	//-- Table RESSOURCES ------------------------------------------
	$spip_asso_ressources = array(
		'id_ressource' => "BIGINT UNSIGNED NOT NULL",
		'code' => "TINYTEXT NOT NULL",
		'intitule' => "TINYTEXT NOT NULL",
		'date_acquisition' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'prix_acquisition' => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // Il s'agit du cout total d'acquisition (pour toutes les quantites acquises, avec --pour simplifier-- les frais accessoires --transports et renumeration d'intermediaires-- et taxes --de valeur ajourtee ou assimilables--)
		'prix_caution' => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // pour le depot de garanti... http://lexinter.net/JF/cautionnement.htm
		'pu' => "DECIMAL(19,2) NOT NULL DEFAULT '0'", // prix unitaire (par tranche de temps) de la location
		'ud' => "CHAR(1) NULL DEFAULT 'D'", // unite des durees (de tranches) de location/pret : ce champ pourrait etre un ENUM('Y','M','W','D','H','S') mais un CHAR(1) est plus portable, d'autant que les caracteres geres le sont par la fonction association_formater_duree(); ce qui est ca de moins a gerer en base.
		'statut' => "TINYINT NULL", // utiliser un entier permet de pouvoir associer la quantite acquise ...assez reduite (d'ou du TinyInt et non autre : il ne s'agit pas non plus de gerer un entrepot de grossiste... mais 2-3 unites sans devoir forcement creer des codes distincts --ce qui est recommande meme si on reste flexible)
		'commentaire' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_ressources_key = array(
		'PRIMARY KEY' => 'id_ressource'
	);
	$tables_principales['spip_asso_ressources'] = array(
		'field' => &$spip_asso_ressources,
		'key' => &$spip_asso_ressources_key
	);

	//-- Table PRETS ------------------------------------------
	$spip_asso_prets = array(
		'id_pret' => "BIGINT UNSIGNED NOT NULL",
		'id_ressource' => "BIGINT UNSIGNED NOT NULL",
		'date_reservation' => "DATETIME DEFAULT NULL ", // reservation prealable, sinon la plus ancienne des dates de sortie ou de depot de caution ; on ne peut helas pas mettre comme valeur par defaut NOW() ou CURRENT_TIME dans la definition
		'date_sortie' => "DATETIME NOT NULL DEFAULT '0000-00-00T00:00:00'", // prise de la ressource
		'date_retour' => "DATETIME NOT NULL DEFAULT '0000-00-00T00:00:00'", // retour de la ressource
		'date_caution1' => "DATE NOT NULL DEFAULT '0000-00-00' ", // depot (encaissement/engagement) de la caution
		'date_caution0' => "DATE NOT NULL DEFAULT '0000-00-00' ", // retrait (decaissement/restitution) de la caution
		'duree' => "FLOAT UNSIGNED NOT NULL DEFAULT '0'", // quantite finale facturee
		'prix_unitaire' => "DECIMAL(19,2) NOT NULL DEFAULT 0", // prix de base unitaire facture
		'prix_caution' => "DECIMAL(19,2) NOT NULL DEFAULT 0", // montant de la garantie deposee
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'nom' => "TINYTEXT NOT NULL",
		'commentaire_sortie' => "TEXT NOT NULL",
		'commentaire_retour' => "TEXT NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_prets_key = array(
		'PRIMARY KEY' => 'id_pret'
	);
	$tables_principales['spip_asso_prets'] = array(
		'field' => &$spip_asso_prets,
		'key' => &$spip_asso_prets_key
	);

	//-- Table ACTIVITES ------------------------------------------
	$spip_asso_activites = array(
		'id_activite' => "BIGINT UNSIGNED NOT NULL",
		'id_evenement' => "BIGINT UNSIGNED NOT NULL",
		'nom' => "TINYTEXT NOT NULL",
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'quantite' => "FLOAT UNSIGNED NOT NULL DEFAULT 0", // C'est la quantite appliquee a un montant unique (equivaut alors au "nombre de tarifs"...) ou le nombre d'invites du participant
		'date_inscription' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'commentaire' => "TEXT NOT NULL",
		'prix_unitaire' => "DECIMAL(19,2) NOT NULL DEFAULT 0", // tarif de base par lequel on va multiplier la quantite (nombre de places) pour avoir le "montant" paye
		'date_paiement' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_activites_key = array(
		'PRIMARY KEY' => 'id_activite' // pourrait etre id_evenement+id_auteur qui en tout cas devrait etre unique
	);
	$tables_principales['spip_asso_activites'] = array(
		'field' => &$spip_asso_activites,
		'key' => &$spip_asso_activites_key
	);

	//-- Table GROUPES de membres ----------------------------
	$spip_asso_groupes = array(
		'id_groupe' => "INT UNSIGNED NOT NULL",
		'nom' => "VARCHAR(128) NOT NULL",
		'commentaire' => "TEXT NOT NULL",
		'affichage' => "TINYINT NOT NULL DEFAULT 0",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_groupes_key = array(
		'PRIMARY KEY' => 'id_groupe'
	);
	$tables_principales['spip_asso_groupes'] = array(
		'field' => &$spip_asso_groupes,
		'key' => &$spip_asso_groupes_key
	);

	//-- Table FONCTIONS de membres ----------------------------
	$spip_asso_fonctions = array(
		'id_groupe' => "BIGINT UNSIGNED NOT NULL",
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'fonction' => "VARCHAR(128) NOT NULL",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_fonctions_key = array(
		'PRIMARY KEY' => 'id_groupe,id_auteur'
	);
	$tables_principales['spip_asso_fonctions'] = array(
		'field' => &$spip_asso_fonctions,
		'key' => &$spip_asso_fonctions_key
	);

	//-- Table MEMBRES ----------------------------------------
	$spip_asso_membres = array(
		'id_auteur' => "BIGINT UNSIGNED NOT NULL",
		'id_asso' => "TINYTEXT NOT NULL",
		'nom_famille' => "TEXT NOT NULL",
		'prenom' => "TEXT NOT NULL",
		'sexe' => "TINYTEXT NOT NULL",
		'id_categorie' => "INT UNSIGNED NOT NULL",
		'statut_interne' => "TINYTEXT NOT NULL",
		'commentaire' => "TEXT NOT NULL",
		'date_validite' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_membres_key = array(
		'PRIMARY KEY' => 'id_auteur'
	);
	$tables_principales['spip_asso_membres'] = array(
		'field' => &$spip_asso_membres,
		'key' => &$spip_asso_membres_key
	);

	//-- Table EXERCICES ----------------------------------------
	$spip_asso_exercices = array(
		'id_exercice' => "INT UNSIGNED NOT NULL",
		'intitule' => "TINYTEXT NOT NULL",
		'commentaire' => "TEXT NOT NULL",
		'date_debut' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'date_fin' => "DATE NOT NULL DEFAULT '0000-00-00'",
		'maj' => "TIMESTAMP NOT NULL"
	);
	$spip_asso_exercices_key = array(
		'PRIMARY KEY' => 'id_exercice'
	);
	$tables_principales['spip_asso_exercices'] = array(
		'field' => &$spip_asso_exercices,
		'key' => &$spip_asso_exercices_key
	);

	return $tables_principales;
}

// http://programmer.spip.net/declarer_tables_auxiliaires
function association_declarer_tables_auxiliaires($tables_auxiliaires) {
	$spip_asso_metas = array(
		'nom' => "VARCHAR(255) NOT NULL",
		'valeur' => "TEXT NOT NULL DEFAULT ''",
		'impt' => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		'maj' => "TIMESTAMP"
	);
	$spip_asso_metas_key = array(
		'PRIMARY KEY' => "nom"
	);
	$tables_auxiliaires['spip_association_metas'] = array(
	'field' => &$spip_asso_metas,
	'key' => &$spip_asso_metas_key
	);
	return $tables_auxiliaires;
}

// http://programmer.spip.net/declarer_tables_interfaces,379
function association_declarer_tables_interfaces($tables_interfaces) {
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
	$tables_interfaces['table_des_tables']['asso_fonctions'] = 'asso_groupes_liaisons';
	$tables_interfaces['table_des_tables']['asso_exercices'] = 'asso_exercices';

	// Pour que les raccourcis ci-dessous heritent d'une zone de clic pertinente
	$tables_interfaces['table_titre']['asso_membres']= "nom_famille AS titre, '' AS lang";
	$tables_interfaces['table_titre']['asso_dons']= "CONCAT('don ', id_don) AS titre, '' AS lang";

	// jointures
	$tables_interfaces['tables_jointures']['spip_asso_membres']['id_auteur'] = 'asso_fonctions';
	$tables_interfaces['tables_jointures']['spip_asso_groupes']['id_groupe'] = 'asso_fonctions';
	$tables_interfaces['tables_jointures']['spip_asso_destination_op']['id_compte'] = 'asso_comptes';
	$tables_interfaces['tables_jointures']['spip_asso_destination_op']['id_destination'] = 'asso_destination';

	return  $tables_interfaces;
}

// http://programmer.spip.net/declarer_tables_objets_surnoms
function association_declarer_tables_objets_surnoms($objets_surnoms) {
	// constructions irregulieres ("table" n'est pas "objet" suffixe de "s")
	// $objets_surnoms["objet"] = "table";
	$objets_surnoms['asso_destination'] = 'asso_destination';
	$objets_surnoms['asso_destination_op'] = 'asso_destination_op';
	$objets_surnoms['asso_plan'] = 'asso_plan';

	return  $objets_surnoms;
}

?>