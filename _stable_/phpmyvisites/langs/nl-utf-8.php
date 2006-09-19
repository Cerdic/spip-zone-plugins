<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

//
// Technical information
//
$lang['auteur_nom'] = "Katrien Cogghe"; // naam van de vertaler
$lang['auteur_email'] = "katrien AT linux.be"; // e-mail adres van de vertaler
$lang['charset'] = "utf-8"; // charset bestandsnaam (default utf-8)
$lang['text_dir'] = "ltr"; // ('ltr' van links naar rechts, 'rtl' van rechts naar links)
$lang['lang_iso'] = "nl"; // iso taal code
$lang['lang_libelle_en'] = "Dutch"; // de naam van de taal in het engels
$lang['lang_libelle_fr'] = "Néerlandais"; // de naam van de taal in het frans
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb');
$lang['separateur_milliers'] = ' '; // driehonderdduizend schrijft men in het nederlands als 300 000
$lang['separateur_decimaux'] = ','; // scheidingsteken tussen de eenheden en de decimalen

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | Een open source webapplicatie voor het bijhouden van webstatistieken en het uitvoeren van verkeeranalyse"; // Titel van de pagina in de header html
$lang['head_keywords'] = "phpmyvisites, php, script, applicatie, programma, statistieken, publiek, stats, gratis, open source, gpl, bezoeken, bezoekers, mysql, bezochte pagina's, pagina's, weergaves, aantal bezoeken, grafisch, browsers, os, besturingssystemen, resolutie, dag, week, maand, records, landen, host, service providers, zoekrobot, sleutelwoorden, opvolging, herkomst, grafieken, eerste pagina's, laatste pagina's, taartdiagram"; // Sleutelwoorden voor de html-header
$lang['head_description'] = "phpMyVisites | Een open source applicatie voor het bijhouden van webstatistieken ontworpen in PHP/MYSQL en verspreid onder de GNU GPL licentie."; // Header description
$lang['logo_description'] = "phpMyVisites : Een open source applicatie voor het bijhouden van webstatistieken ontworpen in PHP/MYSQL en verspreid onder de GNU GPL licentie."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "Bezoekersë";
$lang['menu_pagesvues'] = "Bezochte pags";
$lang['menu_suivi'] = "Beweging";
$lang['menu_provenance'] = "Bron";
$lang['menu_configurations'] = "Instellingen";
$lang['menu_affluents'] = "Herkomst";
$lang['menu_listesites'] = "Lijst sites";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Dag";
$lang['menu_semaine'] = "Week";
$lang['menu_mois'] = "Maand";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Bestudeerde periode : %s"; // Bestudeerde persiode bv.: maandag 11 november
$lang['liens_siteofficiel'] = "Officiële site";
$lang['liens_admin'] = "Installatie &amp; configuratie";
$lang['liens_contacts'] = "Contacten";

//
// Divers
//
$lang['generique_nombre'] = "Nummer";
$lang['generique_tauxsortie'] = "Uitgaande bezoekersverhouding";
$lang['generique_ok'] = "OK";
$lang['generique_timefooter'] = "Pagina werd gegenereerd in %s seconden"; // tijd in seconden
$lang['generique_divers'] = "Volgende"; // (voor de grafieken)
$lang['generique_inconnu'] = "Onbekend"; // (voor de grafieken)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Andere ?"; // Andere taal, vertalers gezocht
$lang['aucunvisiteur_titre'] = "No visitor in this period."; 
$lang['generique_aucune_visite_bdd'] = "<b>Warning ! </b> You have no visitor recorded in the database for the current site. Please be sure you've installed your javascript code on your pages, with the correct phpMyVisites URL <u>IN</u> the Javascript code. Try documentation for help.";
$lang['generique_aucune_site_bdd'] = "No site registered in the database ! Try to Try to login as phpMyVisites Super User to add a new site.";
$lang['generique_retourhaut'] = "Top";
$lang['generique_tempsvisite'] = "%smin %ss"; // 3min 25s means 3 minutes and 25 seconds
$lang['generique_tempsheure'] = "%sh"; // 4h means 4 hours
$lang['generique_siteno'] = "Site %s"; // Site "phpmyvisites"
$lang['generique_newsletterno'] = "Newsletter %s"; // Newsletter "version 2 announcement"
$lang['generique_partnerno'] = "Partner %s"; // Partner "version 2 announcement"
$lang['generique_general'] = "General";
$lang['generique_user'] = "User %s"; // User "Admin"
$lang['generique_previous'] = "Previous";
$lang['generique_next'] = "Next";
$lang['generique_lowpop'] = "Exclude low population from statistics";
$lang['generique_allpop'] = "Include all the population in statistics";
$lang['generique_to'] = "to"; // 4 'to' 8
$lang['generique_total_on'] = "on"; // 4 to 8 'on' 10
$lang['generique_total'] = "TOTAAL";
$lang['generique_information'] = "Information";
$lang['generique_done'] = "Done!";
$lang['generique_other'] = "Other";
$lang['generique_description'] = "Description:";
$lang['generique_name'] = "Name:";
$lang['generique_variables'] = "Variables";
$lang['generique_logout'] = "Logout";
$lang['generique_login'] = "Login";
$lang['generique_hits'] = "Hits";
$lang['generique_errors'] = "Errors";
$lang['generique_site'] = "Site";

//
// Authentication
//
$lang['login_password'] = "paswoord :"; // kleine letters
$lang['login_login'] = "login :"; // kleine letters
$lang['login_error'] = "Kan niet inloggen : verkeerde login/paswoord.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Contacten";
$lang['contacts_langue'] = "Vertalingen";
$lang['contacts_merci'] = "Dank";
$lang['contacts_auteur'] = "De auteur en ontwerper van het phpMyVisites project is <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "Voor <strong>technische vragen, het melden van fouten en eventuele suggesties</strong> gelieve het forum van de offici‰le website te raadplegen %s . Voor andere vragen, gelieve de auteur te contacteren aan de hand van de offici‰le website."; // adres van de website
$lang['contacts_trad1'] = "Wil je phpMyVisites vertalen naar je eigen taal? Aarzal ons dan niet te contacteren want <strong>phpMyVisites heeft je nodig!</strong>";
$lang['contacts_trad2'] = "PhpMyVisites vertalen duurt slechts enkele uurtjes en een goede beheersing van de betrokken talen; maar houd in gedachte dat <strong>elk werk dat je verricht, vele gebruikers ten goede komt</strong>.  Meer informatie omtrent het vertalen van phpMyVisites kun je vinden in de %s offici‰le documentatie van phpMyVisites %s."; // link naar de documentatie
$lang['contacts_doc'] = "Aarzel niet om de %s offici‰le documentatie van phpMyVisites %s te raadplegen. Het geeft je een compleet overzicht van het installatieproces, de configuratie en de functionaliteit van phpMyVisites. Het is beschikbaar voor jou versie van phpMyVisites."; // link naar de documentatie
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "Aarzel niet om de gebruikerspagina van de offici‰le website te raadplegen. Je krijgt er een volldige lijst van alle phpMyVisites medewerkers.";
$lang['contacts_merci2'] = "Een welgemeende dank aan allen die hun kennis hebben gedeeld bij het vertalen van phpMyVisites:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Informatie van de bezoekers";
$lang['visites_statistiques'] = "Statistieken";
$lang['visites_periodesel'] = "Voor de geselecteerde periode";
$lang['visites_visites'] = "Aantal bezoekers";
$lang['visites_uniques'] = "Unieke bezoekers";
$lang['visites_pagesvues'] = "Bezochte pagina's";
$lang['visites_pagesvisiteurs'] = "Pagina's per bezoeker";
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Gemiddelde duur van een bezoek";
$lang['visites_tempsmoyenpv'] = "Gemiddelde duur van een bezochte pagina";
$lang['visites_tauxvisite'] = "Uitgaande bezoekersverhouding";
$lang['visites_recapperiode'] = "Samenvatting periode";
$lang['visites_nbvisites'] = "Aantal bezoekers";
$lang['visites_aucunevivisite'] = "Geen bezoekers"; // in een tabel, moet kort zijn
$lang['visites_recap'] = "Samenvatting";
$lang['visites_unepage'] = "1 pagina"; // (graph)
$lang['visites_pages'] = "%s pagina's"; // 1-2 pages (graph)
$lang['visites_min'] = "%s min"; // 10-15 min (graph)
$lang['visites_sec'] = "%s sec"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "Grafiek : Samenvatting van de statistieken";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Grafiek : Gemiddelde duur van een bezoek";
$lang['visites_graphtempsvisitesimg'] = "Gemiddelde duur van een bezoek";
$lang['visites_graphheureserveur'] = "Grafiek : Aantal bezoekers per uur volgens servertijd";
$lang['visites_graphheureserveurimg'] = "Aantal bezoekers per uur volgens servertijd";
$lang['visites_graphheurevisiteur'] = "Grafiek : Aantal bezoekers per uur volgens lokale tijd";
$lang['visites_graphheurelocalimg'] = "Aantal bezoekers per uur volgens lokale tijd";
$lang['visites_longterm_statd'] = "Long term Analysis (Days of the Period)";
$lang['visites_longterm_statm'] = "Long term Analysis (Months in the Period)";

//
// Sites Summary
//
$lang['summary_title'] = "Site Summary";
$lang['summary_stitle'] = "Summary";

//
// Frequency Part
//
$lang['frequence_titre'] = "Returning visitors";
$lang['frequence_nouveauxconnusgraph'] = "Graph to show New vs Returning visits";
$lang['frequence_nouveauxconnus'] = "New vs Returning visits";
$lang['frequence_titremenu'] = "Frequency";
$lang['frequence_visitesconnues'] = "Returning visits";
$lang['frequence_nouvellesvisites'] = "New visits";
$lang['frequence_visiteursconnus'] = "Returning visitors";
$lang['frequence_nouveauxvisiteurs'] = "New visitors";
$lang['frequence_returningrate'] = "Returning rate";
$lang['pagesvues_vispervisgraph'] = "Graph to show number of visits per visitor";
$lang['frequence_vispervis'] = "Number of visits per visitor";
$lang['frequence_vis'] = "visit";
$lang['frequence_visit'] = "1 visit"; // (graph)
$lang['frequence_visits'] = "%s visits"; // (graph)

//
// Seen Pages
//
$lang['pagesvues_titre'] = "Informatie van de bezochte pagina's";
$lang['pagesvues_joursel'] = "Geselecteerde dag";
$lang['pagesvues_jmoins7'] = "Dag - 7";
$lang['pagesvues_jmoins14'] = "Dag - 14";
$lang['pagesvues_moyenne'] = "(gemiddeld)";
$lang['pagesvues_pagesvues'] = "Aantal bezochte pagina's";
$lang['pagesvues_pagesvudiff'] = "Aantal uniek bezochte pagina's";
$lang['pagesvues_recordpages'] = "Hoogst aantal pagina's bezocht door ‚‚n bezoeker";
$lang['pagesvues_tabdetails'] = "Bezochte pagina's (van %s tot %s)"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "Grafiek : Aantal pagina's per bezoeker";
$lang['pagesvues_graphnbvisitespageimg'] = "Aantal pagina's per bezoeker";
$lang['pagesvues_graphheureserveur'] = "Grafiek : Aantal bezoekers per uur volgens servertijd";
$lang['pagesvues_graphheureserveurimg'] = "Aantal bezoekers per uur volgens servertijd";
$lang['pagesvues_graphheurevisiteur'] = "Grafiek : Aantal bezoekers per uur volgens lokale tijd";
$lang['pagesvues_graphpageslocalimg'] = "Aantal bezoekers per uur volgens lokale tijd";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Beweging van de bezoeker";
$lang['suivi_pageentree'] = "Eerste pagina's";
$lang['suivi_pagesortie'] = "Laatste pagina's";
$lang['suivi_tauxsortie'] = "Uitgaande bezoekersverhouding";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Herkomst van de bezoekers";
$lang['provenance_recappays'] = "Samenvatting landen";
$lang['provenance_pays'] = "Landen";
$lang['provenance_paysimg'] = "Bezoekers op landenkaart";
$lang['provenance_fai'] = "Internet Service Providers";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Providers"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Continent";
$lang['provenance_mappemonde'] = "Wereldkaart";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Instellingen van de bezoekers";
$lang['configurations_os'] = "Besturingssystemen";
$lang['configurations_osimg'] = "Grafiek : Aantal bezoekers volgens hun besturingssysteem";
$lang['configurations_navigateurs'] = "Browsers";
$lang['configurations_navigateursimg'] = "Grafiek : Aantal bezoekers volgens hun brower";
$lang['configurations_resolutions'] = "Schermresolutie";
$lang['configurations_resolutionsimg'] = "Grafiek : Aantal bezoekers volgens hun schermresolutie";
$lang['configurations_couleurs'] = "Kleurendiepte";
$lang['configurations_couleursimg'] = "Grafiek : Aantal bezoekers volgens hun kleurendiepte";
$lang['configurations_rapport'] = "Normaal/breedbeeld";
$lang['configurations_large'] = "Breedbeeld";
$lang['configurations_normal'] = "Normaal";
$lang['configurations_double'] = "Dual Screen";
$lang['configurations_plugins'] = "Plugins"; // TODO : translate
$lang['configurations_navigateursbytype'] = "Browsers (by type)"; // TODO : translate
$lang['configurations_navigateursbytypeimg'] = "Graph to show browsers types"; // TODO : translate
$lang['configurations_os_interest'] = "Operating Systems Interest";
$lang['configurations_navigateurs_interest'] = "Browsers Interest";
$lang['configurations_resolutions_interest'] = "Screen Resolutions Interest";
$lang['configurations_couleurs_interest'] = "Color Depth Interest";
$lang['configurations_configurations'] = "Top settings";

//
// Referers
//
$lang['affluents_titre'] = "Bezoekers volgens hun herkomst";
$lang['affluents_recapimg'] = "Aantal beoekers volgens hun herkomst";
$lang['affluents_directimg'] = "Direct";
$lang['affluents_sitesimg'] = "Websites";
$lang['affluents_moteursimg'] = "Zoekrobots";
$lang['affluents_referrersimg'] = "Herkomst";
$lang['affluents_moteurs'] = "Zoekrobots";
$lang['affluents_nbparmoteur'] = "Aantal bezoekers onstaan door zoekrobots : %s";
$lang['affluents_aucunmoteur'] = "Er werden geen bezoekers geregistreerd afkomstig door zoekrobots.";
$lang['affluents_motscles'] = "Sleutelwoorden";
$lang['affluents_nbmotscles'] = "Verschillende sleutelwoorden : %s";
$lang['affluents_aucunmotscles'] = "Geen sleutelwoorden gevonden.";
$lang['affluents_sitesinternet'] = "Websites";
$lang['affluents_nbautressites'] = "Bezoeken ontstaan door andere websites : %s";
$lang['affluents_nbautressitesdiff'] = "Aantal verschillende websites : %s";
$lang['affluents_aucunautresite'] = "Er werden geen bezoekers geregistreerd afkomstig van andere websites.";
$lang['affluents_entreedirecte'] = "Directe aanvraag";
$lang['affluents_nbentreedirecte'] = "Aantal bezoekers door een directe aanvraag : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Details"; // In the results of the referers array
$lang['affluents_interetsmoteurs'] = "Search Engines Interests";
$lang['affluents_interetsmotscles'] = "Keywords Interests";
$lang['affluents_interetssitesinternet'] = "Websites Interests";
$lang['affluents_partenairesimg'] = "Partners";
$lang['affluents_partenaires'] = "Partners";
$lang['affluents_interetspartenaires'] = "Partners Interests";
$lang['affluents_newslettersimg'] = "Newsletters";
$lang['affluents_newsletters'] = "Newsletters";
$lang['affluents_interetsnewsletters'] = "Newsletters Interests";
$lang['affluents_type'] = "Referer type";
$lang['affluents_interetstype'] = "Access type Interests";

//
// Summary
//
$lang['purge_titre'] = "Samenvatting van de bezoekers en hun afkomst";
$lang['purge_intro'] = "Deze periode werd verwijderd door de administratie, enkel de essenti‰le statistieken werden behouden.";
$lang['admin_purge'] = "Onderhoud database";
$lang['admin_purgeintro'] = "In dit onderdeel kun je de tabellen, die gebruikt worden door phpMyVisites, beheren. Je kunt de grootte zien van elke tabel, elke tabel optimaliseren of oude records zuiveren. Dit heeft als doel de grootte van de tabellen in de database te optimaliseren.";
$lang['admin_optimisation'] = "Optimalisatie van [ %s ]..."; // Tables names
$lang['admin_postopt'] = "De totale afgenomen grootte: %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "Volgende periode verwijderd: %s";
$lang['admin_purge_fini'] = "Operatie zuiveren van tabellen beeidigd...";
$lang['admin_bdd_nom'] = "Naam";
$lang['admin_bdd_enregistrements'] = "Records";
$lang['admin_bdd_taille'] = "Grootte tabel";
$lang['admin_bdd_opt'] = "Optimaliseer";
$lang['admin_bdd_purge'] = "Zuiveringscriteria";
$lang['admin_bdd_optall'] = "Alles geoptimaliseerd";
$lang['admin_purge_j'] = "Verwijder records ouder dan %s dagen";
$lang['admin_purge_s'] = "Verwijder records ouder dan %s weken";
$lang['admin_purge_m'] = "Verwijder records ouder dan %s maanden";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Verwijder alle logs.";
$lang['admin_purge_autres'] = "Algemene zuivering op de tabel '%s'";
$lang['admin_purge_none'] = "Geen actie mogelijk";
$lang['admin_purge_cal'] = "Bereken en zuiver (dit kan enkele minuten duren).";
$lang['admin_alias_title'] = "Website aliases and URLs";
$lang['admin_partner_title'] = "Website partners";
$lang['admin_newsletter_title'] = "Website newsletters";
$lang['admin_ip_exclude_title'] = "IP address ranges to exclude from the statistics";
$lang['admin_name'] = "Name:";
$lang['admin_error_ip'] = "IP has to be in correct format: %s";
$lang['admin_site_name'] = "Site Name";
$lang['admin_site_url'] = "Site main URL";
$lang['admin_db_log'] = "Try to login as phpMyVisites Super User to change database settings.";
$lang['admin_error_critical'] = "Error, needs to be repaired for phpMyVisites to work.";
$lang['admin_warning'] = "Warning, phpMyVisites will work correctly but maybe some extra features won't.";
$lang['admin_move_group'] = "Move to group:";
$lang['admin_move_select'] = "Select a group";

//
// Setup
//
$lang['admin_intro'] = "Welkom bij het configuratiegedeelte van phpMyVisites. Hier kunt u alle informatie wijzigen inzake uw installatie. Wanneer u problemen hebt, aarzel dan niet de %s offici‰le documentatie van phpMyVisites %s te raadplegen"; // lien vers la documentation
$lang['admin_configetperso'] = "Algemene configuratie";
$lang['admin_afficherjavascript'] = "Javascriptcode tonen";
$lang['admin_cookieadmin'] = "Administrator uitsluiten bij de telling";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Lijst van de geregistreerde websites :";
$lang['admin_retour'] = "Terug";
$lang['admin_cookienavigateur'] = "Je kunt de Administrator uitsluiten van de statistieken. Deze methode is gebaseerd op cookies en deze optie zal enkel werken met de huidige browser. Je kunt deze optie op elk moment wijzigen.";
$lang['admin_prendreencompteadmin'] = "Tel de administrator mee in de statistieken (verwijder de cookie).";
$lang['admin_nepasprendreencompteadmin'] = "Tel de administrator niet mee in de statistieken (maak een cookie aan).";
$lang['admin_etatcookieoui'] = "De administrator wordt meegeteld in de statistieken voor deze website (dit is de standaard configuratie, je wordt beschouwd als een normale bezoeker).";
$lang['admin_etatcookienon'] = "Je wordt niet bij de statistieken voor deze website geteld (Je bezoeken zullen niet bijgeteld worden bij deze website).";
$lang['admin_deleteconfirm'] = "Please confirm that you want to delete %s?";
$lang['admin_sitedeletemessage'] = "Please <u>be very careful</u>: all data associated to that Site will be deleted <br>and there won't be any way to recover the data loss.";
$lang['admin_confirmyes'] = "Yes, I want to delete it";
$lang['admin_confirmno'] = "No, I don't want to delete it";
$lang['admin_nonewsletter'] = "No newsletter found for this site!";
$lang['admin_nopartner'] = "No Partner found for this site!";
$lang['admin_get_question'] = "Record GET variable? (URL variables)";
$lang['admin_get_a1'] = "Record ALL URL variables";
$lang['admin_get_a2'] = "Do NOT record any URL variable";
$lang['admin_get_a3'] = "Record ONLY specified variables";
$lang['admin_get_a4'] = "Record all EXCEPT specified variables";
$lang['admin_get_list'] = "Variable names (<b>;</b> separated list) <br/>Example : %s";
$lang['admin_required'] = "%s is required.";
$lang['admin_title_required'] = "Required";
$lang['admin_write_dir'] = "Writable directories";
$lang['admin_chmod_howto'] = "These directories need to be writable by the server. This means you have to chmod 777 them, with your FTP software (right-click on the directory -> Permissions (or chmod))";
$lang['admin_optional'] = "Optional";
$lang['admin_memory_limit'] = "Memory limit";
$lang['admin_allowed'] = "allowed";
$lang['admin_webserver'] = "Web server";
$lang['admin_server_os'] = "Server OS";
$lang['admin_server_time'] = "Server time";
$lang['admin_legend'] = "Legend:";
$lang['admin_error_url'] = "URL has to be in a correct format : %s (without slash at the end)";
$lang['admin_url_n'] = "URL %s:";
$lang['admin_url_aliases'] = "URLs aliases";
$lang['admin_logo_question'] = "Display logo?";
$lang['admin_type_again'] = "(type again)";
$lang['admin_admin_mail'] = "Super Administrator email";
$lang['admin_admin'] = "Super Administrator";
$lang['admin_phpmv_path'] = "Complete path to the phpMyVisites application";
$lang['admin_valid_email'] = "Email has to be a valid email";
$lang['admin_valid_pass'] = "Password must be more complex (6 characters minimum, must contain numbers)";
$lang['admin_match_pass'] = "Passwords do not match";
$lang['admin_no_user_group'] = "No user in this group for this site";
$lang['admin_recorded_nl'] = "Recorded newsletters:";
$lang['admin_recorded_partners'] = "Recorded partners:";
$lang['admin_recorded_users'] = "Recorded users:";
$lang['admin_select_site_title'] = "Please select a site";
$lang['admin_select_user_title'] = "Please select a user";
$lang['admin_no_user_registered'] = "No user registered!";
$lang['admin_configuration'] = "Configuration";
$lang['admin_general_conf'] = "General configuration";
$lang['admin_group_title'] = "Groups manager (permissions)";
$lang['admin_user_title'] = "User management";
$lang['admin_user_add'] = "Add user";
$lang['admin_user_mod'] = "Modify user";
$lang['admin_user_del'] = "Delete user";
$lang['admin_server_info'] = "Server Information";
$lang['admin_send_mail'] = "Send statistics by email";
$lang['admin_rss_feed'] = "Statistics in a RSS feed";
$lang['admin_site_admin'] = "Site Administration";
$lang['admin_site_add'] = "Add site";
$lang['admin_site_mod'] = "Modify site";
$lang['admin_site_del'] = "Delete site";
$lang['admin_nl_add'] = "Add newsletter";
$lang['admin_nl_mod'] = "Modify newsletter";
$lang['admin_nl_del'] = "Delete newsletter";
$lang['admin_partner_add'] = "Add partner";
$lang['admin_partner_mod'] = "Modify partner's name and URL";
$lang['admin_partner_del'] = "Delete partner";
$lang['admin_url_alias'] = "URL alias Manager";
$lang['admin_group_admin_n'] = "View statistics + Admin permission";
$lang['admin_group_admin_d'] = "Users can view site statistics AND edit site information (name, add cookie, exclude IP ranges, manage URLs alias/partners/newsletters, etc.)";
$lang['admin_group_view_n'] = "View statistics";
$lang['admin_group_view_d'] = "User can only view site statistics. No admin permission.";
$lang['admin_group_noperm_n'] = "No permission";
$lang['admin_group_noperm_d'] = "Users in this group don't have any permission to view statistics or edit information.";
$lang['admin_group_stitle'] = "You can edit user's groups by selecting the users you want to change, and then select a group in which you want to move the selected users.";

//
// Installation Step
//
$lang['install_loginmysql'] = "Database login";
$lang['install_mdpmysql'] = "Database paswoord";
$lang['install_serveurmysql'] = "Database server";
$lang['install_basemysql'] = "Database naam";
$lang['install_prefixetable'] = "Voorzetsel tabel";
$lang['install_utilisateursavances'] = "Gevorderde gebruikers (optioneel)";
$lang['install_oui'] = "Ja";
$lang['install_non'] = "Neen";
$lang['install_ok'] = "OK";
$lang['install_probleme'] = "Probleem: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Administrator login:";
$lang['install_mdpadmin'] = "Administrator paswoord:";
$lang['install_chemincomplet'] = "Vervolledig het pad van de phpMyVisites applicatie (bijvoorbeeld http://www.mysite.com/rep1/rep3/phpmyvisites/). Het pad moet eindigen met een <strong>/</strong>.";
$lang['install_afficherlogo'] = "Het logo tonen op je pagina's? %s <br />By allowing the display of the logo on your site, you will help publicize phpMyVisites and help it evolve more rapidly.  It is also a way to thank the author who has spent many hours developing this Open Source, free application."; // %s toont je het logo
$lang['install_affichergraphique'] = "Toon de statistieken in de vorm van grafieken ?";
$lang['install_valider'] = "Ok"; //  tijdens de installatie en voor de login
$lang['install_popup_logo'] = "Please select a logo"; // TODO : translate
$lang['install_logodispo'] = "See the various logos available"; // TODO : translate
$lang['install_welcome'] = "Welcome!";
$lang['install_system_requirements'] = "System Requirements";
$lang['install_database_setup'] = "Database Setup";
$lang['install_create_tables'] = "Table creation";
$lang['install_general_setup'] = "General Setup";
$lang['install_create_config_file'] = "Create Config File";
$lang['install_first_website_setup'] = "Add First Website";
$lang['install_display_javascript_code'] = "Display Javascript code";
$lang['install_finish'] = "Finished!";
$lang['install_txt2'] = "Op het einde van de installatie vragen wij u de offici‰le website te raadplegen, zo hebben wij een idee van het aantal begruikers van phpMyVisites. Alvast bedankt voor u begrip.";
$lang['install_database_setup_txt'] = "Please enter your Database settings.";
$lang['install_general_config_text'] = "phpMyVisites will have only one administrator user who has full access to view/modify everything. Please choose a username and password for your super administrator account. You can add additional users later.";
$lang['install_config_file'] = " Admin user info entered successfully.";
$lang['install_js_code_text'] = "<p>To count all visitors, you must insert the javascript code on all of your pages. </p><p> Your pages do not have to be made with PHP, <strong>phpMyVisites will work on all kinds of pages (whether it is HTML, ASP, Perl or any other languages).</strong> </p><p> Here is the code you have to insert: (copy and paste on all your pages) </p>";
$lang['install_intro'] = "Welcome to the phpMyVisites installation."; 
$lang['install_intro2'] = "This process is split up into %s easy steps and will take around 10 minutes.";
$lang['install_next_step'] = "Go to next step";
$lang['install_status'] = "Installation Status";
$lang['install_done'] = "Installation %s%% complete"; // Install 25% complete
$lang['install_site_success'] = "Website created with success!";
$lang['install_site_info'] = "Please type in all information about the first website.";
$lang['install_go_phpmv'] = "Go to phpMyVisites!";
$lang['install_congratulation'] = "Congratulations! Your phpMyVisites installation is complete.";
$lang['install_end_text'] = "Make sure your javascript code is entered on your pages, and wait for your first visitors!";
$lang['install_db_ok'] = "Connection to database server ok!";
$lang['install_table_exist'] = "phpMyVisites tables already exist in the database.";
$lang['install_table_choice'] = "Either choose to reuse the existing database tables or select a clean install to erase all existing data in the database.";
$lang['install_table_erase'] = "Erase all tables (be careful!)";
$lang['install_table_reuse'] = "Reuse existing tables";
$lang['install_table_success'] = "Tables created with success!";
$lang['install_send_mail'] = "Receive an email each day per website containing statistics summary?";

//
// Update Step
//
$lang['update_title'] = "Update phpMyVisites";
$lang['update_subtitle'] = "We detect that you are updating phpMyVisites.";
$lang['update_versions'] = "Your previous version was %s and we have updated it to %s.";
$lang['update_db_updated'] = "Your database was successfully updated!";
$lang['update_continue'] = "Continue to phpMyVisites";
$lang['update_jschange'] = "Warning! <br /> The phpMyVisites javascript code has been modified. You MUST update your pages and copy/paste the new phpMyVisites Javascript on ALL your configured sites. <br /> The changes made to javascript code are rare, we apologies for the trouble we are taking you through with this change."; // TODO : translate

//
// Dates
//

/*
%daylong% // Monday
%dayshort% // Mon
%daynumeric% // 27
%monthlong% // Febuary
%monthshort% // Feb
%monthnumeric% // 02
%yearlong% // 2004
%yearshort% // 04
*/

// Monday February 10 2004
$lang['tdate1'] = "%daylong% %monthlong% %daynumeric% %yearlong%";

// Monday 10
$lang['tdate2'] = "%daylong% %daynumeric%";

// Week February 10 To February 17 2004
$lang['tdate3'] = "Week %monthlong% %daynumeric% To %monthlong2% %daynumeric2% %yearlong%";

// February 2004 Month
$lang['tdate4'] = "%monthlong% %yearlong% Month";

// December 2003
$lang['tdate5'] = "%monthlong% %yearlong%";

// 10 Febuary week
$lang['tdate6'] = "%daynumeric% %monthlong% week";

// 10-02-2003 // February 2 2003
$lang['tdate7'] = "%daynumeric%-%monthnumeric%-%yearlong%";

// Mon 10 (Only for Graphs purpose)
$lang['tdate8'] = "%dayshort% %daynumeric%";

// Week 10 Feb (Only for Graphs purpose)
$lang['tdate9'] = " Week %daynumeric% %monthshort%";

// Dec 04 (Only for Graphs purpose)
$lang['tdate10'] = "%monthshort% %yearshort%";

// Year 2004
$lang['tdate11'] = "Year %yearlong%";

// 2004
$lang['tdate12'] = "%yearlong%";

// 31
$lang['tdate13'] = "%daynumeric%";

// Months
$lang['moistab']['01'] = "Januari";
$lang['moistab']['02'] = "Februari";
$lang['moistab']['03'] = "Maart";
$lang['moistab']['04'] = "April";
$lang['moistab']['05'] = "Mei";
$lang['moistab']['06'] = "Juni";
$lang['moistab']['07'] = "Juli";
$lang['moistab']['08'] = "Augustus";
$lang['moistab']['09'] = "September";
$lang['moistab']['10'] = "Oktober";
$lang['moistab']['11'] = "November";
$lang['moistab']['12'] = "December";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Jan";
$lang['moistab_graph']['02'] = "Feb";
$lang['moistab_graph']['03'] = "Maart";
$lang['moistab_graph']['04'] = "April";
$lang['moistab_graph']['05'] = "Mei";
$lang['moistab_graph']['06'] = "Juni";
$lang['moistab_graph']['07'] = "Juli";
$lang['moistab_graph']['08'] = "Aug";
$lang['moistab_graph']['09'] = "Sept";
$lang['moistab_graph']['10'] = "Okt";
$lang['moistab_graph']['11'] = "Nov";
$lang['moistab_graph']['12'] = "Dec";

// Day of the week
$lang['jsemaine']['Mon'] = "Maandag";
$lang['jsemaine']['Tue'] = "Dinsdag";
$lang['jsemaine']['Wed'] = "Woensdag";
$lang['jsemaine']['Thu'] = "Donderdag";
$lang['jsemaine']['Fri'] = "Vrijdag";
$lang['jsemaine']['Sat'] = "Zaterdag";
$lang['jsemaine']['Sun'] = "Zondag";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Ma";
$lang['jsemaine_graph']['Tue'] = "Di";
$lang['jsemaine_graph']['Wed'] = "Wo";
$lang['jsemaine_graph']['Thu'] = "Do";
$lang['jsemaine_graph']['Fri'] = "Vr";
$lang['jsemaine_graph']['Sat'] = "Za";
$lang['jsemaine_graph']['Sun'] = "Zo";

// First letter of each day, weekdays ordered
$lang['calendrier_jours'][0] = "M";
$lang['calendrier_jours'][1] = "T";
$lang['calendrier_jours'][2] = "W";
$lang['calendrier_jours'][3] = "T";
$lang['calendrier_jours'][4] = "F";
$lang['calendrier_jours'][5] = "S";
$lang['calendrier_jours'][6] = "S";

// DO NOT ALTER!
$lang['weekdays']['Mon'] = '1';
$lang['weekdays']['Tue'] = '2';
$lang['weekdays']['Wed'] = '3';
$lang['weekdays']['Thu'] = '4';
$lang['weekdays']['Fri'] = '5';
$lang['weekdays']['Sat'] = '6';
$lang['weekdays']['Sun'] = '7';

// Continents
$lang['eur'] = "Europa";
$lang['afr'] = "Afrika";
$lang['asi'] = "Azie";
$lang['ams'] = "Centraal/Zuid Amerika";
$lang['amn'] = "Noord Amerika";
$lang['oce'] = "Oceanie";

// Oceans
$lang['oc_pac'] = "Pacific Ocean"; // TODO : translate
$lang['oc_atl'] = "Atlantic Ocean"; // TODO : translate
$lang['oc_ind'] = "Indian Ocean"; // TODO : translate

// Countries
$lang['domaines'] = array(
    "xx" => "Onbekend",
    "ac" => "Ascension Eilanden",
    "ad" => "Andorra",
    "ae" => "Verenigd Arabische Emiraten",
    "af" => "Afghanistan",
    "ag" => "Antigua en Barbuda",
    "ai" => "Anguilla",
    "al" => "Albanie",
    "am" => "Armenie",
    "an" => "Nederlandse Antillen",
    "ao" => "Angola",
    "aq" => "Antarctica",
    "ar" => "Argentini‰",
    "as" => "Amerikaans Samoa",
    "at" => "Oostenrijk",
    "au" => "Australi‰",
    "aw" => "Aruba",
    "az" => "Azerbaijan",
    "ba" => "Bosni‰ en Herzegowina",
    "bb" => "Barbados",
    "bd" => "Bangladesh",
    "be" => "Belgi‰",
    "bf" => "Burkina Faso",
    "bg" => "Bulgarije",
    "bh" => "Bahrein",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermuda",
    "bn" => "Brunei Darussalam",
    "bo" => "Bolivi‰",
    "br" => "Brazilli‰",
    "bs" => "Bahamas",
    "bt" => "Bhutan",
    "bv" => "Bouvet Eiland",
    "bw" => "Botswana",
    "by" => "Belarus",
    "bz" => "Belize",
    "ca" => "Canada",
    "cc" => "Cocos Eilanden",
    "cd" => "Congo, Democratische Republiek",
    "cf" => "Centraal Afrikaanse Republiek",
    "cg" => "Congo",
    "ch" => "Zwitserland",
    "ci" => "Ivoorkust",
    "ck" => "Cook Eilanden",
    "cl" => "Chili",
    "cm" => "Kameroen",
    "cn" => "China",
    "co" => "Colombi‰",
    "cr" => "Costa Rica",
	"cs" => "Serbia Montenegro",
    "cu" => "Cuba",
    "cv" => "Cape Verde",
    "cx" => "Christmas Eilanden",
    "cy" => "Cyprus",
    "cz" => "Tjechische Republiek",
    "de" => "Duitsland",
    "dj" => "Djibouti",
    "dk" => "Denemarken",
    "dm" => "Dominica",
    "do" => "Dominicaanse Republiek",
    "dz" => "Algerije",
    "ec" => "Equador",
    "ee" => "Estland",
    "eg" => "Egypte",
    "eh" => "West Sahara",
    "er" => "Eritrea",
    "es" => "Spanje",
    "et" => "Ethiopi‰",
    "fi" => "Finland",
    "fj" => "Fiji",
    "fk" => "Falkland Eilanden",
    "fm" => "Micronesia, Federale Staten van",
    "fo" => "Farao Eilanden",
    "fr" => "Frankrijk",
    "ga" => "Gabon",
    "gd" => "Grenada",
    "ge" => "Georgi‰",
    "gf" => "Frans Guiana",
    "gg" => "Guernsey",
    "gh" => "Ghana",
    "gi" => "Gibraltar",
    "gl" => "Groenland",
    "gm" => "Gambia",
    "gn" => "Guinea",
    "gp" => "Guadeloupe",
    "gq" => "Equatoriaal Guinea",
    "gr" => "Griekenland",
    "gs" => "Zuid Georgi‰",
    "gt" => "Guatemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard en McDonald Eilanden",
    "hn" => "Honduras",
    "hr" => "Kroati‰",
    "ht" => "Haiti",
    "hu" => "Hungarije",
    "id" => "Indonesi‰",
    "ie" => "Ierland",
    "il" => "Israel",
    "im" => "Eiland van Man",
    "in" => "India",
    "io" => "Brits-Indische Oceaan Territorium",
    "iq" => "Irak",
    "ir" => "Iran, Islamitische republiek van",
    "is" => "Ijsland",
    "it" => "Itali‰",
    "je" => "Jersey",
    "jm" => "Jamaica",
    "jo" => "Jordani‰",
    "jp" => "Japan",
    "ke" => "Kenya",
    "kg" => "Kyrgystan",
    "kh" => "Cambodia",
    "ki" => "Kiribati",
    "km" => "Comoros",
    "kn" => "St. Kitts en Nevis",
    "kp" => "Korea, Democratische Volksrepubliek",
    "kr" => "Korea, Republiek",
    "kw" => "Koeweit",
    "ky" => "Kaaiman Eilanden",
    "kz" => "Kazachstan",
    "la" => "Laos",
    "lb" => "Libanon",
    "lc" => "Sint Lucia",
    "li" => "Liechtenstein",
    "lk" => "Sri Lanka",
    "lr" => "Liberi‰",
    "ls" => "Lesotho",
    "lt" => "Lithouwen",
    "lu" => "Luxembourg",
    "lv" => "Letland",
    "ly" => "Libi‰",
    "ma" => "Marokko",
    "mc" => "Monaco",
    "md" => "Moldova, Republiek",
    "mg" => "Madagascar",
    "mh" => "Marshall Eilanden",
    "mk" => "Macedoni‰",
    "ml" => "Mali",
    "mm" => "Myanmar",
    "mn" => "Mongoli‰",
    "mo" => "Macau",
    "mp" => "Noordelijke Mariana Eilanden",
    "mq" => "Martinique",
    "mr" => "Mauritani‰",
    "ms" => "Montserrat",
    "mt" => "Malta",
    "mu" => "Mauritius",
    "mv" => "Maldiven",
    "mw" => "Malawi",
    "mx" => "Mexico",
    "my" => "Maleisi‰",
    "mz" => "Mozambique",
    "na" => "Namibi‰",
    "nc" => "Nieuw Caledoni‰",
    "ne" => "Niger",
    "nf" => "Norfolk Eilanden",
    "ng" => "Nigerie",
    "ni" => "Nicaragua",
    "nl" => "Nederland",
    "no" => "Noorwegen",
    "np" => "Nepal",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "Nieuw Zeeland",
    "om" => "Oman",
    "pa" => "Panama",
    "pe" => "Peru",
    "pf" => "Frans Polynesi‰",
    "pg" => "Papua Nieuw Guinea",
    "ph" => "Philipijnen",
    "pk" => "Pakistan",
    "pl" => "Polen",
    "pm" => "St. Pierre en Miquelon",
    "pn" => "Pitcairn",
    "pr" => "Puerto Rico",
    "pt" => "Portugal",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Qatar",
    "re" => "Reunion Eilanden",
    "ro" => "Romeni‰",
    "ru" => "Russische Federatie",
    "rs" => "Rusland",
    "rw" => "Rwanda",
    "sa" => "Saudi Arabi‰",
    "sb" => "Solomon Eilanden",
    "sc" => "Seychellen",
    "sd" => "Soedan",
    "se" => "Zweden",
    "sg" => "Singapore",
    "sh" => "Sint Helena",
    "si" => "Sloveni‰",
    "sj" => "Spitsbergen en Jan Mayen Eilanden",
    "sk" => "Slowakije",
    "sl" => "Sierra Leone",
    "sm" => "San Marino",
    "sn" => "Senegal",
    "so" => "Somali‰",
    "sr" => "Surinami‰",
    "st" => "Sao Tome en Principe",
    "su" => "U.R.S.S.",
    "sv" => "El Salvador",
    "sy" => "Arabische Rebupliek Syri‰",
    "sz" => "Zwitserland",
    "tc" => "De Turkse en Caicos Eilanden",
    "td" => "Chad",
    "tf" => "De Franse Zuidelijke Territoria",
    "tg" => "Togo",
    "th" => "Thailand",
    "tj" => "Tajikistan",
    "tk" => "Tokelau",
    "tm" => "Turkmenistan",
    "tn" => "Tunisi‰",
    "to" => "Tonga",
    "tp" => "Oost Timor",
    "tr" => "Turkije",
    "tt" => "Trinidad en Tobago",
    "tv" => "Tuvalu",
    "tw" => "Taiwan",
    "tz" => "Tanzania, Verenigd Republiek",
    "ua" => "Oekra‹ne",
    "ug" => "Oeganda",
    "uk" => "Verenigd Koninkrijk",
    "gb" => "Groot Britanni‰",
    "um" => "Verenigde Staten (Eilanden)",
    "us" => "Verenigde Staten",
    "uy" => "Uruguay",
    "uz" => "Uzbekistan",
    "va" => "Vaticaanstad",
    "vc" => "St. Vincent en de Grenadinen",
    "ve" => "Venezuela",
    "vg" => "Virgin Eilanden, Brits",
    "vi" => "Virgin Eilanden, U.S.",
    "vn" => "Vietnam",
    "vu" => "Vanuatu",
    "wf" => "Wallis en Futuna Eilanden",
    "ws" => "Samoa",
    "ye" => "Jemen",
    "yt" => "Mayotte",
    "yu" => "Joegoslavi‰",
    "za" => "Zuid Afrika",
    "zm" => "Zambia",
    "zr" => "Zaire",
    "zw" => "Zimbabwe",
    "com" => "-",
    "net" => "-",
    "org" => "-",
    "edu" => "-",
    "int" => "-",
    "arpa" => "-",
    "gov" => "-",
    "mil" => "-",
    "reverse" => "-",
    "biz" => "-",
    "info" => "-",
    "name" => "-",
    "pro" => "-",
    "coop" => "-",
    "aero" => "-",
    "museum" => "-",
    "tv" => "Tuvalu",
    "ws" => "Samoa",
);
?>