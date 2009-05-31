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
$lang['auteur_nom'] = "Manuel Sammeth"; // Translator's name
$lang['auteur_email'] = "brontalus@web.de"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "de"; // iso language code
$lang['lang_libelle_en'] = "German"; // english language name
$lang['lang_libelle_fr'] = "Allemand"; // french language name
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
$lang['separateur_milliers'] = '.'; // three thousand spells 3,000 in english
$lang['separateur_decimaux'] = ','; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | open source Website Statistik und Webverkehr Analyse Programm"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, script, Application, software, Statistik, referals, stats, free, open source, gpl, volumen, Besucher, visits, visitors, mysql, viewed pages, pages, views, number of visits, graphs, Browsers, os, operating system, resolutions, day, week, month, records, country, host, service providors, search enginge, key words, referrers, graphs, entry pages, exit pages, pie charts"; // Header keywords
$lang['head_description'] = "phpMyVisites | Ein Open Source Website Statistikprogramm in PHP/MySQL veröffentlicht unter der Gnu GPL."; // Header description
$lang['logo_description'] = "phpMyVisites : Ein Open Source Website Statistikprogramm in PHP/MySQL veröffentlicht unter der Gnu GPL."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "Besucher";
$lang['menu_pagesvues'] = "Besuchte Seiten";
$lang['menu_suivi'] = "Follow-Up";
$lang['menu_provenance'] = "Ursprung";
$lang['menu_configurations'] = "Konfigurationen der Besucher";
$lang['menu_affluents'] = "Verweise";
$lang['menu_listesites'] = "Seiten Auflisten";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Tag";
$lang['menu_semaine'] = "Woche";
$lang['menu_mois'] = "Monat";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "analysierter Zeitraum: %s"; // Text formatted (e.g.: Studied period: Sunday, July the 14th)
$lang['liens_siteofficiel'] = "Offizielle Website";
$lang['liens_admin'] = "Administration";
$lang['liens_contacts'] = "Kontakt";

//
// Divers
//
$lang['generique_nombre'] = "Nummer";
$lang['generique_tauxsortie'] = "Exit Rate";
$lang['generique_ok'] = "OK";
$lang['generique_timefooter'] = "Seite wurde in %s Sekunden generiert"; // Time in seconds
$lang['generique_divers'] = "Andere"; // (for the graphs)
$lang['generique_inconnu'] = "Unbekannt"; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Andere?"; // Other language, translations wanted
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
$lang['generique_total'] = "Insgesamt";
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
$lang['login_password'] = "passwort: "; // lowercase
$lang['login_login'] = "login"; // lowercase
$lang['login_error'] = "Einloggen nicht möglich: Passwort oder Login nicht gültig.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Kontakte";
$lang['contacts_langue'] = "Übersetzungen";
$lang['contacts_merci'] = "Danke!";
$lang['contacts_auteur'] = "Der Autor, Dokumentator, und Entwickler von phpMyVisites ist <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "Für <strong>technische Fragen, Bugs, Anmerkungen</strong> benutzen Sie bitte die offiziellen Wesite Foren %s. Bei anderen Fragen, nehmen Sie bitte mit dem Autor Kontakt auf über das offizielle Formular auf der Website."; // adresse du site
$lang['contacts_trad1'] = "Willst du phpMyVisites in deine Sprache übersetzen? dann zögere nicht, denn  <strong>phpMyVisites braucht dich!</strong>";
$lang['contacts_trad2'] = "phpMyVisites benötigt ein paar Stunden zum Übersetzen und du benötigst eine gute Kenntnis der Sprache. Aber durch <strong>jede Hilfe profitieren sehr viele Anwender</strong>.  Wenn du Interesse daran hast das Programm zu übersetzen, findest du alle benötigten Informationen in %s der offiziellen Dokumentation von phpMyVisites %s."; // lien vers la doc
$lang['contacts_doc'] = "Zögere nicht %s die offizielle dokumentation von phpMyVisites zu benutzen,%s weil du dort viele nuetzliche und wichtige Infomationen aus allen Bereichen von phpMyVisites finden kannst. Sie ist in Ihrer Version von phpMyVisites verfügbar."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "Bitte besuchen Sie die Danksagungsseite um alle Freunde von phpMyVisites kennen zu lernen.";
$lang['contacts_merci2'] = "Ein herzliches Danke an alle, die sich die Arbeit gemacht haben und phpMyVisites übersetzt haben:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Besucher Informationen";
$lang['visites_statistiques'] = "Statistiken";
$lang['visites_periodesel'] = "In der gewählten Zeit";
$lang['visites_visites'] = "Besuche";
$lang['visites_uniques'] = "Einmalige Besucher";
$lang['visites_pagesvues'] = "besuchte Seiten";
$lang['visites_pagesvisiteurs'] = "Seiten je Besucher";
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Durschnittliche Verweildauer";
$lang['visites_tempsmoyenpv'] = "Durchschnittliche Betrachtungszeit";
$lang['visites_tauxvisite'] = "Besucher die nur 1 Seite  gesehen haben";
$lang['visites_recapperiode'] = "Gesamt-Besuche";
$lang['visites_nbvisites'] = "Besuche";
$lang['visites_aucunevivisite'] = "Nicht besucht"; // in the table, must be short
$lang['visites_recap'] = "Ergebniss";
$lang['visites_unepage'] = "1 Seite"; // (graph)
$lang['visites_pages'] = "%s Seiten"; // 1-2 pages (graph)
$lang['visites_min'] = "%s min"; // 10-15 min (graph)
$lang['visites_sec'] = "%s sek"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "Besucher Diagramm";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Dauer der Besuche";
$lang['visites_graphtempsvisitesimg'] = "Dauer der Besuche je Besucher";
$lang['visites_graphheureserveur'] = "Aufrufe je Stunde";
$lang['visites_graphheureserveurimg'] = "Besuche in der Serverzeit";
$lang['visites_graphheurevisiteur'] = "Aufrufe je Besucher und Stunde";
$lang['visites_graphheurelocalimg'] = "Besuche nach Ortszeit";
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
$lang['pagesvues_titre'] = "Besuchte seiten info";
$lang['pagesvues_joursel'] = "ausgewählter Tag:";
$lang['pagesvues_jmoins7'] = "Tag - 7";
$lang['pagesvues_jmoins14'] = "Tag - 14";
$lang['pagesvues_moyenne'] = "(durchschnittlich)";
$lang['pagesvues_pagesvues'] = "Seitenaufrufe";
$lang['pagesvues_pagesvudiff'] = "einmalige Seitenaufrufe";
$lang['pagesvues_recordpages'] = "Höchste Anzahl von Aufrufen von einem Benutzer";
$lang['pagesvues_tabdetails'] = "Seitenaufrufe (von %s bis %s)"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "Diagramm für die Aufrufe je Seite";
$lang['pagesvues_graphnbvisitespageimg'] = "Besuche nach Anzahl der Seite";
$lang['pagesvues_graphheureserveur'] = "Diagramm für die Anzeige von Besuchen nach Serverzeit";
$lang['pagesvues_graphheureserveurimg'] = "Besuche nach Serverzeit";
$lang['pagesvues_graphheurevisiteur'] = "Diagramm für die Besuche nach Ortzeit";
$lang['pagesvues_graphpageslocalimg'] = "Besuche nach Ortszeit";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Navigation der Besucher";
$lang['suivi_pageentree'] = "kommend von";
$lang['suivi_pagesortie'] = "gehend nach";
$lang['suivi_tauxsortie'] = "Exit Rate";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Besucher Herkunft";
$lang['provenance_recappays'] = "Länderzusammenfassung";
$lang['provenance_pays'] = "Länder";
$lang['provenance_paysimg'] = "Besuchertabelle je Land";
$lang['provenance_fai'] = "Internet Service Providers";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Providers"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Kontinent";
$lang['provenance_mappemonde'] = "Weltkarte";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Besuchereinstellungen";
$lang['configurations_os'] = "Betriebssysteme";
$lang['configurations_osimg'] = "Diagramm für die benutzten Betriebssysteme";
$lang['configurations_navigateurs'] = "Browser";
$lang['configurations_navigateursimg'] = "Diagram für die benutzten Browser";
$lang['configurations_resolutions'] = "Bildschirmauflösung";
$lang['configurations_resolutionsimg'] = "Diagramm für die Bildschirmauflösung";
$lang['configurations_couleurs'] = "Farbtiefe";
$lang['configurations_couleursimg'] = "Diagramm für die Farbtiefe";
$lang['configurations_rapport'] = "Normal/widescreen";
$lang['configurations_large'] = "Widescreen";
$lang['configurations_normal'] = "Normal";
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
$lang['affluents_titre'] = "Verlinkung";
$lang['affluents_recapimg'] = "Besucheranzeige von Links";
$lang['affluents_directimg'] = "Direkt";
$lang['affluents_sitesimg'] = "Websites";
$lang['affluents_moteursimg'] = "Engines";
$lang['affluents_referrersimg'] = "Links";
$lang['affluents_moteurs'] = "Suchmaschinen";
$lang['affluents_nbparmoteur'] = "Besucher von Suchmaschinen: %s";
$lang['affluents_aucunmoteur'] = "Es wurden keine Besucher von Suchmaschinen weitergeleitet";
$lang['affluents_motscles'] = "Suchworte";
$lang['affluents_nbmotscles'] = "benutzte Suchworte : %s";
$lang['affluents_aucunmotscles'] = "Es wurden keine Suchworte gefunden.";
$lang['affluents_sitesinternet'] = "Websites";
$lang['affluents_nbautressites'] = "Besucher von anderen Websites : %s";
$lang['affluents_nbautressitesdiff'] = "Anzahl der Websites : %s";
$lang['affluents_aucunautresite'] = "Es kamen keine Besucher von anderen Websites.";
$lang['affluents_entreedirecte'] = "Direkte Anfragen";
$lang['affluents_nbentreedirecte'] = "Direkte Besucher : %s";
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
$lang['purge_titre'] = "Zusammenfassung der Besuche und Verlinkungen";
$lang['purge_intro'] = "Der Zeitraum wurde vom Administrator gelöscht, nur die notwendigen Statistiken wurden gespeichert.";
$lang['admin_purge'] = "Datenbankpflege";
$lang['admin_purgeintro'] = "Hier können Sie die Tabellen verwalten die von phpMyVisites benutzt werden. Hier können Sie den benötigten Speicheroplatz der Tabellen sehen, sie optimieren, oder alte Einträge löschen. Hier können Sie den benutzten Speicherplatz festlegen.";
$lang['admin_optimisation'] = "Optimierung von [ %s ]..."; // Tables names
$lang['admin_postopt'] = "die Gesamtgröße reduziert um %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "Folgende Zeiträume löschen: %s";
$lang['admin_purge_fini'] = "Löschen beendet...";
$lang['admin_bdd_nom'] = "Name";
$lang['admin_bdd_enregistrements'] = "Einträge";
$lang['admin_bdd_taille'] = "Tabellengröße";
$lang['admin_bdd_opt'] = "Optimieren";
$lang['admin_bdd_purge'] = "Bereinigungs Kriterien";
$lang['admin_bdd_optall'] = "Alle optimieren";
$lang['admin_purge_j'] = "Einträge entfernen älter als %s Tage";
$lang['admin_purge_s'] = "Einträge entfernen älter als %s Wochen";
$lang['admin_purge_m'] = "Einträge entfernen älter als %s Monate";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Alle Logs entfernen";
$lang['admin_purge_autres'] = "Tabelle bereinigen '%s'";
$lang['admin_purge_none'] = "keine Änderungen möglich";
$lang['admin_purge_cal'] = "Kalkulieren und Bereinigen (dies kann einige Minuten dauern)";
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
$lang['admin_intro'] = "Willkommen in der phpMyVisites Konfigurationsoberfläche. Hier können sie alles anpassen, was mit Ihrer Installation zusammenhängt. Wenn Sie Probleme haben benutzen Sie bitte die %s offizielle Dokumentation von phpMyVisites %s."; // link to the doc
$lang['admin_configetperso'] = "Allgemeine Einstellungen";
$lang['admin_afficherjavascript'] = "JavaScript Statistik-code anzeigen";
$lang['admin_cookieadmin'] = "Administrator in den Statistiken ignorieren";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Überwachte Seiten:";
$lang['admin_retour'] = "Zurück";
$lang['admin_cookienavigateur'] = "Sie können den Administrator aus der Statistik ausschließen. Dies basiert auf Cookies und wird nur mit demselben Browser weiter funktionieren. Diese Option kann jederzeit geändert werden.";
$lang['admin_prendreencompteadmin'] = "Den Administrator in die Statistik mit einbeziehen (Cookie löschen)";
$lang['admin_nepasprendreencompteadmin'] = "Den Administrator aus der Statistik ausschließen (Cookie anlegen)";
$lang['admin_etatcookieoui'] = "Der Administrator wird in der Statistik mit gewertet (dies ist die Standardeinstellung, Sie werden als normaler Besucher angesehen)";
$lang['admin_etatcookienon'] = "Sie werden in die Statistik nicht mit einbezogen (Ihre Besuche werden für diese Website nicht ausgewertet)";
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
$lang['install_loginmysql'] = "Datenbank Login";
$lang['install_mdpmysql'] = "Datenbank Passwort";
$lang['install_serveurmysql'] = "Datenbank Server";
$lang['install_basemysql'] = "Datenbank Name";
$lang['install_prefixetable'] = "Tabelle";
$lang['install_utilisateursavances'] = "Fortgeschrittene Benutzer (optional)";
$lang['install_oui'] = "Ja";
$lang['install_non'] = "Nein";
$lang['install_ok'] = "OK";
$lang['install_probleme'] = "Problem: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Administrator Login:";
$lang['install_mdpadmin'] = "Administrator Passwort:";
$lang['install_chemincomplet'] = "Bitte geben Sie den Pfad zu phpMyVisites an. (z.B. http://www.mysite.com/rep1/rep3/phpmyvisites/). Der Pfad muss mit einem <strong>/</strong> beendet werden.";
$lang['install_afficherlogo'] = "Logo auf Ihren Seiten anzeigen? %s <br />By allowing the display of the logo on your site, you will help publicize phpMyVisites and help it evolve more rapidly.  It is also a way to thank the author who has spent many hours developing this Open Source, free application."; // %s replaced by the logo image
$lang['install_affichergraphique'] = "Diagramme anzeigen.";
$lang['install_valider'] = "Bestätigen"; //  during installation and for login
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
$lang['install_txt2'] = "Am Ende der Installation werden Sie an die offizielle Seite von phpMyVisites verwiesen um die Zahl der Installationen nachverfolgen zu können. Danke für Ihr Verständniss.";
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
$lang['moistab']['01'] = "Januar";
$lang['moistab']['02'] = "Februar";
$lang['moistab']['03'] = "März";
$lang['moistab']['04'] = "April";
$lang['moistab']['05'] = "Mai";
$lang['moistab']['06'] = "Juni";
$lang['moistab']['07'] = "Juli";
$lang['moistab']['08'] = "August";
$lang['moistab']['09'] = "September";
$lang['moistab']['10'] = "Oktober";
$lang['moistab']['11'] = "November";
$lang['moistab']['12'] = "Dezember";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Jan";
$lang['moistab_graph']['02'] = "Feb";
$lang['moistab_graph']['03'] = "Mär";
$lang['moistab_graph']['04'] = "Apr";
$lang['moistab_graph']['05'] = "Mai";
$lang['moistab_graph']['06'] = "Jun";
$lang['moistab_graph']['07'] = "Jul";
$lang['moistab_graph']['08'] = "Aug";
$lang['moistab_graph']['09'] = "Sep";
$lang['moistab_graph']['10'] = "Okt";
$lang['moistab_graph']['11'] = "Nov";
$lang['moistab_graph']['12'] = "Dez";

// Day of the week
$lang['jsemaine']['Mon'] = "Montag";
$lang['jsemaine']['Tue'] = "Dienstag";
$lang['jsemaine']['Wed'] = "Mittwoch";
$lang['jsemaine']['Thu'] = "Donnerstag";
$lang['jsemaine']['Fri'] = "Freitag";
$lang['jsemaine']['Sat'] = "Samstag";
$lang['jsemaine']['Sun'] = "Sonntag";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Mon";
$lang['jsemaine_graph']['Tue'] = "Die";
$lang['jsemaine_graph']['Wed'] = "Mit";
$lang['jsemaine_graph']['Thu'] = "Don";
$lang['jsemaine_graph']['Fri'] = "Fre";
$lang['jsemaine_graph']['Sat'] = "Sam";
$lang['jsemaine_graph']['Sun'] = "Son";

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
$lang['asi'] = "Asien";
$lang['ams'] = "Süd und Zentral Amerika";
$lang['amn'] = "Nord Amerika";
$lang['oce'] = "Ozeanien";

// Oceans
$lang['oc_pac'] = "Pacific Ocean"; // TODO : translate
$lang['oc_atl'] = "Atlantic Ocean"; // TODO : translate
$lang['oc_ind'] = "Indian Ocean"; // TODO : translate

// Countries
$lang['domaines'] = array(
    "xx" => "Unknown",
    "ac" => "Ascension Islands",
    "ad" => "Andorra",
    "ae" => "United Arab Emirates",
    "af" => "Afghanistan",
    "ag" => "Antigua and Barbuda",
    "ai" => "Anguilla",
    "al" => "Albania",
    "am" => "Armenia",
    "an" => "Netherlands Antilles",
    "ao" => "Angola",
    "aq" => "Antarctica",
    "ar" => "Argentina",
    "as" => "American Samoa",
    "at" => "Austria",
    "au" => "Australia",
    "aw" => "Aruba",
    "az" => "Azerbaijan",
    "ba" => "Bosnia and Herzegovina",
    "bb" => "Barbados",
    "bd" => "Bangladesh",
    "be" => "Belgium",
    "bf" => "Burkina Faso",
    "bg" => "Bulgaria",
    "bh" => "Bahrain",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermuda",
    "bn" => "Bruneo",
    "bo" => "Bolivia",
    "br" => "Brazil",
    "bs" => "Bahamas",
    "bt" => "Bhutan",
    "bv" => "Bouvet Island",
    "bw" => "Botswana",
    "by" => "Belarus",
    "bz" => "Belize",
    "ca" => "Canada",
    "cc" => "Cocos (Keeling) Islands",
    "cd" => "Congo, The Democratic Republic of the",
    "cf" => "Central African Republic",
    "cg" => "Congo",
    "ch" => "Switzerland",
    "ci" => "Cote D'Ivoire",
    "ck" => "Cook Islands",
    "cl" => "Chile",
    "cm" => "Cameroon",
    "cn" => "China",
    "co" => "Colombia",
    "cr" => "Costa Rica",
	"cs" => "Serbia Montenegro",
    "cu" => "Cuba",
    "cv" => "Cape Verde",
    "cx" => "Christmas Island",
    "cy" => "Cyprus",
    "cz" => "Czech Republic",
    "de" => "Germany",
    "dj" => "Djibouti",
    "dk" => "Denmark",
    "dm" => "Dominica",
    "do" => "Dominican Republic",
    "dz" => "Algeria",
    "ec" => "Ecuador",
    "ee" => "Estonia",
    "eg" => "Egypt",
    "eh" => "Western Sahara",
    "er" => "Eritrea",
    "es" => "Spain",
    "et" => "Ethiopia",
    "fi" => "Finland",
    "fj" => "Fiji",
    "fk" => "Falkland Islands (Malvinas)",
    "fm" => "Micronesia, Federated States of",
    "fo" => "Faroe Islands",
    "fr" => "France",
    "ga" => "Gabon",
    "gd" => "Grenada",
    "ge" => "Georgia",
    "gf" => "French Guyana",
    "gg" => "Guernsey",
    "gh" => "Ghana",
    "gi" => "Gibraltar",
    "gl" => "Greenland",
    "gm" => "Gambia",
    "gn" => "Guinea",
    "gp" => "Guadeloupe",
    "gq" => "Equatorial Guinea",
    "gr" => "Greece",
    "gs" => "South Georgia and the South Sandwich Islands",
    "gt" => "Guatemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard Island and McDonald Islands",
    "hn" => "Honduras",
    "hr" => "Croatia",
    "ht" => "Haiti",
    "hu" => "Hungary",
    "id" => "Indonesia",
    "ie" => "Ireland",
    "il" => "Israel",
    "im" => "Man Island",
    "in" => "India",
    "io" => "British Indian Ocean Territory",
    "iq" => "Iraq",
    "ir" => "Iran, Islamic Republic of",
    "is" => "Iceland",
    "it" => "Italy",
    "je" => "Jersey",
    "jm" => "Jamaica",
    "jo" => "Jordan",
    "jp" => "Japan",
    "ke" => "Kenya",
    "kg" => "Kyrgyzstan",
    "kh" => "Cambodia",
    "ki" => "Kiribati",
    "km" => "Comoros",
    "kn" => "Saint Kitts and Nevis",
    "kp" => "Korea, Democratic People's Republic of",
    "kr" => "Korea, Republic of",
    "kw" => "Kuwait",
    "ky" => "Cayman Islands",
    "kz" => "Kazakhstan",
    "la" => "Laos",
    "lb" => "Lebanon",
    "lc" => "Saint Lucia",
    "li" => "Liechtenstein",
    "lk" => "Sri Lanka",
    "lr" => "Liberia",
    "ls" => "Lesotho",
    "lt" => "Lithuania",
    "lu" => "Luxembourg",
    "lv" => "Latvia",
    "ly" => "Libya",
    "ma" => "Morocco",
    "mc" => "Monaco",
    "md" => "Moldova, Republic of",
    "mg" => "Madagascar",
    "mh" => "Marshall Islands",
    "mk" => "Macedonia",
    "ml" => "Mali",
    "mm" => "Myanmar",
    "mn" => "Mongolia",
    "mo" => "Macau",
    "mp" => "Northern Mariana Islands",
    "mq" => "Martinique",
    "mr" => "Mauritania",
    "ms" => "Montserrat",
    "mt" => "Malta",
    "mu" => "Mauritius",
    "mv" => "Maldives",
    "mw" => "Malawi",
    "mx" => "Mexico",
    "my" => "Malaysia",
    "mz" => "Mozambique",
    "na" => "Namibia",
    "nc" => "New Caledonia",
    "ne" => "Niger",
    "nf" => "Norfolk Island",
    "ng" => "Nigeria",
    "ni" => "Nicaragua",
    "nl" => "Netherlands",
    "no" => "Norway",
    "np" => "Nepal",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "New Zealand",
    "om" => "Oman",
    "pa" => "Panama",
    "pe" => "Peru",
    "pf" => "French Polynesia",
    "pg" => "Papua New Guinea",
    "ph" => "Philippines",
    "pk" => "Pakistan",
    "pl" => "Poland",
    "pm" => "Saint Pierre and Miquelon",
    "pn" => "Pitcairn",
    "pr" => "Puerto Rico",
    "pt" => "Portugal",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Qatar",
    "re" => "Reunion Island",
    "ro" => "Romania",
    "ru" => "Russian Federation",
    "rs" => "Russia",
    "rw" => "Rwanda",
    "sa" => "Saudi Arabia",
    "sb" => "Solomon Islands",
    "sc" => "Seychelles",
    "sd" => "Sudan",
    "se" => "Sweden",
    "sg" => "Singapore",
    "sh" => "Saint Helena",
    "si" => "Slovenia",
    "sj" => "Svalbard",
    "sk" => "Slovakia",
    "sl" => "Sierra Leone",
    "sm" => "San Marino",
    "sn" => "Senegal",
    "so" => "Somalia",
    "sr" => "Suriname",
    "st" => "Sao Tome and Principe",
    "su" => "Old U.R.S.S.",
    "sv" => "El Salvador",
    "sy" => "Syrian Arab Republic",
    "sz" => "Switzerland",
    "tc" => "Turks and Caicos Islands",
    "td" => "Chad",
    "tf" => "French Southern Territories",
    "tg" => "Togo",
    "th" => "Thailand",
    "tj" => "Tajikistan",
    "tk" => "Tokelau",
    "tm" => "Turkmenistan",
    "tn" => "Tunisia",
    "to" => "Tonga",
    "tp" => "East Timor",
    "tr" => "Turkey",
    "tt" => "Trinidad and Tobago",
    "tv" => "Tuvalu",
    "tw" => "Taiwan, Province of China",
    "tz" => "Tanzania, United Republic of",
    "ua" => "Ukraine",
    "ug" => "Uganda",
    "uk" => "United Kingdom",
    "gb" => "Great Britain",
    "um" => "United States Minor Outlying Islands",
    "us" => "United States",
    "uy" => "Uruguay",
    "uz" => "Uzbekistan",
    "va" => "Vatican City",
    "vc" => "Saint Vincent and the Grenadines",
    "ve" => "Venezuela",
    "vg" => "Virgin Islands, British",
    "vi" => "Virgin Islands, U.S.",
    "vn" => "Vietnam",
    "vu" => "Vanuatu",
    "wf" => "Wallis and Futuna",
    "ws" => "Samoa",
    "ye" => "Yemen",
    "yt" => "Mayotte",
    "yu" => "Yugoslavia",
    "za" => "South Africa",
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