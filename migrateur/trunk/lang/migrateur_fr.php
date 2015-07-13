<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(


	// C
	'cle_authentification_perimee' => "Clé d'authentification non renseignée ou périmée.",
	'cle_valide_une_heure'       => "Clé d'authentification valide seulement @nb@ heure",
	'cle_valide_nb_heures'       => "Clé d'authentification valide encore @nb@ heures",
	'conf_aes_key'               => 'Clé AES',
	'conf_auth_key'              => "Clé d'authentification",
	'conf_chemin'                => 'Chemin',
	'conf_ce_site_est'           => 'Ce site est défini comme',
	'conf_configurer_migrateur'  => '[Configurer le migrateur->?exec=configurer_migrateur]',
	'conf_destination'           => 'Destination',
	'conf_non_configure'         => 'Non configuré',
	'conf_source'                => 'Source',
	'conf_sql_user'              => 'Utilisateur SQL',
	'conf_sql_bdd'               => 'Base de données',
	'conf_url_source'            => 'URL du site source',
	'conf_verification_visuelle' => "Vous n'avez pas encore configuré le migrateur
		sur ce site. Cette étape est indispensable pour le fonctionnement.",

	// E
	'erreur_cle_authentification' => "Clé d'authentification périmée.",
	'etape_suivante' => 'Étape suivante',
	'explication_configuration_migrateur' => "Le migrateur doit être configuré comme étant source de données
		 (auquel cas des clés d'authentification et de cryptage seront générées) ou comme étant
		 le site de destination vers lequel on migre (dans ce cas, il faudra remplir les clés qui ont été
		 calculées par le migrateur du site source.",

	// G
	'generer_cle_auth' => "Générer une nouvelle clé d'authentification",
	'generer_cles' => "Générer toutes les clés",

	// M
	'migrateur_configurer'       => 'Configurer le migrateur',
	'migrateur_page'             => 'Page du migrateur',
	'migrateur_titre'            => 'Migrateur de site',
	'migrateur_titre_court'      => 'Migrateur',

	// R
	'recommencer_etapes' => 'Recommencer les étapes ?',

);

?>
