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
$lang['auteur_nom'] = "Liuta Romulus Ovidiu"; // Translator's name
$lang['auteur_email'] = "admin@devplug.com"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "ro"; // iso language code
$lang['lang_libelle_en'] = "Romanian"; // english language name
$lang['lang_libelle_fr'] = "Roumain"; // french language name
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
$lang['separateur_milliers'] = ''; // three thousand spells 3,000 in english
$lang['separateur_decimaux'] = '.'; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | aplicatie open source pentru statistici web si analizator de traffic"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, script, aplicatie, software, statistici, referals, stats, gratis, open source, gpl, visite, vizitatori, mysql, pagini accesate, pagini, accesari, numarul de visite, grafice, Browsers, sisteme de operare, so, rezolutii, zi, saptamana, luna, inregistrari, tara, gazda, service providers, motor de cautare, cuvinte cheie, referrers, grafice, pagini de intrare, pagini de iesire, grafice complexe"; // Header keywords
$lang['head_description'] = "phpMyVisites | O aplicatie open source pentru statistici web dezvoltata in PHP/MySQL si distribuita sub licenta Gnu GPL."; // Header description
$lang['logo_description'] = "phpMyVisites : O aplicatie open source pentru statistici web dezvoltata in PHP/MySQL si distribuita sub licenta Gnu GPL."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "Accesari";
$lang['menu_pagesvues'] = "Pagini Accesate";
$lang['menu_suivi'] = "Accesari";
$lang['menu_provenance'] = "Sursa";
$lang['menu_configurations'] = "Setari";
$lang['menu_affluents'] = "Referinte";
$lang['menu_listesites'] = "Site-uri Listate";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Zi";
$lang['menu_semaine'] = "Saptamana";
$lang['menu_mois'] = "Luna";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Perioada Studiata: %s"; // Text formatted (e.g.: Studied period: Sunday, July the 14th)
$lang['liens_siteofficiel'] = "Site-ul Oficial";
$lang['liens_admin'] = "Administrator";
$lang['liens_contacts'] = "Contacte";

//
// Divers
//
$lang['generique_nombre'] = "Numar";
$lang['generique_tauxsortie'] = "Rata de Iesire";
$lang['generique_ok'] = "OK";
$lang['generique_timefooter'] = "Pagina generata in %s secunde"; // Time in seconds
$lang['generique_divers'] = "Altele"; // (for the graphs)
$lang['generique_inconnu'] = "Necunoscut"; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Altele?"; // Other language, translations wanted
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
$lang['generique_total'] = "Total";
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
$lang['login_password'] = "parola : "; // lowercase
$lang['login_login'] = "login : "; // lowercase
$lang['login_error'] = "Nu putem realiza accesul. Login sau parola incorecta.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Contacte";
$lang['contacts_langue'] = "Traduceri";
$lang['contacts_merci'] = "Multumiri";
$lang['contacts_auteur'] = "Autorul, cercetatorul, si creatorul proiectului phpMyVisites este <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "Pentru <strong>intrebari tehnice, probleme sau sugestii</strong> va rugam folositi forumurile site-ului oficial %s. Pentru alte cerinte, va rugam contactati autorul prin formularul de pe site-ul oficial."; // adresse du site
$lang['contacts_trad1'] = "Vrei sa traduci phpMyVisites in limba ta? Nu ezita pentru ca <strong>phpMyVisites are nevoie de TINE!</strong>";
$lang['contacts_trad2'] = "Traducerea phpMyVisites va lua ceva timp (cateva ore) si implica o buna cunoastere a limbajului in cauza; dar tine minte ca <strong>din munca ta vor beneficia multi utilizatori</strong>.  Daca esti interesat in traducere phpMyVisites poti sa gasesti toate informatiile necesare in %s documentatia oficial a phpMyVisites %s."; // lien vers la doc
$lang['contacts_doc'] = "Nu ezita sa consulti %s documentatia oficiala a phpMyVisites %s care iti va oferi informatia necesara instalarii, configurarii, si functionalitatii phpMyVisites. Documentatia phpMyVisites este prezenta pentru toate versiunile."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "Nu ezita sa consulti pagina prietenilor phpMyVisites de pe site-ul oficial.";
$lang['contacts_merci2'] = "Un mare MULTUMESC pentru toti cei care au contribuit prin cultura limbajului lor la traducerea phpMyVisites:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Informatiile Vizitatorilor";
$lang['visites_statistiques'] = "Statistici";
$lang['visites_periodesel'] = "Pentru perioada selectata";
$lang['visites_visites'] = "Accesari";
$lang['visites_uniques'] = "Accesari Unice";
$lang['visites_pagesvues'] = "Pagini Accesate";
$lang['visites_pagesvisiteurs'] = "Pagini pe vizitator";
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Durata medie de vizita";
$lang['visites_tempsmoyenpv'] = "Timpul mediu petrecut pe pagina";
$lang['visites_tauxvisite'] = "rata de visitare a primii pagini";
$lang['visites_recapperiode'] = "Sumarul Perioadei";
$lang['visites_nbvisites'] = "Accesari";
$lang['visites_aucunevivisite'] = "Nici o visita"; // in the table, must be short
$lang['visites_recap'] = "Sumar";
$lang['visites_unepage'] = "prima pagina"; // (graph)
$lang['visites_pages'] = "%s pagini"; // 1-2 pages (graph)
$lang['visites_min'] = "%s min"; // 10-15 min (graph)
$lang['visites_sec'] = "%s s"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "Grafic pentru sumarul statisticilor";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Grafic pentru durata vizitei fiecarui vizitator";
$lang['visites_graphtempsvisitesimg'] = "Durata vizitei fiecarui vizitator";
$lang['visites_graphheureserveur'] = "Grafic pentru numarul de accesari pe ora pentru server";
$lang['visites_graphheureserveurimg'] = "Vizite dupa timpul serverului";
$lang['visites_graphheurevisiteur'] = "Grafic pentru numarul de accesari pe ora pentru vizitator";
$lang['visites_graphheurelocalimg'] = "Accesari dupa timpul local";
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
$lang['pagesvues_titre'] = "Informatie accesari Pagini";
$lang['pagesvues_joursel'] = "Ziua Selectata";
$lang['pagesvues_jmoins7'] = "Ziua - 7";
$lang['pagesvues_jmoins14'] = "Ziua - 14";
$lang['pagesvues_moyenne'] = "(media)";
$lang['pagesvues_pagesvues'] = "Accesari Pagini";
$lang['pagesvues_pagesvudiff'] = "Accesari unice pe Pagini";
$lang['pagesvues_recordpages'] = "Cel mai mare numar de pagini accesate pe vizitator";
$lang['pagesvues_tabdetails'] = "Pagini accesate (de la %s la %s)"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "Grafic pentru a arata numarul de vizite pe pagini accesate";
$lang['pagesvues_graphnbvisitespageimg'] = "Numarul de vizite pe pagini accesate";
$lang['pagesvues_graphheureserveur'] = "Grafic pentru a arata numarul de vizite in timpul serverului";
$lang['pagesvues_graphheureserveurimg'] = "Vizite in timpul serverului";
$lang['pagesvues_graphheurevisiteur'] = "Grafic pentru a arata numarul de vizite in timpul local";
$lang['pagesvues_graphpageslocalimg'] = "Vizite in timpul local";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Accesarile Vizitatorilor";
$lang['suivi_pageentree'] = "Pagini de intrare";
$lang['suivi_pagesortie'] = "Pagini de iesire";
$lang['suivi_tauxsortie'] = "Rata de iesire";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Originea Vizitatorilor";
$lang['provenance_recappays'] = "Sumarul Tarilor";
$lang['provenance_pays'] = "Tari";
$lang['provenance_paysimg'] = "Harta Vizitatorilor pe tari";
$lang['provenance_fai'] = "Provideri de Internet";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Provideri"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Continent";
$lang['provenance_mappemonde'] = "Harta Pamantului";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Setarile Vizitatorilor";
$lang['configurations_os'] = "Sistemul de Operare";
$lang['configurations_osimg'] = "Grafic pentru a arata sistemul de operare al vizitatorilor";
$lang['configurations_navigateurs'] = "Browsere";
$lang['configurations_navigateursimg'] = "Grafic pentru a arata browsere-le vizitatorilor";
$lang['configurations_resolutions'] = "Rezolutia ecranului";
$lang['configurations_resolutionsimg'] = "Grafic pentru a arata rezolutiile ecranelor";
$lang['configurations_couleurs'] = "Formatul Culorilor";
$lang['configurations_couleursimg'] = "Grafic pentru a arata formatul culorilor";
$lang['configurations_rapport'] = "Normal/Ecran alungit";
$lang['configurations_large'] = "Ecran Alungit";
$lang['configurations_normal'] = "Normal";
$lang['configurations_double'] = "Dual Screen";
$lang['configurations_plugins'] = "Plugin-uri";
$lang['configurations_navigateursbytype'] = "Browsere (dupa tip)";
$lang['configurations_navigateursbytypeimg'] = "Grafic pentru a arata tipul browsere-lor";
$lang['configurations_os_interest'] = "Operating Systems Interest";
$lang['configurations_navigateurs_interest'] = "Browsers Interest";
$lang['configurations_resolutions_interest'] = "Screen Resolutions Interest";
$lang['configurations_couleurs_interest'] = "Color Depth Interest";
$lang['configurations_configurations'] = "Top settings";

//
// Referers
//
$lang['affluents_titre'] = "Referals";
$lang['affluents_recapimg'] = "Harta vizitatorilor dupa referals";
$lang['affluents_directimg'] = "Direct";
$lang['affluents_sitesimg'] = "Website-uri";
$lang['affluents_moteursimg'] = "Motoare de Cautare";
$lang['affluents_referrersimg'] = "Referinte";
$lang['affluents_moteurs'] = "Motoare de cautare";
$lang['affluents_nbparmoteur'] = "Vizite provenite din motoare de cautare : %s";
$lang['affluents_aucunmoteur'] = "Nici o vizita provenita dintr-un motor de cautare.";
$lang['affluents_motscles'] = "Cuvinte Cheie";
$lang['affluents_nbmotscles'] = "Cuvinte distincte : %s";
$lang['affluents_aucunmotscles'] = "Nici un cuvant cheie gasit.";
$lang['affluents_sitesinternet'] = "Websites";
$lang['affluents_nbautressites'] = "Visite provenite de la alte website-uri : %s";
$lang['affluents_nbautressitesdiff'] = "Numarul de site-uri : %s";
$lang['affluents_aucunautresite'] = "Nici o vizita provenita din alte site-uri.";
$lang['affluents_entreedirecte'] = "Accesari Directe";
$lang['affluents_nbentreedirecte'] = "Vizite pentru accesari directe : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Detalii"; // In the results of the referers array
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
$lang['purge_titre'] = "Sumarul vizitelor si referintelor";
$lang['purge_intro'] = "Acesta perioada a fost stearsa de administrator, numai statisticile esentiale sunt tinute.";
$lang['admin_purge'] = "Intretinere Baza de Date";
$lang['admin_purgeintro'] = "Aceasta sectiune va permite modificarea tabelelor utilizate de phpMyVisites. Puteti vizualiza spatiul pe disk folosit de tabele, le puteti optimiza, sau sterge inregistrarile vechi. Acesta  va permite sa limitati spatiul de stocare al tabelelor in folosinta.";
$lang['admin_optimisation'] = "Optimizarea [ %s ]..."; // Tables names
$lang['admin_postopt'] = "Marimea totala s-a micsorat cu %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "Sterge urmatoarele perioade: %s";
$lang['admin_purge_fini'] = "Am terminat stergerea tabelelor...";
$lang['admin_bdd_nom'] = "Numele";
$lang['admin_bdd_enregistrements'] = "Inregistrari";
$lang['admin_bdd_taille'] = "Marimea Tabelei";
$lang['admin_bdd_opt'] = "Optimizeaza";
$lang['admin_bdd_purge'] = "Criteriul de Stergere";
$lang['admin_bdd_optall'] = "Optimizare Totala";
$lang['admin_purge_j'] = "Sterge inregistrarile mai vechi de %s zile";
$lang['admin_purge_s'] = "Sterge inregistrarile mai vechi de %s saptamani";
$lang['admin_purge_m'] = "Sterge inregistrarile mai vechi de %s luni";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Sterge toate accesarile inregistrate";
$lang['admin_purge_autres'] = "Stergere normala pentru tabela '%s'";
$lang['admin_purge_none'] = "Nici o modificare posibile";
$lang['admin_purge_cal'] = "Calculeaza si sterge (poate dura cateva minute)";
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
$lang['admin_intro'] = "Bun venit in zona de configurare a phpMyVisites . Puteti modifica toata informatia in legatura cu instalarea. Daca aveti vreo problema nu ezitati sa contactati %s documentatia oficiala a phpMyVisites %s."; // link to the doc
$lang['admin_configetperso'] = "Setari Generale";
$lang['admin_afficherjavascript'] = "Arata codul JavaScript";
$lang['admin_cookieadmin'] = "Nu adauga administratorul in statistici";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Site-uri inregistrate:";
$lang['admin_retour'] = "Inapoi";
$lang['admin_cookienavigateur'] = "Puteti exclude Administratorul din statistici. Acesta metoda este bazata pe cookie-uri si aceasta optiune este valabila doar in browser-ul curent. Puteti schimba aceata optiune oricand.";
$lang['admin_prendreencompteadmin'] = "Adauga administratorul printre statistici (sterge cookie)";
$lang['admin_nepasprendreencompteadmin'] = "Nu adauga administratorul pentri statistici (creaza cookie)";
$lang['admin_etatcookieoui'] = "Administratorul este calculat pintre statisticile acestui site (Aceasta este configuratia normala si sunteti considerat un vizitator normal)";
$lang['admin_etatcookienon'] = "Nu sunteti calculat in statisticile acestui site(Vizitele dumneavoastra nu vor fi calculate in aceste statistici)";
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
$lang['install_loginmysql'] = "Utilizatorul bazei de date";
$lang['install_mdpmysql'] = "Parola bazei de date";
$lang['install_serveurmysql'] = "Severul bazei de date";
$lang['install_basemysql'] = "Numele bazei de date";
$lang['install_prefixetable'] = "Prefixul tabelelor";
$lang['install_utilisateursavances'] = "Useri avansati(optional)";
$lang['install_oui'] = "Yes";
$lang['install_non'] = "No";
$lang['install_ok'] = "OK";
$lang['install_probleme'] = "Problema: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Administrator nume utilizator :";
$lang['install_mdpadmin'] = "Administrator parola:";
$lang['install_chemincomplet'] = "Locatia completa pentru aplicatia phpMyVisites (exemplu: http://www.mysite.com/rep1/rep3/phpmyvisites/). Locatia trebuie sa se termine cu un <strong>/</strong>.";
$lang['install_afficherlogo'] = "Arata logo-ul pe toate paginile? %s <br />Permitandu-ne sa aratam logo-ul pe toate paginile, veti ajuta la popularizarea phpMyVisites si sa-l ajutati sa se dezvolte rapid.  Este de asemenea un mod de a multumi autorului care a petrecut multe ore in dezvoltarea acestei aplicatii gratis, Open Source."; // %s replaced by the logo image
$lang['install_affichergraphique'] = "Arata Graficele Statisticilor.";
$lang['install_valider'] = "Submit"; //  during installation and for login
$lang['install_popup_logo'] = "Va rugam selectati un Logo";
$lang['install_logodispo'] = "Vizionati diferitele logo-uri disponibile";
$lang['install_welcome'] = "Welcome!";
$lang['install_system_requirements'] = "System Requirements";
$lang['install_database_setup'] = "Database Setup";
$lang['install_create_tables'] = "Table creation";
$lang['install_general_setup'] = "General Setup";
$lang['install_create_config_file'] = "Create Config File";
$lang['install_first_website_setup'] = "Add First Website";
$lang['install_display_javascript_code'] = "Display Javascript code";
$lang['install_finish'] = "Finished!";
$lang['install_txt2'] = "La sfarsitul instalarii, o interogare va fi facuta catre site-ul oficial pentru a tine cont de numarul de utilizatori ai sistemului phpMyVisites. Multumim pentru intelegere.";
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
$lang['update_jschange'] = "Alerta! <br /> Codul javascript al phpMyVisites a fost modificat. Trebuie sa va copiati noul cod curent si sa-l  copiati pe toate site-urile in folosinta. <br /> Modificarile facute in codul Javascript sunt rare, ne cerem scuze pentru deranjul creat de aceasta modificare vitala.";

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
$lang['moistab']['01'] = "Ianuarie";
$lang['moistab']['02'] = "Februarie";
$lang['moistab']['03'] = "Martie";
$lang['moistab']['04'] = "Aprilie";
$lang['moistab']['05'] = "Mai";
$lang['moistab']['06'] = "Iunie";
$lang['moistab']['07'] = "Iulie";
$lang['moistab']['08'] = "August";
$lang['moistab']['09'] = "Septembrie";
$lang['moistab']['10'] = "Octombrie";
$lang['moistab']['11'] = "Noiembrie";
$lang['moistab']['12'] = "Decembrie";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Ian";
$lang['moistab_graph']['02'] = "Feb";
$lang['moistab_graph']['03'] = "Mar";
$lang['moistab_graph']['04'] = "Apr";
$lang['moistab_graph']['05'] = "Mai";
$lang['moistab_graph']['06'] = "Iun";
$lang['moistab_graph']['07'] = "Iul";
$lang['moistab_graph']['08'] = "Aug";
$lang['moistab_graph']['09'] = "Sep";
$lang['moistab_graph']['10'] = "Oct";
$lang['moistab_graph']['11'] = "Nov";
$lang['moistab_graph']['12'] = "Dec";

// Day of the week
$lang['jsemaine']['Mon'] = "Luni";
$lang['jsemaine']['Tue'] = "Marti";
$lang['jsemaine']['Wed'] = "Miercuri";
$lang['jsemaine']['Thu'] = "Joi";
$lang['jsemaine']['Fri'] = "Vineri";
$lang['jsemaine']['Sat'] = "Sambata";
$lang['jsemaine']['Sun'] = "Duminica";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Lun";
$lang['jsemaine_graph']['Tue'] = "Mar";
$lang['jsemaine_graph']['Wed'] = "Mie";
$lang['jsemaine_graph']['Thu'] = "Joi";
$lang['jsemaine_graph']['Fri'] = "Vin";
$lang['jsemaine_graph']['Sat'] = "Sam";
$lang['jsemaine_graph']['Sun'] = "Dum";

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
$lang['afr'] = "Africa";
$lang['asi'] = "Asia";
$lang['ams'] = "America Centrala si de Sud";
$lang['amn'] = "America de Nord";
$lang['oce'] = "Oceania";

// Oceans
$lang['oc_pac'] = "Oceanul Pacific";
$lang['oc_atl'] = "Oceanul Atlantic";
$lang['oc_ind'] = "Oceanul  Indian";

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