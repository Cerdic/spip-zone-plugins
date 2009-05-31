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
$lang['auteur_nom'] = "Szabolcs Polgár"; // Translator's name
$lang['auteur_email'] = "http://www.hirtek.hu/"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "hu"; // iso language code
$lang['lang_libelle_en'] = "Magyar"; // hungarian language name
$lang['lang_libelle_fr'] = "Hongrois"; // french language name
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
$lang['separateur_milliers'] = ' '; // three thousand spells 3,000 in english
$lang['separateur_decimaux'] = ','; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | nyílt forráskódú weboldal statisztikai és web forgalom elemző alkalmazás"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, szkript, alkalmazás, szoftver, statisztika, hivatkozások, ingyenes, nyílt forráskódú, gpl, látogatók, látogatások, mysql, megtekintett oldalak, látogatás, látogatások száma, grafikon, böngészők, or, operációs rendszer, felbontás, nap, hét, hónap, bejegyzések, ország, hoszt, szolgáltatók, kereső motorok, kulcsszavak, belépő, kilépő oldal, kördiagram"; // Header keywords
$lang['head_description'] = "phpMyVisites | egy nyílt forráskódú weboldal statisztikai alkalmazás PHP/MySQL-ben fejlesztve, terjesztve a Gnu GPL licensz alatt."; // Header description
$lang['logo_description'] = "phpMyVisites | egy nyílt forráskódú weboldal statisztikai alkalmazás PHP/MySQL-ben fejlesztve, terjesztve a Gnu GPL licensz alatt."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "Látogatások";
$lang['menu_pagesvues'] = "Oldalak";
$lang['menu_suivi'] = "Követés";
$lang['menu_provenance'] = "Forrás";
$lang['menu_configurations'] = "Beállítások";
$lang['menu_affluents'] = "Hivatkozások";
$lang['menu_listesites'] = "Weboldalak";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Nap";
$lang['menu_semaine'] = "Hét";
$lang['menu_mois'] = "Hónap";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Vizsgált időszak: %s"; // Text formatted (e.g.: Studied period: Sunday, July the 14th)
$lang['liens_siteofficiel'] = "Hivatalos oldal";
$lang['liens_admin'] = "Adminisztráció";
$lang['liens_contacts'] = "Kapcsolat";

//
// Divers
//
$lang['generique_nombre'] = "Szám";
$lang['generique_tauxsortie'] = "Elhagyási ráta";
$lang['generique_ok'] = "Rendben";
$lang['generique_timefooter'] = "Oldal létrehozva %s másodperc alatt"; // Time in seconds
$lang['generique_divers'] = "Többi"; // (for the graphs)
$lang['generique_inconnu'] = "Ismeretlen"; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Többi?"; // Other language, translations wanted
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
$lang['generique_total'] = "Öszzes";
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
$lang['login_password'] = "jelszó : "; // lowercase
$lang['login_login'] = "név : "; // lowercase
$lang['login_error'] = "Hibás bejelentkezés. Rossz név vagy jelszó.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Kapcsolat";
$lang['contacts_langue'] = "Fordítások";
$lang['contacts_merci'] = "Köszönet";
$lang['contacts_auteur'] = "A phpMyVisites projekt szerzője, dokumentátora, készítője <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "A <strong>technikai kérdéseket, hibabejelentésket, javaslatokat</strong> a hivatalos weboldal fórumába küldhetitek el %s. Egyéb kívánságokat a hivatalos weboldal űrlapján keresztül várja a szerző."; // adresse du site
$lang['contacts_trad1'] = "Le szeretnéd fordítani a phpMyVisites-t a saját nyelvedre? Ne tétovázz, mert <strong> a phpMyVisites számít rád!</strong>";
$lang['contacts_trad2'] = "A phpMyVisites lefordítása (csak) néhány órába tellik, de gondolj arra, hogy <strong> így rengeteg felhasználónak segíthetsz</strong>.  A fordításhoz szükséges információkat %s a hivatalos phpMyVisites dokumentációban %s megtalálod."; // lien vers la doc
$lang['contacts_doc'] = "A %s hivatalos phpMyVisites dokumentáció %s tartalmaz minden olyan információt, ami az adott verzió telepítéséhez, beállításához kell."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "A hivatalos weboldal köszönetnyilvánítás oldalán a phpMyVisites összes barátját megtalálhatod.";
$lang['contacts_merci2'] = "Hatalmas köszönet a phpMyVisites fordítóinak, akik közreműködtek abban, hogy a projekt átlépje az országhatárokat :";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Látogató információk";
$lang['visites_statistiques'] = "Statisztika";
$lang['visites_periodesel'] = "A kiválasztott időszakra";
$lang['visites_visites'] = "Látogatások";
$lang['visites_uniques'] = "Egyedi látogatók";
$lang['visites_pagesvues'] = "Megtekintett oldalak";
$lang['visites_pagesvisiteurs'] = "Oldalak látogatónként";
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Átlagos látogatási időtartam";
$lang['visites_tempsmoyenpv'] = "Átlagos oldal megtekintési időtartam";
$lang['visites_tauxvisite'] = "1 oldal látogatási aránya";
$lang['visites_recapperiode'] = "Időszak összefoglalás";
$lang['visites_nbvisites'] = "Látogatások";
$lang['visites_aucunevivisite'] = "nincs látogatás"; // in the table, must be short
$lang['visites_recap'] = "Összefoglalás";
$lang['visites_unepage'] = "1 oldal"; // (graph)
$lang['visites_pages'] = "%s oldalak"; // 1-2 pages (graph)
$lang['visites_min'] = "%s p"; // 10-15 min (graph)
$lang['visites_sec'] = "%s mp"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "Grafikon mutatja a statisztika összegzését";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Grafikon mutatja a látogatók látogatási időtartamát";
$lang['visites_graphtempsvisitesimg'] = "Látogatók látogatási időtartama";
$lang['visites_graphheureserveur'] = "Grafikon mutatja a szerver óránkénti látogatását";
$lang['visites_graphheureserveurimg'] = "Látogatások a szerver ideje szerint";
$lang['visites_graphheurevisiteur'] = "Grafikon mutatja a látogatók óránkénti látogatását";
$lang['visites_graphheurelocalimg'] = "Látogatások a helyi idő szerint";
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
$lang['pagesvues_titre'] = "Oldal megtekintési információk";
$lang['pagesvues_joursel'] = "Kiválasztott nap";
$lang['pagesvues_jmoins7'] = "Nap - 7";
$lang['pagesvues_jmoins14'] = "Nap - 14";
$lang['pagesvues_moyenne'] = "(átlag)";
$lang['pagesvues_pagesvues'] = "Oldal megtekintések";
$lang['pagesvues_pagesvudiff'] = "Egyedi oldal megtekintések";
$lang['pagesvues_recordpages'] = "Egy látogató által meglátogatott oldalak legnagyobb száma";
$lang['pagesvues_tabdetails'] = "Megtekintett oldalak (%s - %s)"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "Grafikon mutatja az oldalak látogatási számát";
$lang['pagesvues_graphnbvisitespageimg'] = "Megtekintett oldalak látogatási száma";
$lang['pagesvues_graphheureserveur'] = "Grafikon mutatja a szerver idő szerinti látogatások számát";
$lang['pagesvues_graphheureserveurimg'] = "Szerver idő szerinti látogatások";
$lang['pagesvues_graphheurevisiteur'] = "Grafikon mutatja a helyi idő szerinti látogatásokat";
$lang['pagesvues_graphpageslocalimg'] = "Helyi idő szerinti látogatások";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Látogató aktivitás";
$lang['suivi_pageentree'] = "Belépő oldalak";
$lang['suivi_pagesortie'] = "Kilépő oldalak";
$lang['suivi_tauxsortie'] = "Kilépési arány";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Látogatók származása";
$lang['provenance_recappays'] = "Országok összefoglalása";
$lang['provenance_pays'] = "Országok";
$lang['provenance_paysimg'] = "Látogatók országonkénti diagramja";
$lang['provenance_fai'] = "Internet szolgáltatók";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Szolgáltatók"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Kontinens";
$lang['provenance_mappemonde'] = "Világtérkép";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Látogató beállítások";
$lang['configurations_os'] = "Operációs Rendszer";
$lang['configurations_osimg'] = "Grafikon mutatja a látogató operációs rendszerének típusát";
$lang['configurations_navigateurs'] = "Böngészők";
$lang['configurations_navigateursimg'] = "Grafikon mutatja a látogatók böngészőjének típusát";
$lang['configurations_resolutions'] = "Képernyő felbontások";
$lang['configurations_resolutionsimg'] = "Grafikon mutatja a látogatók képernyő felbontásait";
$lang['configurations_couleurs'] = "Színmélység";
$lang['configurations_couleursimg'] = "Grafikon mutatja a látogatók színmélység beállítását";
$lang['configurations_rapport'] = "Normál/Szélesvásznú";
$lang['configurations_large'] = "Szelesvasznu";
$lang['configurations_normal'] = "Normal";
$lang['configurations_double'] = "Dual Screen";
$lang['configurations_plugins'] = "Bővítmények";
$lang['configurations_navigateursbytype'] = "Böngészők (típusonként)";
$lang['configurations_navigateursbytypeimg'] = "Grafikon mutatja a böngészők típusait";
$lang['configurations_os_interest'] = "Operating Systems Interest";
$lang['configurations_navigateurs_interest'] = "Browsers Interest";
$lang['configurations_resolutions_interest'] = "Screen Resolutions Interest";
$lang['configurations_couleurs_interest'] = "Color Depth Interest";
$lang['configurations_configurations'] = "Top settings";

//
// Referers
//
$lang['affluents_titre'] = "Hivatkozások";
$lang['affluents_recapimg'] = "Látogatók diagramja hivatkozásonként";
$lang['affluents_directimg'] = "Közvetlen";
$lang['affluents_sitesimg'] = "Weboldalak";
$lang['affluents_moteursimg'] = "Keresők";
$lang['affluents_referrersimg'] = "Hivatkozások";
$lang['affluents_moteurs'] = "Kereső Motorok";
$lang['affluents_nbparmoteur'] = "Látogatások a kereső motorok által: %s";
$lang['affluents_aucunmoteur'] = "Nem voltak kereső motor látogatások.";
$lang['affluents_motscles'] = "Kulcsszavak";
$lang['affluents_nbmotscles'] = "Eltérő kulcsszavak : %s";
$lang['affluents_aucunmotscles'] = "Nem találhatóak kulcsszavak.";
$lang['affluents_sitesinternet'] = "Weboldalak";
$lang['affluents_nbautressites'] = "Egyéb weboldalakról származó látogatások : %s";
$lang['affluents_nbautressitesdiff'] = "Különböző weboldalak száma : %s";
$lang['affluents_aucunautresite'] = "Nincsenek egyéb weboldalakról származó látogatások.";
$lang['affluents_entreedirecte'] = "Közvetlen kérelem";
$lang['affluents_nbentreedirecte'] = "Közvetlen kérésű látogatások : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Részletek"; // In the results of the referers array
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
$lang['purge_titre'] = "A látogatások és hivatkozások összefoglalása";
$lang['purge_intro'] = "Ez az időszak el lett távolítva az adminisztrációból, csak a nélkülözhetetlen statisztikák maradtak meg.";
$lang['admin_purge'] = "Adatbázis karbantartás";
$lang['admin_purgeintro'] = "Itt tudod kezelni a phpMyVisites által használt táblákat. Ellenőrizheted a használt lemezterületet, optimalizálhatod, vagy eltávolíthatod a régi bejegyzéseket. Korlátozhatod a táblák méretét az adatbázisban.";
$lang['admin_optimisation'] = "A(z) [ %s ] tábla optimalizálása..."; // Tables names
$lang['admin_postopt'] = "Az összes méret csökkentése %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "Következő időszak eltávolítása: %s";
$lang['admin_purge_fini'] = "Táblák törlése befejeződött...";
$lang['admin_bdd_nom'] = "Név";
$lang['admin_bdd_enregistrements'] = "Bejegyzés";
$lang['admin_bdd_taille'] = "Méret";
$lang['admin_bdd_opt'] = "Optimalizál";
$lang['admin_bdd_purge'] = "Karbantartás feltétele";
$lang['admin_bdd_optall'] = "Összes Optimalizálása";
$lang['admin_purge_j'] = "A %s napnál régebbi bejegyzések eltávolítása";
$lang['admin_purge_s'] = "A %s hétnél régebbi bejegyzések eltávolítása";
$lang['admin_purge_m'] = "A %s hónapnál régebbi bejegyzések eltávolítása";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Összes napló eltávolítása";
$lang['admin_purge_autres'] = "Karbantartás a táblába '%s'";
$lang['admin_purge_none'] = "Nem lehetséges művelet";
$lang['admin_purge_cal'] = "Számítás és karbantartás...(néhány perc)";
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
$lang['admin_intro'] = "Üdvözöllek a phpMyVisites konfigurálásánál. A telepítés minden információját itt módosíthatod. Ha eközben bármi problémád adódna, ne habozz belenézni %s a phpMyVisites hivatalos dokumentációjába %s."; // link to the doc
$lang['admin_configetperso'] = "Általános Beállítások";
$lang['admin_afficherjavascript'] = "JavaScript statisztikai kód lekérése";
$lang['admin_cookieadmin'] = "Az adminisztrátort ne rögzítse a statisztika";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Vizsgált webodalak:";
$lang['admin_retour'] = "Vissza";
$lang['admin_cookienavigateur'] = "Letilthatod az Adminisztrátort a statisztikából. Ez a művelet süti alapú, és csak a jelenlegi böngészővel működik. Ezt az opciót bármikor módosíthatod.";
$lang['admin_prendreencompteadmin'] = "Az Adminisztrátort is vizsgálja a statisztika (süti törlése)";
$lang['admin_nepasprendreencompteadmin'] = "Ne számolja bele az Adminisztrátort a statisztikába (süti készítése)";
$lang['admin_etatcookieoui'] = "Az Adminisztrátort is beleszámolja a weboldal statisztikájába (Ez az alapértelmezett beállítás, normál látogatóként vagy azonosítva)";
$lang['admin_etatcookienon'] = "Nem vagy beleszámolva a weboldal statisztikájába (A látogatásaidat nem veszi figyelembe az oldal statisztikája)";
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
$lang['install_loginmysql'] = "Adatbázis bejelentkezési név";
$lang['install_mdpmysql'] = "Adatbázis jelszó";
$lang['install_serveurmysql'] = "Adatbázis szerver";
$lang['install_basemysql'] = "Adatbázis név";
$lang['install_prefixetable'] = "Tábla előtag";
$lang['install_utilisateursavances'] = "Haladó felhasználók (opcionális)";
$lang['install_oui'] = "Igen";
$lang['install_non'] = "Nem";
$lang['install_ok'] = "Rendben";
$lang['install_probleme'] = "Probléma: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Adminisztrátor bejelentkezés:";
$lang['install_mdpadmin'] = "Adminisztrátor jelszó:";
$lang['install_chemincomplet'] = "Teljes elérési út a phpMyVisites programhoz (mint http://www.oldalam.hu/rep1/rep3/phpmyvisites/). Az útvonal végéről ezt ne hagyd le: <strong>/</strong>.";
$lang['install_afficherlogo'] = "Legyen látható a logo? %s <br />Ha ezt engedélyezed, a weboldaladon megjelenik a phpMyVisites logoja, és így segíthetsz reklámozni a szoftvert. Ez egy lehetőség arra, hogy megköszönd a szerzőnek a sok órát, amit eltöltött ezen nyílt forráskódú, ingyenes szoftver kifejlesztésével."; // %s replaced by the logo image
$lang['install_affichergraphique'] = "Statisztikai garfikonok mutatása.";
$lang['install_valider'] = "Mehet"; //  during installation and for login
$lang['install_popup_logo'] = "Válassz egy logot!";
$lang['install_logodispo'] = "Nézd meg a rendelkezésre álló többi logot is!";
$lang['install_welcome'] = "Welcome!";
$lang['install_system_requirements'] = "System Requirements";
$lang['install_database_setup'] = "Database Setup";
$lang['install_create_tables'] = "Table creation";
$lang['install_general_setup'] = "General Setup";
$lang['install_create_config_file'] = "Create Config File";
$lang['install_first_website_setup'] = "Add First Website";
$lang['install_display_javascript_code'] = "Display Javascript code";
$lang['install_finish'] = "Finished!";
$lang['install_txt2'] = "A telepítés befejezésekor tudathatod velünk a hivatalos honlapon, hogy a phpMyVisites-t használod, így egy képet kaphatunk a projektet használó emnberek számáról. Segítségedet köszönjük.";
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
$lang['update_jschange'] = "Figyelmeztetés! <br /> A phpMyVisites javascript kód megváltozott. Frissítened kell az oldalaidat a phpMyVisites új Javascript kódjával minden honlapon. <br /> A javascript kód megváltozása elég ritka, elnézésedet kérjük a kellemetlenségekért.";

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
$lang['moistab']['01'] = "Január";
$lang['moistab']['02'] = "Február";
$lang['moistab']['03'] = "Március";
$lang['moistab']['04'] = "Április";
$lang['moistab']['05'] = "Május";
$lang['moistab']['06'] = "Június";
$lang['moistab']['07'] = "Július";
$lang['moistab']['08'] = "Augusztus";
$lang['moistab']['09'] = "Szeptember";
$lang['moistab']['10'] = "Október";
$lang['moistab']['11'] = "November";
$lang['moistab']['12'] = "December";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Jan";
$lang['moistab_graph']['02'] = "Feb";
$lang['moistab_graph']['03'] = "Mar";
$lang['moistab_graph']['04'] = "Apr";
$lang['moistab_graph']['05'] = "Maj";
$lang['moistab_graph']['06'] = "Jun";
$lang['moistab_graph']['07'] = "Jul";
$lang['moistab_graph']['08'] = "Aug";
$lang['moistab_graph']['09'] = "Sze";
$lang['moistab_graph']['10'] = "Okt";
$lang['moistab_graph']['11'] = "Nov";
$lang['moistab_graph']['12'] = "Dec";

// Day of the week
$lang['jsemaine']['Mon'] = "Hétfő";
$lang['jsemaine']['Tue'] = "Kedd";
$lang['jsemaine']['Wed'] = "Szerda";
$lang['jsemaine']['Thu'] = "Csütörtök";
$lang['jsemaine']['Fri'] = "Péntek";
$lang['jsemaine']['Sat'] = "Szombat";
$lang['jsemaine']['Sun'] = "Vasárnap";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Het";
$lang['jsemaine_graph']['Tue'] = "Ked";
$lang['jsemaine_graph']['Wed'] = "Sze";
$lang['jsemaine_graph']['Thu'] = "Csu";
$lang['jsemaine_graph']['Fri'] = "Pen";
$lang['jsemaine_graph']['Sat'] = "Szo";
$lang['jsemaine_graph']['Sun'] = "Vas";

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
$lang['eur'] = "Európa";
$lang['afr'] = "Afrika";
$lang['asi'] = "Ázsia";
$lang['ams'] = "Közép-, és Dél-Amerika";
$lang['amn'] = "Észak-Amerika";
$lang['oce'] = "Óceánia";

// Oceans
$lang['oc_pac'] = "Csendes Óceán";
$lang['oc_atl'] = "Atlanti Óceán";
$lang['oc_ind'] = "Indiai Óceán";

// Countries
$lang['domaines'] = array(
    "xx" => "Ismeretlen",
    "ac" => "Ascension-Szigetek",
    "ad" => "Andorra",
    "ae" => "Egyesült Arab Emirátusok",
    "af" => "Afganisztán",
    "ag" => "Antigua és Barbuda",
    "ai" => "Anguilla",
    "al" => "Albánia",
    "am" => "Örményország",
    "an" => "Netherlands Antilles",
    "ao" => "Angola",
    "aq" => "Antarktika",
    "ar" => "Argentína",
    "as" => "Amerikai Szamoa",
    "at" => "Ausztria",
    "au" => "Ausztrália",
    "aw" => "Aruba",
    "az" => "Azerbajdzsán",
    "ba" => "Bosznia és Herzegovina",
    "bb" => "Barbados",
    "bd" => "Banglades",
    "be" => "Belgium",
    "bf" => "Burkina Faso",
    "bg" => "Bulgária",
    "bh" => "Bahrain",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermuda",
    "bn" => "Bruneo",
    "bo" => "Bolívia",
    "br" => "Brazília",
    "bs" => "Bahamák",
    "bt" => "Bhutan",
    "bv" => "Bouvet-Sziget",
    "bw" => "Botswana",
    "by" => "Belarusz",
    "bz" => "Belize",
    "ca" => "Kanada",
    "cc" => "Kókusz-Szigetek",
    "cd" => "Kongói Demokratikus Köztársaság",
    "cf" => "Közép Afrikai Köztársaság",
    "cg" => "Kongó",
    "ch" => "Svájc",
    "ci" => "Cote D'Ivoire",
    "ck" => "Cook-Szigetek",
    "cl" => "Chile",
    "cm" => "Cameroon",
    "cn" => "Kína",
    "co" => "Kolumbia",
    "cr" => "Costa Rica",
	"cs" => "Serbia Montenegro",
    "cu" => "Kuba",
    "cv" => "Cape Verde",
    "cx" => "Karácsony-Sziget",
    "cy" => "Ciprus",
    "cz" => "Cseh Köztársaság",
    "de" => "Németország",
    "dj" => "Djibouti",
    "dk" => "Dánia",
    "dm" => "Dominika",
    "do" => "Dominikai Köztársaság",
    "dz" => "Algéria",
    "ec" => "Ecuador",
    "ee" => "Észtország",
    "eg" => "Egyiptom",
    "eh" => "Nyugat-Szahara",
    "er" => "Eritrea",
    "es" => "Spanyolország",
    "et" => "Etiópia",
    "fi" => "Finnország",
    "fj" => "Fiji",
    "fk" => "Falkland-Szigetek",
    "fm" => "Mikronéziai Szövetségi Köztársaság",
    "fo" => "Faroe-Szigetek",
    "fr" => "Franciaország",
    "ga" => "Gabon",
    "gd" => "Grenada",
    "ge" => "Georgia",
    "gf" => "Francia Guyana",
    "gg" => "Guernsey",
    "gh" => "Ghana",
    "gi" => "Gibraltár",
    "gl" => "Grönland",
    "gm" => "Gambia",
    "gn" => "Guinea",
    "gp" => "Guadeloupe",
    "gq" => "Egyenlítői-Guinea",
    "gr" => "Görögország",
    "gs" => "Dél-Georgia és a Dél-Sandwich-Szigetek",
    "gt" => "Guatemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard-Sziget és a McDonald -Szigetek",
    "hn" => "Honduras",
    "hr" => "Horvátország",
    "ht" => "Haiti",
    "hu" => "Magyarország",
    "id" => "Indonézia",
    "ie" => "Írország",
    "il" => "Izrael",
    "im" => "Man-Sziget",
    "in" => "India",
    "io" => "Brit Indiai-Óceáni Övezet",
    "iq" => "Irak",
    "ir" => "Iráni Iszlám Köztársaság",
    "is" => "Izland",
    "it" => "Olaszország",
    "je" => "Jersey",
    "jm" => "Jamaica",
    "jo" => "Jordánia",
    "jp" => "Japán",
    "ke" => "Kenya",
    "kg" => "Kyrgyzstan",
    "kh" => "Kambodzsa",
    "ki" => "Kiribati",
    "km" => "Comoros",
    "kn" => "Saint Kitts és Nevis",
    "kp" => "Koreai Demokratikus Köztársaság",
    "kr" => "Koreai Köztársaság",
    "kw" => "Kuvait",
    "ky" => "Cayman-Szigetek",
    "kz" => "Kazahsztán",
    "la" => "Laosz",
    "lb" => "Libanon",
    "lc" => "Saint Lucia",
    "li" => "Liechtenstein",
    "lk" => "Srí Lanka",
    "lr" => "Libéria",
    "ls" => "Lesotho",
    "lt" => "Litvánia",
    "lu" => "Luxemburg",
    "lv" => "Lettország",
    "ly" => "Líbia",
    "ma" => "Marokkó",
    "mc" => "Monaco",
    "md" => "Moldovai Köztársaság",
    "mg" => "Madagaszkár",
    "mh" => "Marshall-Szigetek",
    "mk" => "Macedonia",
    "ml" => "Mali",
    "mm" => "Myanmar",
    "mn" => "Mongólia",
    "mo" => "Macau",
    "mp" => "Észak-Mariana-Szigetek",
    "mq" => "Martinique",
    "mr" => "Mauritánia",
    "ms" => "Montserrat",
    "mt" => "Málta",
    "mu" => "Mauritius",
    "mv" => "Maldív-Szigetek",
    "mw" => "Malawi",
    "mx" => "Mexikó",
    "my" => "Malajzia",
    "mz" => "Mozambik",
    "na" => "Namíbia",
    "nc" => "Új-Kaledónia",
    "ne" => "Niger",
    "nf" => "Norfolk-Sziget",
    "ng" => "Nigéria",
    "ni" => "Nicaragua",
    "nl" => "Netherlands",
    "no" => "Norvégia",
    "np" => "Nepál",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "Új-Zéland",
    "om" => "Oman",
    "pa" => "Panama",
    "pe" => "Peru",
    "pf" => "Francia-Polinézia",
    "pg" => "Pápua Új-Guinea",
    "ph" => "Fülöp-Szigetek",
    "pk" => "Pakisztán",
    "pl" => "Lengyelország",
    "pm" => "Saint Pierre és Miquelon",
    "pn" => "Pitcairn",
    "pr" => "Puerto Rico",
    "pt" => "Portugália",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Katar",
    "re" => "Reunion-Sziget",
    "ro" => "Románia",
    "ru" => "Orosz Szövetségi Állam",
    "rs" => "Oroszország",
    "rw" => "Ruanda",
    "sa" => "Szaúd-Arábia",
    "sb" => "Salamon-Szigetek",
    "sc" => "Seychelles-Szigetek",
    "sd" => "Szudán",
    "se" => "Svédország",
    "sg" => "Szingapúr",
    "sh" => "Saint Helena",
    "si" => "Szlovénia",
    "sj" => "Svalbard",
    "sk" => "Szlovákia",
    "sl" => "Sierra Leone",
    "sm" => "San Marino",
    "sn" => "Szenegál",
    "so" => "Szomália",
    "sr" => "Suriname",
    "st" => "Sao Tome és Principe",
    "su" => "Régi U.R.S.S.",
    "sv" => "El Salvador",
    "sy" => "Szír Arab Köztársaság",
    "sz" => "Szváziföld",
    "tc" => "Török és Caicos-Szigetek",
    "td" => "Csád",
    "tf" => "Francia Déli Övezetek",
    "tg" => "Togo",
    "th" => "Thaiföld",
    "tj" => "Tajikistan",
    "tk" => "Tokelau",
    "tm" => "Turkmenistan",
    "tn" => "Tunézia",
    "to" => "Tonga",
    "tp" => "Észak Timor",
    "tr" => "Törökország",
    "tt" => "Trinidad és Tobago",
    "tv" => "Tuvalu",
    "tw" => "Taiwan Kínai Tartománya",
    "tz" => "Tanzániai Egyesült Köztársaság",
    "ua" => "Ukrajna",
    "ug" => "Uganda",
    "uk" => "Egyesült Királyság",
    "gb" => "Nagy-Britannia",
    "um" => "Minor Outlying-Szigetek Egyesült Államai",
    "us" => "Egyesült Államok",
    "uy" => "Uruguay",
    "uz" => "Üzbegisztán",
    "va" => "Vatikán Város",
    "vc" => "Saint Vincent és a Grenadines",
    "ve" => "Venezuela",
    "vg" => "Brit Virgin-Szigetek",
    "vi" => "Egyesült Államok Virgin-Szigetei",
    "vn" => "Vietnám",
    "vu" => "Vanuatu",
    "wf" => "Wallis és Futuna",
    "ws" => "Szamoa",
    "ye" => "Jemen",
    "yt" => "Mayotte",
    "yu" => "Jugoszlávia",
    "za" => "Dél-Afrika",
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
    "ws" => "Szamoa",
);
?>