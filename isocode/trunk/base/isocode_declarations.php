<?php
/**
 * Ce fichier contient les modifications de la base de données requises
 * par le plugin.
 *
 * @package SPIP\ISOCODE\ADMINISTRATION
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
 * - `spip_iso639families`, qui contient les familles et groupes de langues ISO-639-5,
 *
 * Le plugin déclare aussi une table `spip_iso15924scripts` qui contient les codets d'écriture à 4 lettres et leur
 * définition en français et en anglais et une table `spip_iana5646subtags` qui contient les codes des sous-étiquettes
 * des étiquettes de langue construites selon la RFC 5646.
 *
 * Enfin, la plugin déclare une table `spip_iso15924countries` qui contient les indicatifs ISO-3166 des pays.
 *
 * @pipeline declarer_tables_principales
 *
 * @param array $tables_principales
 *        Tableau global décrivant la structure des tables de la base de données
 *
 * @return array
 *        Tableau fourni en entrée et mis à jour avec les nouvelles déclarations
 */
function isocode_declarer_tables_principales($tables_principales) {

	// ---------------------------------------------------------------
	// Table principale des codes de langue ISO-639 : spip_iso639codes
	$table_codes = array(
		'code_639_3'  => "char(3) DEFAULT '' NOT NULL",      // The three-letter 639-3 identifier
		'code_639_2b' => 'char(3)',                          // Equivalent 639-2 identifier of the bibliographic applications code set, if there is one
		'code_639_2t' => 'char(3)',                          // Equivalent 639-2 identifier of the terminology applications code set, if there is one
		'code_639_1'  => 'char(2)',                          // Equivalent 639-1 identifier, if there is one
		'scope'       => "char(1) DEFAULT '' NOT NULL",      // I(ndividual), M(acrolanguage), S(pecial)
		'type'        => "char(1) DEFAULT '' NOT NULL",      // A(ncient), C(onstructed), E(xtinct), H(istorical), L(iving), S(pecial)
		'ref_name'    => "varchar(150) DEFAULT '' NOT NULL", // Reference language name
		'comment'     => 'varchar(150)',                     // Comment relating to one or more of the columns
		'maj'         => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
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
		'maj'           => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
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
		'maj'         => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
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
		'maj'            => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_rets_key = array(
		'PRIMARY KEY' => 'code_639_3'
	);

	$tables_principales['spip_iso639retirements'] =
		array('field' => &$table_rets, 'key' => &$table_rets_key);

	// ---------------------------------------------------------------------------
	// Tables des familles et groupes de langues (ISO-639-5) : spip_iso639families
	$table_families = array(
		'code_639_5' => "char(3) DEFAULT '' NOT NULL",      // The three-letter 639-5 identifier
		'uri'        => "varchar(150) DEFAULT '' NOT NULL", // Description page
		'label_en'   => "text DEFAULT '' NOT NULL",         // English label for the family
		'label_fr'   => "text DEFAULT '' NOT NULL",         // French label for the family
		'label'      => "text DEFAULT '' NOT NULL",         // Multiple langages label for the family
		'code_639_1' => 'char(2)',                          // Equivalent 639-1 identifier, if there is one
		'scope'      => "char(1) DEFAULT '' NOT NULL",      // C(ollective) always
		'code_set'   => "varchar(32) DEFAULT '' NOT NULL",  // Any combinaison of 639-5 and 639-2 separed by comma
		'hierarchy'  => "varchar(32) DEFAULT '' NOT NULL",  // List of 639-5 identifiers separated by comma
		'parent'     => "char(3) DEFAULT '' NOT NULL",      // The parent three-letter 639-5 identifier
		'maj'        => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_families_key = array(
		'PRIMARY KEY' => 'code_639_5'
	);

	$tables_principales['spip_iso639families'] =
		array('field' => &$table_families, 'key' => &$table_families_key);

	// -------------------------------------------------------------------
	// Table des indicatifs d'écritures (ISO 15924) : spip_iso15924scripts
	$table_scripts = array(
		'code_15924' => "char(4) DEFAULT '' NOT NULL",                     // The four-letter identifier
		'label_en'   => "varchar(255) DEFAULT '' NOT NULL",                // English script name
		'label_fr'   => "varchar(255) DEFAULT '' NOT NULL",                // french script name
		'label'      => "text DEFAULT '' NOT NULL",                        // Multiple langages label
		'code_num'   => "char(3) DEFAULT '' NOT NULL",                     // Numeric identifier
		'alias_en'   => "varchar(32) DEFAULT '' NOT NULL",                 // Unicode alias showing how ISO 15924 code relate to script names defined in Unicode.
		'date_ref'   => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // The reference date to follow changes
		'maj'        => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_scripts_key = array(
		'PRIMARY KEY' => 'code_15924'
	);

	$tables_principales['spip_iso15924scripts'] =
		array('field' => &$table_scripts, 'key' => &$table_scripts_key);

	// -------------------------------------------------------------------------------------
	// Table des indicatifs des pays ISO-3166 et autres informations : spip_iso3166countries
	$table_countries = array(
		'code_alpha2'     => "char(2) DEFAULT '' NOT NULL",       // The two-letter identifier
		'code_alpha3'     => "char(3) DEFAULT '' NOT NULL",       // The three-letter identifier
		'code_num'        => "char(3) DEFAULT '' NOT NULL",       // Numeric identifier
		'label_en'        => "varchar(255) DEFAULT '' NOT NULL",  // English name
		'label_fr'        => "varchar(255) DEFAULT '' NOT NULL",  // french name
		'label'           => "text DEFAULT '' NOT NULL",          // Multiple langages label
		'capital'         => "varchar(255) DEFAULT '' NOT NULL",  // Capital name
		'area'            => "int DEFAULT 0 NOT NULL",            // Area in squared km
		'population'      => "int DEFAULT 0 NOT NULL",            // Inhabitants count
		'code_continent'  => "char(2) DEFAULT '' NOT NULL",       // Continent code alpha2
		'code_num_region' => "char(3) DEFAULT '' NOT NULL",       // Parent region numeric code (ISO 3166)
		'tld'             => "char(3) DEFAULT '' NOT NULL",       // Tld - Top-Level Domain
		'code_4217_3'     => "char(3) DEFAULT '' NOT NULL",       // Currency code ISO-4217
		'currency_en'     => "varchar(255) DEFAULT '' NOT NULL",  // Currency English name
		'phone_id'        => "varchar(16) DEFAULT '' NOT NULL",   // Phone id
		'maj'             => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_countries_key = array(
		'PRIMARY KEY'     => 'code_alpha2',
		'KEY code_alpha3' => 'code_alpha3',
		'KEY code_num'    => 'code_num',
	);

	$tables_principales['spip_iso3166countries'] =
		array('field' => &$table_countries, 'key' => &$table_countries_key);

	// ------------------------------------------------------------------
	// Table des indicatifs des devises ISO-4217 : spip_iso4217currencies
	$table_currencies = array(
		'code_4217_3' => "char(3) DEFAULT '' NOT NULL",       // The three-letter identifier
		'code_num'    => "char(3) DEFAULT '' NOT NULL",       // Numeric identifier
		'label_en'    => "varchar(255) DEFAULT '' NOT NULL",  // English name
		'label_fr'    => "varchar(255) DEFAULT '' NOT NULL",  // french name
		'label'       => "text DEFAULT '' NOT NULL",          // Multiple langages label
		'symbol'      => "char(8) DEFAULT '' NOT NULL",       // Currency symbol
		'minor_units' => "int DEFAULT 0 NOT NULL",            // Minor units
		'maj'         => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_currencies_key = array(
		'PRIMARY KEY' => 'code_4217_3'
	);

	$tables_principales['spip_iso4217currencies'] =
		array('field' => &$table_currencies, 'key' => &$table_currencies_key);

	// -----------------------------------------------------------------------------------------
	// Table reproduisant le registre IANA des sous-étiquettes de langues : spip_iana5646subtags
	$table_subtags = array(
		'type'           => "varchar(16) DEFAULT '' NOT NULL", // Subtag type as language, variant, extlang, region, script...
		'subtag'         => "varchar(32) DEFAULT '' NOT NULL", // Subtag value
		'description'    => "text DEFAULT '' NOT NULL",       // Descriptions of subtags separated by comma
		'date_ref'       => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // Subtag creation date
		'no_script'      => "char(4) DEFAULT '' NOT NULL",     // The four letter script identifier not to be used for the subtag
		'scope'          => "varchar(32) DEFAULT '' NOT NULL", // Scope indication : collection, macrolanguage...
		'macro_language' => "char(3) DEFAULT '' NOT NULL",     // Macrolanguage to which subtag is refering to
		'deprecated'     => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // Deprecated date if any
		'preferred_tag'  => "char(3) DEFAULT '' NOT NULL",     // Preferred tag to be used instead the current subtag
		'prefix'         => "char(3) DEFAULT '' NOT NULL",     // Prefix to be used thos the subtag except is a preferred tag exists
		'comments'       => "text DEFAULT '' NOT NULL",        // Comments on subtag
		'maj'            => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_subtags_key = array(
		'PRIMARY KEY' => 'type, subtag'
	);

	$tables_principales['spip_iana5646subtags'] =
		array('field' => &$table_subtags, 'key' => &$table_subtags_key);

	// -------------------------------------------------------------------------------------
	// Table des indicatifs des continents GeoIP : spip_geoipcontinents
	$table_continents = array(
		'code'     => "char(2) DEFAULT '' NOT NULL",       // The two-letter identifier
		'label_en' => "varchar(255) DEFAULT '' NOT NULL",  // English name
		'label_fr' => "varchar(255) DEFAULT '' NOT NULL",  // french name
		'label'    => "text DEFAULT '' NOT NULL",          // Multiple langages label
		'maj'      => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_continents_key = array(
		'PRIMARY KEY' => 'code'
	);

	$tables_principales['spip_geoipcontinents'] =
		array('field' => &$table_continents, 'key' => &$table_continents_key);

	// -------------------------------------------------------------------------------------
	// Table des indicatifs des régions du monde (arborescence) : spip_m49regions
	$table_regions = array(
		'code_num' => "char(3) DEFAULT '' NOT NULL",       // The three-letter numeric identifier
		'parent'   => "char(3) DEFAULT '' NOT NULL",       // The three-letter numeric identifier of parent
		'label_en' => "varchar(255) DEFAULT '' NOT NULL",  // English name
		'label_fr' => "varchar(255) DEFAULT '' NOT NULL",  // french name
		'label'    => "text DEFAULT '' NOT NULL",          // Multiple langages label
		'maj'      => 'timestamp DEFAULT current_timestamp ON UPDATE current_timestamp'
	);

	$table_regions_key = array(
		'PRIMARY KEY' => 'code_num'
	);

	$tables_principales['spip_m49regions'] =
		array('field' => &$table_regions, 'key' => &$table_regions_key);

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
 * @param array $interfaces
 *        Tableau global des informations tierces sur les tables de la base de données
 *
 * @return array
 *        Tableau fourni en entrée et mis à jour avec les nouvelles informations
 */
function isocode_declarer_tables_interfaces($interfaces) {
	// Les tables
	$interfaces['table_des_tables']['iso639codes'] = 'iso639codes';
	$interfaces['table_des_tables']['iso639names'] = 'iso639names';
	$interfaces['table_des_tables']['iso639macros'] = 'iso639macros';
	$interfaces['table_des_tables']['iso639retirements'] = 'iso639retirements';
	$interfaces['table_des_tables']['iso639families'] = 'iso639families';
	$interfaces['table_des_tables']['iso15924scripts'] = 'iso15924scripts';
	$interfaces['table_des_tables']['iso3166countries'] = 'iso3166countries';
	$interfaces['table_des_tables']['iso4217currencies'] = 'iso4217currencies';
	$interfaces['table_des_tables']['iana5646subtags'] = 'iana5646subtags';
	$interfaces['table_des_tables']['geoipcontinents'] = 'geoipcontinents';
	$interfaces['table_des_tables']['m49regions'] = 'm49regions';

	// Les traitements

	return $interfaces;
}
