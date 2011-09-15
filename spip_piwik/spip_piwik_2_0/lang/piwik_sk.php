<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Vytvoriť stránku',
	'action_recuperer_liste' => 'Získať zoznam stránok',

	// C
	'cfg_description_piwik' => 'Tu môžete uviesť svoj identifikátor pre piwik, ako aj adresu svojho štatistického servera.',
	'cfg_erreur_recuperation_data' => 'Pri komunikácii so serverom sa vyskytol problém &ndash; prosím, skontrolujte adresu a token',
	'cfg_erreur_token' => 'Your identification token is invalid', # NEW
	'cfg_erreur_user_token' => 'The username and token do not match each other.', # NEW

	// E
	'explication_adresse_serveur' => 'Zadajte adresu stránky bez "http://" alebo "https://" a bez lomky na konci',
	'explication_creer_site' => 'The link below enables you to create a site on the Piwik server which will then be available from the list.Check that you have correctly entered the address and name of your SPIP site before clicking, as these are the details will be used later.', # NEW
	'explication_exclure_ips' => 'Ak vymenujete niekoľko adries, ktoré majú byť vylúčené, oddeľte ich bodkočiarkami',
	'explication_identifiant_site' => 'The list of sites available on the Piwik server has been automatically retrieved using the submitted details. Select the one you wish to use from the list below', # NEW
	'explication_mode_insertion' => 'There are two methods for inserting the code pages required to make the plugin work correctly: either with the "insert_head" pipeline (an automatic method with only minor configurations possible), or by inserting a tag (a manual method of including the #PIWIK tag at the bottom of your pages), which is fully configurable.', # NEW
	'explication_recuperer_liste' => 'The link below is used to retrieve the lists of sites that your account can manage on the Piwik server.', # NEW
	'explication_restreindre_statut_prive' => 'Select the user statuses which will not be taken into account in the private zone statistics', # NEW
	'explication_restreindre_statut_public' => 'Select the user statuses which will not be taken into account in the public zone statistics', # NEW
	'explication_token' => 'The identification token is available in your personal preferences or in the API section on your Piwik server', # NEW

	// I
	'info_aucun_site_compte' => 'K vášmu účtu na Piwiku nie sú priradené žiadne stránky.',
	'info_aucun_site_compte_demander_admin' => 'O pridanie zodpovedajúcej stránky musíte požiadať administrátora svojho servera s Piwikom.',

	// L
	'label_adresse_serveur' => 'Internetová adresa servera (https:// alebo http://)',
	'label_comptabiliser_prive' => 'Pridať návštevy súkromnej zóny',
	'label_creer_site' => 'Vytvoriť stránku na serveri s Piwikom',
	'label_exclure_ips' => 'Vylúčiť určité IP adresy',
	'label_identifiant_site' => 'Identifikátor vašej stránky na serveri s Piwikom',
	'label_mode_insertion' => 'Režim vkladania pre verejne prístupné stránky',
	'label_piwik_user' => 'Používateľský účet Piwiku',
	'label_recuperer_liste' => 'Získať zoznam stránok na serveri s Piwikom',
	'label_restreindre_auteurs_prive' => 'Obmedziť prístup určitým prihláseným používateľom (súkromná zóna)',
	'label_restreindre_auteurs_public' => 'Obmedziť prístup určitým prihláseným používateľom (verejné)',
	'label_restreindre_statut_prive' => 'Obmedziť prístup do súkromnej zóny pre určité funkcie',
	'label_restreindre_statut_public' => 'Obmedziť prístup do súkromnej zóny pre určité funkcie',
	'label_token' => 'Identification token on the server', # NEW

	// M
	'mode_insertion_balise' => 'Vložiť pomocou tagu #PIWIK (musíte upraviť svoje šablóny)',
	'mode_insertion_pipeline' => 'Automatické vkladanie pomocou reťazenia údajov "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Váš identifikátor',
	'textes_url_piwik' => 'Váš server pre piwik'
);

?>
