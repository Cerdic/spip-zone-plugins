<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bouton_isocode'                => 'Codes ISO',

	// E
	'erreur_400_region_nok_titre'      => 'La région d\'identifiant « @valeur@ » est invalide',
	'erreur_400_region_nok_message'    => 'La région doit être désignée par son identifiant UN M49 (3 chiffres)',
	'erreur_400_continent_nok_titre'   => 'Le continent d\'identifiant « @valeur@ » est invalide',
	'erreur_400_continent_nok_message' => 'Le continent doit être désignée par son identifiant GeoIP (2 lettres)',
	'erreur_charger_table'             => 'Une erreur s\'est produite lors du chargement de la ou des tables « @tables@ ».',
	'erreur_decharger_table'           => 'Une erreur s\'est produite lors du vidage de la ou des tables « @tables@ ».',
	'explication_action_table'         => 'Si la table est déjà chargée en base de données elle sera vidée avant le chargement.',

	// I
	'info_table_chargee'               => 'chargée',

	// L
	'label_action_charger_table'       => 'Charger une table',
	'label_action_table'               => 'Action à exécuter',
	'label_action_decharger_table'     => 'Vider une table',
	'label_colonne_actualisation'      => 'Actualisé le',
	'label_colonne_libelle'            => 'Description',
	'label_colonne_service'            => 'Service',
	'label_colonne_statistiques'       => 'Enregistrements',
	'label_colonne_table'              => 'Table',
	'label_table_geoipcontinents'      => 'table des codes GeoIP des continents',
	'label_table_iana5646subtags'      => 'table reproduisant le registre IANA des sous-étiquettes de langues (RFC 5646)',
	'label_table_iso15924scripts'      => 'table des indicatifs d\'écritures (ISO 15924)',
	'label_table_iso3166countries'     => 'table des indicatifs des pays (ISO 3166)',
	'label_table_iso3166subdivisions'  => 'table des indicatifs des subdivisions des pays (ISO 3166-2)',
	'label_table_m49regions'           => 'table des indicatifs des régions du monde selon l\'ONU (UN M.49)',
	'label_table_iso4217currencies'    => 'table des devises (ISO 4217)',
	'label_table_iso639codes'          => 'table principale des codes de langue (ISO 639-1,2 et 3)',
	'label_table_iso639families'       => 'table des familles et groupes de langues (ISO 639-5)',
	'label_table_iso639macros'         => 'table des macrolangues',
	'label_table_iso639names'          => 'table des noms de langues',
	'label_table_iso639retirements'    => 'table des langues supprimées',
	'label_tables'                     => 'Tables sur lesquelles appliquer l\'action',

	// N
	'notice_charger_table'          => 'Aucune mise à jour n\'est nécessaire sur la ou les tables « @tables@ ».',
	'notice_liste_aucune_table'     => 'Aucune table de codes ISO n\'a encore été chargée en base de données. Utiliser le formulaire ci-dessous pour y remédier.',

	// S
	'succes_charger_table'          => 'La ou les tables « @tables@ » ont bien été chargées.',
	'succes_decharger_table'        => 'La ou les tables « @tables@ » ont bien été vidées.',

	// T
	'titre_form_gerer_table'        => 'Vider ou charger des tables',
	'titre_liste_tables'            => 'Liste des nomenclatures officielles chargées en base de données',
	'titre_page'                    => 'Gestion des nomenclatures',
);
