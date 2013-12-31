<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Maak de site',
	'action_recuperer_liste' => 'Haal de sitelijst op',

	// C
	'cfg_description_piwik' => 'Hier kun je je Piwik identificatie vermelden, alsmede de naam van de server voor jouw statistieken.',
	'cfg_erreur_recuperation_data' => 'Er is een communicatiefout met de server. Controleer het adres en de token',
	'cfg_erreur_token' => 'Je identificatie-token is ongeldig',
	'cfg_erreur_user_token' => 'De combinatie gebruikersnaam / token is onjuist.',

	// E
	'explication_adresse_serveur' => 'Voer het adres in zonder "http://" of "https://" en ook geen schuine streep aan het einde',
	'explication_creer_site' => 'Met onderstaande link kun je een site op de Piwik server aanmaken die vervolgens in de lijst beschikbaar komt. Controleer alvorens te klikken de juiste configuratie van het adres en de naam van je SPIP site. Dit zijn de gegevens die zullen worden gebruikt.',
	'explication_exclure_ips' => 'Om meerdere adressen uit te sluiten gebruik je een puntkomma als scheidingsteken',
	'explication_identifiant_site' => 'De lijst van beschikbare sites op de Piwik server werd aan de hand van de verstrekte gegevens automatisch opgehaald. Maak uit onderstaande lijst je keuze',
	'explication_mode_insertion' => 'Er zijn twee manieren om op de bladzijdes de code in te voeren die de plugin goed laat functioneren. Via de pipeline "insert_head" (automatisch, maar weinig configureerbaar), of door het invoegen van een tag (handmatig invoeren van de tag #PIWIK onder een bladzijde) wat een volop configureerbare oplossing biedt.',
	'explication_recuperer_liste' => 'Met onderstaande link kun je de lijst ophalen van door jouw te beheren sites op de Piwik server.',
	'explication_restreindre_statut_prive' => 'Kies hier de statussen van gebruikers die niet moeten worden meegeteld in de statistieken van de privé-ruimte',
	'explication_restreindre_statut_public' => 'Kies hier de statussen van gebruikers die niet moeten worden meegeteld in de statistieken van de publieke site',
	'explication_token' => 'De identificatie-token is beschikbaar in je persoonlijke voorkeuren of in het API-gedeelte van je Piwik server',

	// I
	'info_aucun_site_compte' => 'Geen enkele site is aan jouw Piwik account gekoppeld.',
	'info_aucun_site_compte_demander_admin' => 'Je zult een beheerder van je Piwik server moeten vragen om een site toe te voegen.',

	// L
	'label_adresse_serveur' => 'URL-adres van de server (https:// of http://)',
	'label_comptabiliser_prive' => 'Bezoeken aan de privé-ruimte meetellen',
	'label_creer_site' => 'Maak een site op de Piwik server',
	'label_exclure_ips' => 'Sluit bepaalde IP-adressen uit',
	'label_identifiant_site' => 'De identificatie van je site op de Piwik server',
	'label_mode_insertion' => 'Invoegmethode in de bladzijdes van de publieke site',
	'label_piwik_user' => 'Piwik gebruikersaccount',
	'label_recuperer_liste' => 'De sitelijst ophalen van de Piwik server',
	'label_restreindre_auteurs_prive' => 'Sluit bepaalde aangesloten gebruikers uit (privé)',
	'label_restreindre_auteurs_public' => 'Sluit bepaalde aangesloten gebruikers uit (publiek)',
	'label_restreindre_statut_prive' => 'Sluit gebruikers met bepaalde statussen uit (privé)',
	'label_restreindre_statut_public' => 'Sluit gebruikers met bepaalde statussen uit (publiek)',
	'label_token' => 'Identificatie-token op de server',

	// M
	'mode_insertion_balise' => 'Invoegen van tag #PIWIK (handmatig aanpassen van skeletten noodzakelijk)',
	'mode_insertion_pipeline' => 'Automatisch invoegen via de pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Je identificatie',
	'textes_url_piwik' => 'Je Piwik server'
);

?>
