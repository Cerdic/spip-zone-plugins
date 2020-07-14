<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bouton_isocode'                => 'Nomenclatures officielles',

	// E
	'erreur_400_zone_nok_titre'        => 'La zone géographique d\'identifiant « @valeur@ » est invalide',
	'erreur_400_zone_nok_message'      => 'La zone géographique doit être désignée par son identifiant UN M49 (3 chiffres)',
	'erreur_400_continent_nok_titre'   => 'Le continent d\'identifiant « @valeur@ » est invalide',
	'erreur_400_continent_nok_message' => 'Le continent doit être désignée par son identifiant GeoIP (2 lettres)',
	'erreur_charger'                   => 'Erreur de chargement pour « @elements@ ».',
	'erreur_decharger'                 => 'Erreur de vidage pour « @elements@ ».',
	'explication_action_nomenclature'  => 'Si les tables sont déjà chargées en base de données elle seront vidées avant le chargement.',
	'explication_action_geometrie'     => 'Si les contours sont déjà chargés en base de données ils seront vidés avant le chargement.',

	// G
	'geometrie_urssafregfr'                => 'Contours des régions françaises, URSSAF',
	'geometrie_urssafdepfr'                => 'Contours des départements françaises, URSSAF',
	'geometrie_mapofglobe'                 => 'Contours des pays, Map of Globe',

	// I
	'info_charge' => 'chargé',

	// L
	'label_action_charger'                       => 'Charger',
	'label_action'                               => 'Action à exécuter',
	'label_action_decharger'                     => 'Vider',
	'label_colonne_actualisation'                => 'Actualisé le',
	'label_colonne_libelle'                      => 'Description',
	'label_colonne_service'                      => 'Service',
	'label_colonne_statistiques'                 => 'Compteur',
	'label_colonne_table'                        => 'Table',
	'label_element_geometrie'                    => 'Contours sur lesquels appliquer l\'action',
	'label_element_nomenclature'                 => 'Tables sur lesquelles appliquer l\'action',
	'label_groupe_langue'                        => 'Langues',
	'label_groupe_geographie'                    => 'Informations géographiques',

	// M
	'menu_nomenclature' => 'Nomenclatures',
	'menu_geometrie'    => 'Contours géométriques',

	// N
	'nomenclature_geoipcontinents'         => 'table des codes GeoIP des continents',
	'nomenclature_iana5646subtags'         => 'table reproduisant le registre IANA des sous-étiquettes de langues (RFC 5646)',
	'nomenclature_iso15924scripts'         => 'table des indicatifs d\'écritures (ISO 15924)',
	'nomenclature_iso3166alternates'       => 'table des codes alternatifs des subdivisions',
	'nomenclature_iso3166countries'        => 'table des indicatifs des pays (ISO 3166-1)',
	'nomenclature_iso3166subdivisions'     => 'table des indicatifs des subdivisions des pays (ISO 3166-2)',
	'nomenclature_m49regions'              => 'table des indicatifs des régions du monde selon l\'ONU (UN M.49)',
	'nomenclature_iso4217currencies'       => 'table des devises (ISO 4217)',
	'nomenclature_iso639codes'             => 'table principale des codes de langue (ISO 639-1,2 et 3)',
	'nomenclature_iso639families'          => 'table des familles et groupes de langues (ISO 639-5)',
	'nomenclature_iso639macros'            => 'table des macrolangues',
	'nomenclature_iso639names'             => 'table des noms de langues',
	'nomenclature_iso639retirements'       => 'table des langues supprimées',
	'notice_charger'            => 'Aucune mise à journécessaire pour « @elements@ ».',
	'notice_geometrie_aucun_chargement' => 'Aucun contour géométrique n\'a encore été chargé en base de données. Utiliser le formulaire ci-dessous pour y remédier.',
	'notice_nomenclature_aucun_chargement' => 'Aucune table de nomenclatures n\'a encore été chargée en base de données. Utiliser le formulaire ci-dessous pour y remédier.',

	// S
	'succes_charger'   => 'Chargement ok pour « @elements@ ».',
	'succes_decharger' => 'Vidage ok pour « @elements@ ».',

	// T
	'titre_form_gerer_table'      => 'Vider ou charger des tables',
	'titre_form_gerer_geometrie'  => 'Vider ou charger des ensembles de contours géographiques',
	'titre_liste_tables'          => 'Liste des nomenclatures officielles chargées en base de données',
	'titre_liste_geometries'      => 'Liste des contours géométriques chargés en base de données',
	'titre_page_geometrie'        => 'Gestion des contours géométriques',
	'titre_page_nomenclature'     => 'Gestion des nomenclatures',
);
