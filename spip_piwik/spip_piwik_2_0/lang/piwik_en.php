<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Create the site',

	// C
	'cfg_description_piwik' => 'You can specify your piwik identifier here, as well as the the address of your statistics server.',
	'cfg_erreur_recuperation_data' => 'There was a server communication problem - please check the address and token',
	'cfg_erreur_token' => 'Your identification token is invalid',

	// E
	'explication_adresse_serveur' => 'Enter the URL address without "http://" or "https://" and without the final slash',
	'explication_creer_site' => 'The link below enables you to create a site on the Piwik server which will then be available from the list.Check that you have correctly entered the address and name of your SPIP site before clicking, as these are the details will be used later.',
	'explication_exclure_ips' => 'To nominate several addresses to be excluded, separate them with semi-colons',
	'explication_identifiant_site' => 'The list of sites available on the Piwik server has been automatically retrieved using the submitted details. Select the one you wish to use from the list below',
	'explication_mode_insertion' => 'There are two methods for inserting the code pages required to make the plugin work correctly: either with the "insert_head" pipeline (an automatic method with only minor configurations possible), or by inserting a tag (a manual method of including the #PIWIK tag at the bottom of your pages), which is fully configurable.',
	'explication_restreindre_statut_prive' => 'Select the user statuses which will not be taken into account in the private zone statistics',
	'explication_restreindre_statut_public' => 'Select the user statuses which will not be taken into account in the public zone statistics',
	'explication_token' => 'The identification token is available in your personal preferences or in the API section on your Piwik server',

	// L
	'label_adresse_serveur' => 'URL address of the server (https:// or http://)',
	'label_comptabiliser_prive' => 'Include visits to the private space',
	'label_creer_site' => 'Create a site on the Piwik server',
	'label_exclure_ips' => 'Exclude certain IP addresses',
	'label_identifiant_site' => 'The identifier of your site on the Piwik server',
	'label_mode_insertion' => 'Insert mode for the public pages',
	'label_restreindre_auteurs_prive' => 'Restrict certain logged in users (private)',
	'label_restreindre_auteurs_public' => 'Restrict certain logged in users (public)',
	'label_restreindre_statut_prive' => 'Restrict certain user statuses in the private zone',
	'label_restreindre_statut_public' => 'Restrict certain user statuses in the public zone',
	'label_token' => 'Identification token on the server',

	// M
	'mode_insertion_balise' => 'Insert using the #PIWIK tag (you must modify your templates)',
	'mode_insertion_pipeline' => 'Automatic insertion using the "insert_head" pipeline',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Your identifier',
	'textes_url_piwik' => 'Your piwik server'
);

?>
