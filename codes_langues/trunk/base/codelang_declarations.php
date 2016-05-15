<?php
/**
 * Ce fichier contient les modifications de la base de données requises
 * par le plugin.
 *
 * @package SPIP\CODELANG\ADMINISTRATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des nouvelles tables de la base de données propres au plugin.
 *
 * Le plugin déclare 5 nouvelles tables ISO-639 issues de 2 bases de données (SIL et Library of Congress
 * uniquemet pour les familles de langues) :
 *
 * - `spip_iso639codes`, qui contient les codes ISO-639-3, 2 et 1,
 * - `spip_iso639names`, qui contient les noms de langue,
 * - `spip_iso639macros`, qui contient le mapping des macrolangues,
 * - `spip_iso639retirements`, qui contient les langues retirées de la liste officielle,
 * - `spip_iso639families`, qui contient les familles et groupes de langues,
 *
 * et une table `spip_codes_langues` qui contient les codes de langues de certains services
 * comme spip et leur correspondance avec les codes ISO-639.
 *
 * @pipeline declarer_tables_principales
 *
 * @param array $tables_principales
 *        Tableau global décrivant la structure des tables de la base de données
 *
 * @return array
 *        Tableau fourni en entrée et mis à jour avec les nouvelles déclarations
 */
function codelang_declarer_tables_principales($tables_principales) {

	// -------------------------------------------------
	// Table principale des codes ISO : spip_iso639codes
	$table_codes = array(
		'code_639_3'  => "char(3) DEFAULT '' NOT NULL",      // The three-letter 639-3 identifier
		'code_639_2b' => 'char(3)',                          // Equivalent 639-2 identifier of the bibliographic applications code set, if there is one
		'code_639_2t' => 'char(3)',                          // Equivalent 639-2 identifier of the terminology applications code set, if there is one
		'code_639_1'  => 'char(2)',                          // Equivalent 639-1 identifier, if there is one
		'scope'       => "char(1) DEFAULT '' NOT NULL",      // I(ndividual), M(acrolanguage), S(pecial)
		'type'        => "char(1) DEFAULT '' NOT NULL",      // A(ncient), C(onstructed), E(xtinct), H(istorical), L(iving), S(pecial)
		'ref_name'    => "varchar(150) DEFAULT '' NOT NULL", // Reference language name
		'comment'     => 'varchar(150)',                     // Comment relating to one or more of the columns
		'maj'         => 'timestamp'
	);

	$table_codes_key = array(
		'PRIMARY KEY' => 'code_639_3'
	);

	$tables_principales['spip_iso639codes'] =
		array('field' => &$table_codes, 'key' => &$table_codes_key);

	// --------------------------------------------
	// Tables des noms de langue : spip_iso639names
	$table_names = array(
		'code_639_3'    => "char(3) DEFAULT '' NOT NULL",     // The three-letter 639-3 identifier
		'print_name'    => "varchar(75) DEFAULT '' NOT NULL", // One of the names associated with this identifier
		'inverted_name' => "varchar(75) DEFAULT '' NOT NULL", // The inverted form of this Print_Name form
		'maj'           => 'timestamp'
	);

	$table_names_key = array(
		'PRIMARY KEY' => 'code_639_3, print_name'
	);

	$tables_principales['spip_iso639names'] =
		array('field' => &$table_names, 'key' => &$table_names_key);

	// -------------------------------------------
	// Tables des macrolangues : spip_iso639macros
	$table_macros = array(
		'macro_639_3' => "char(3) DEFAULT '' NOT NULL",       // The identifier for a macrolanguage
		'code_639_3'  => "char(3) DEFAULT '' NOT NULL",       // The identifier for an individual language that is a member of the macrolanguage
		'status'      => "char(1) DEFAULT '' NOT NULL",       // A (active) or R (retired) indicating the status of the individual code element
		'maj'         => 'timestamp'
	);

	$table_macros_key = array(
		'PRIMARY KEY' => 'macro_639_3, code_639_3'
	);

	$tables_principales['spip_iso639macros'] =
		array('field' => &$table_macros, 'key' => &$table_macros_key);

	// ------------------------------------------------------
	// Tables des langues supprimées : spip_iso639retirements
	$table_rets = array(
		'code_639_3'     => "char(3) DEFAULT '' NOT NULL",      // The three-letter 639-3 identifier
		'ref_name'       => "varchar(150) DEFAULT '' NOT NULL", // Reference language name
		'ret_reason'     => "char(1) DEFAULT '' NOT NULL",      // code for retirement: C (change), D (duplicate), N (non-existent), S (split), M (merge)
		'change_to'      => 'char(3)',                          // in the cases of C, D, and M, the identifier to which all instances of this Id should be changed
		'ret_remedy'     => 'varchar(300)',                     // The instructions for updating an instance of the retired (split) identifier
		'effective_date' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // The date the retirement became effective
		'maj'            => 'timestamp'
	);

	$table_rets_key = array(
		'PRIMARY KEY' => 'code_639_3'
	);

	$tables_principales['spip_iso639retirements'] =
		array('field' => &$table_rets, 'key' => &$table_rets_key);

	// ---------------------------------------------------------------------------
	// Tables des familles et groupes de langues (ISO-639-5) : spip_iso639families
	$table_families = array(
		'code_639_5'     => "char(3) DEFAULT '' NOT NULL",      // The three-letter 639-5 identifier
		'uri'		     => "varchar(150) DEFAULT '' NOT NULL", // Description page
		'label_en'       => "text DEFAULT '' NOT NULL",         // English label for the family
		'label_fr'       => "text DEFAULT '' NOT NULL",         // French label for the family
		'code_639_1'     => 'char(2)',                          // Equivalent 639-1 identifier, if there is one
		'scope'          => "char(1) DEFAULT '' NOT NULL",      // C(ollective) always
		'code_set'       => "varchar(32) DEFAULT '' NOT NULL",  // Any combinaison of 639-5 and 639-2 separed by comma
		'parent'         => "varchar(32) DEFAULT '' NOT NULL",  // List of 639-5 identifiers separated by comma
		'maj'            => 'timestamp'
	);

	$table_families_key = array(
		'PRIMARY KEY' => 'code_639_5'
	);

	$tables_principales['spip_iso639families'] =
		array('field' => &$table_families, 'key' => &$table_families_key);

	// ------------------------------------------------------------
	// Tables des codes de langues des services web, spip y compris
	$table_langues = array(
		'service'	    => "varchar(32) DEFAULT '' NOT NULL", // Nom du service, par exemple spip ou wunderground
		'code_langue'   => "varchar(16) DEFAULT '' NOT NULL", // code de langue pour le service concerné
		'code_639_3'    => "char(3) DEFAULT '' NOT NULL",     // The corresponding three-letter 639-3 identifier
		'nom_langue'    => "varchar(75) DEFAULT '' NOT NULL", // Nom de la langue tel que défini par le service
		'descriptif' 	=> "text DEFAULT '' NOT NULL" , // The inverted form of this Print_Name form
		'maj'           => 'timestamp'
	);

	$table_langues_key = array(
		'PRIMARY KEY' => 'service, code_langue'
	);

	$tables_principales['spip_codes_langues'] =
		array('field' => &$table_langues, 'key' => &$table_langues_key);


	return $tables_principales;
}


/**
 * Déclaration des informations tierces (alias, traitements, jointures, etc)
 * sur les tables de la base de données modifiées ou ajoutées par le plugin.
 *
 * Le plugin se contente de déclarer les alias des tables qu'il ajoute.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interface
 *        Tableau global des informations tierces sur les tables de la base de données
 *
 * @return array
 *        Tableau fourni en entrée et mis à jour avec les nouvelles informations
 */
function codelang_declarer_tables_interfaces($interfaces) {
	// Les tables
	$interfaces['table_des_tables']['iso639codes'] = 'iso639codes';
	$interfaces['table_des_tables']['iso639names'] = 'iso639names';
	$interfaces['table_des_tables']['iso639macros'] = 'iso639macros';
	$interfaces['table_des_tables']['iso639retirements'] = 'iso639retirements';
	$interfaces['table_des_tables']['iso639families'] = 'iso639families';
	$interfaces['table_des_tables']['codes_langues'] = 'codes_langues';

	// Les traitements

	return $interfaces;
}
