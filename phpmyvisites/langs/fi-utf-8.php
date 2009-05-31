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
$lang['auteur_nom'] = "Jarno Tukiainen"; // Kääntäjän nimi
$lang['auteur_email'] = "jarno.tukiainen@raja-antura.org"; // Kääntäjän email
$lang['charset'] = "utf-8"; // Kielitiedoston merkkikoodaus (Oletuksenautf-8)
$lang['text_dir'] = "ltr"; // ('ltr' vasemalta oikealle, 'rtl' oikealta vasemmalle)
$lang['lang_iso'] = "fi"; // ISO-kielikoodi
$lang['lang_libelle_en'] = "Finnish"; // Englanninkielinen kielen nimi
$lang['lang_libelle_fr'] = "Finnois"; // Ranskankielinen kielen nimi
$lang['unites_bytes'] = array('Tavua', 'Kt', 'Mt', 'Gt', 'Tt', 'Pt', 'Et', 'Zt', 'Yt');
$lang['separateur_milliers'] = ''; // Tuhansien erotinmerkki (kolme tuhatta sanotaan englanniksi 3,000)
$lang['separateur_decimaux'] = ','; // Desimaalierotin

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisities | Avoimen lähdekoodin internet-sivujen statistiikka- sekä liikennemääräanalysaattori"; // Sivujen otsake
$lang['head_keywords'] = "phpmyvisites, php, skripti, sovellus, ohjelmisto, statistiikka, suosittaja, avoin lädekoodi, ilmainen, vapaa, gpl, vierailijaa, vierailuja, mysql, nätetyt sivut, sivut, näyttö�, näyttöjen määrä� tilastot, Selaimet, kättöjärjestelmä, os, resoluutiot, näyttötarkkuus, päivä, viikko, kuukausi, tallennetta, maa, isäntä, palveluntarjoajat, hakukone, avainsanat, viittaajat, tilastot, avaussivut, poistumissivut, piirakkakaavio, kaavio"; // Otsakkeen avainsanat
$lang['head_description'] = "phpMyVisites | Avoimen lähdekoodin tilasto-ohjema, joka on kehitetty PHP/MySQL:llä ja jaetaan Gnu GPL:n alaisuudessa."; // Otsakkeen kuvaus
$lang['logo_description'] = "phpMyVisites : avoimen lähdekoodin nettisivujen statistiikkaohjelmisto, joka on kehitetty PHP/MySQL:llä ja ja jota jaetaan GPL:n alaisena."; // Javascriptin kuvaus, pitää olla lyhyt

//
// Main menu & submenu
//
$lang['menu_visites'] = "Käynnit";
$lang['menu_pagesvues'] = "Sivunäytöt";
$lang['menu_suivi'] = "Lisätietoa";
$lang['menu_provenance'] = "Lähde";
$lang['menu_configurations'] = "Asetukset";
$lang['menu_affluents'] = "Viittaajat";
$lang['menu_listesites'] = "Sivustolistaus";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Päivä";
$lang['menu_semaine'] = "Viikko";
$lang['menu_mois'] = "Kuukausi";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Tutkittu ajanjakso: %s"; // Formatoitu teksti (esim. : Tutkittu ajanjakso: Sunnuntai, Heinäkuun 14.)
$lang['liens_siteofficiel'] = "Ohjelman virallinen kotisivu";
$lang['liens_admin'] = "Ylläpito";
$lang['liens_contacts'] = "Yhteystiedot";

//
// Divers
//
$lang['generique_nombre'] = "Numero";
$lang['generique_tauxsortie'] = "Poistumistiheys";
$lang['generique_ok'] = "OK";
$lang['generique_timefooter'] = "Sivu luotu %s sekuntissa"; // Aika sekunneissa
$lang['generique_divers'] = "Muut"; // (kaavioita varten)
$lang['generique_inconnu'] = "Tuntematon"; // (kaavioita varten)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Muu?"; // Muu kieli, käännös tarvitaan
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
$lang['generique_total'] = "Yhteensä";
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
$lang['login_password'] = "salasana : "; // pienet kirjaimet
$lang['login_login'] = "käyttäjätunnus : "; // pienet kirjaimet
$lang['login_error'] = "Ei voitu kirjautua. Virheellinen käyttäjätunnus tai salasana.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Yhteystiedot";
$lang['contacts_langue'] = "Käännökset";
$lang['contacts_merci'] = "Kiitokset";
$lang['contacts_auteur'] = "PhpMyVisitesin alkuunpanija, dokumentoija sekä luoja on <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "<strong>Teknisä kysymyksiä, virheraportteja sekä ehdotuksia varten</strong> olkaa hyvä ja käyttäkää virallisen sivusto keskustelualuetta %s. Muita pyyntöjä varten, ottakaa yhteyttä alkuperäiseen kehittäjään käyttäen kaavaketta joka löytyy ohjelman kotisivulta."; // Sivun osoite
$lang['contacts_trad1'] = "Haluatko kääntää phpMyVisitesin omalle kielellesi ? Älä epäröi, sillä <strong>phpMyVisites</strong> tarvitsee sinua !";
$lang['contacts_trad2'] = "phpMyVisitesin kääntäminen vie jonkin aikaa (muutaman tunnin) ja vaatii hyvän kielitaidon käännettävistä kielistä, mutta muista, että <strong>mitä tahansa työtä teetkin, moni muu käyttäjä hyötyy siitä todella paljon</strong>. Jos olet kiinnostunut kääntämään phpMyVisitesiä, voit löytää sitä varten tarvittavan tiedon %s phpMyVisitesin virallisesta dokumentaatiosta %s."; // linkki dokumentaatioon
$lang['contacts_doc'] = "Älä epäröikö turvautua %s phpMyVisitesin viralliseen dokumentaatioon %s, sillä se antaa sinulle laajalti tietoa asennuksesta, asetuksita sekä phpMyVisitesin toiminnasta. Dokumentaatio on saatavilla omassa phpMyVisitesin versiossasi."; // linkki dokumentaatioon
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "Älä epäröi katsoa virallisten sivujen kiitossivulle saadaksesi täydellisen listan phpMyVisitesin ystävistä.";
$lang['contacts_merci2'] = "Suuri kiitos kaikille niille, jotka ovat jakaneet kulttuuriaan lahjoittaessaan käännöksiä phpMyVisitesiin:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Tietoa kävijöistä"; 
$lang['visites_statistiques'] = "Tilastot";
$lang['visites_periodesel'] = "Valitulle ajanjaksolle";
$lang['visites_visites'] = "Käyntejä";
$lang['visites_uniques'] = "Uniikkeja kävijöitä";
$lang['visites_pagesvues'] = "Sivunäyttöjä";
$lang['visites_pagesvisiteurs'] = "Sivuja kävijää kohden"; 
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Käynnin keskimääräinen kesto";
$lang['visites_tempsmoyenpv'] = "Keskimääräinen kesto sivua kohden";
$lang['visites_tauxvisite'] = "Yhden sivun käynnin suhde"; 
$lang['visites_recapperiode'] = "Aikajakson yhteenveto";
$lang['visites_nbvisites'] = "Käyntejä";
$lang['visites_aucunevivisite'] = "Ei käyntejä"; // taulukossa, pitää olla lyhyt
$lang['visites_recap'] = "Yhteenveto";
$lang['visites_unepage'] = "Yksi sivu"; // kaavio
$lang['visites_pages'] = "%s sivua"; // 1-2 sivua (kaavio)
$lang['visites_min'] = "%s min"; // 10-15 min (kaavio)
$lang['visites_sec'] = "%s s"; // 0-30 s (sekuntia,kaavio)
$lang['visites_grapghrecap'] = "Tilastojen yhteenvedon kaaviokuva";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Kaaviokuva käynnin kestosta kävijää kohden";
$lang['visites_graphtempsvisitesimg'] = "Käynnin kesto kävijää kohden";
$lang['visites_graphheureserveur'] = "Käyntejä tuntia kohden palvelimen kellon mukaan"; 
$lang['visites_graphheureserveurimg'] = "Käyntejä palvelimen aikaan"; 
$lang['visites_graphheurevisiteur'] = "Käyntejä tuntia kohden kävijän kellon mukaan";
$lang['visites_graphheurelocalimg'] = "Käyntejä paikalliseen aikaan"; 
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
$lang['pagesvues_titre'] = "Tietoa sivunäytöistä";
$lang['pagesvues_joursel'] = "Valittu päivä";
$lang['pagesvues_jmoins7'] = "Päivä - 7";
$lang['pagesvues_jmoins14'] = "Päivä - 14";
$lang['pagesvues_moyenne'] = "(keskiarvo)";
$lang['pagesvues_pagesvues'] = "Sivunäyttöjä";
$lang['pagesvues_pagesvudiff'] = "Yksittäisiä sivunäyttöjä";
$lang['pagesvues_recordpages'] = "Eniten sivuja yksittäistä kävijää kohden";
$lang['pagesvues_tabdetails'] = "Sivunäyttöjä (%s - %s)"; // (yhdestä kahteen)
$lang['pagesvues_graphsnbpages'] = "Kaaviokuva käynneistä sivunäyttöä kohden";
$lang['pagesvues_graphnbvisitespageimg'] = "Käyntejä sivunäyttöjä kohden";
$lang['pagesvues_graphheureserveur'] = "Kaaviokuva käynneistä palvelimen kellon mukaan";
$lang['pagesvues_graphheureserveurimg'] = "Käyntejä palvelimen aikaan";
$lang['pagesvues_graphheurevisiteur'] = "Kaaviokuva käynneistä kävijän kellon mukaan";
$lang['pagesvues_graphpageslocalimg'] = "Käyntejä paikalliseen aikaan";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Kävijän liikkuminen";
$lang['suivi_pageentree'] = "Saapumissivut";
$lang['suivi_pagesortie'] = "Poistumissivut";
$lang['suivi_tauxsortie'] = "Poistumissuhde";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Kävijöiden alkuperä";
$lang['provenance_recappays'] = "Maiden yhteenveto";
$lang['provenance_pays'] = "Maat";
$lang['provenance_paysimg'] = "Kävijäkaavio maan mukaan";
$lang['provenance_fai'] = "Palveluntarjoajat";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Palveluntarjoajat"; // Sama kuin $lang['provenance_fai'], paitsi jos $lang['provenance_fai'] on liian pitkä
$lang['provenance_continent'] = "Manner";
$lang['provenance_mappemonde'] = "Maailmankartta";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Kävijöiden asetukset";
$lang['configurations_os'] = "Käyttöjärjestelmät";
$lang['configurations_osimg'] = "Kaaviokuva kävijöiden käyttöjärjestelmistä";
$lang['configurations_navigateurs'] = "Selaimet";
$lang['configurations_navigateursimg'] = "Kaaviokuva kävijöiden selaimista";
$lang['configurations_resolutions'] = "Resoluutiot";
$lang['configurations_resolutionsimg'] = "Kaaviokuva kävijöiden resoluutioista";
$lang['configurations_couleurs'] = "Värisyvyys";
$lang['configurations_couleursimg'] = "Kaaviokuva kävijöiden värisyvyydestä";
$lang['configurations_rapport'] = "Normaali/laajakuva";
$lang['configurations_large'] = "Laajakuva";
$lang['configurations_normal'] = "Normaali";
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
$lang['affluents_titre'] = "Viittaajat";
$lang['affluents_recapimg'] = "Kaavio kävijöistä viittaajan mukaan";
$lang['affluents_directimg'] = "Suorat pyynnöt";
$lang['affluents_sitesimg'] = "Sivustot";
$lang['affluents_moteursimg'] = "Hakukoneet";
$lang['affluents_referrersimg'] = "Viittaajat";
$lang['affluents_moteurs'] = "Hakukoneet";
$lang['affluents_nbparmoteur'] = "Hakukoneiden käynnit : %s";
$lang['affluents_aucunmoteur'] = "Käyntejä ei ole kertynyt hakukoneista.";
$lang['affluents_motscles'] = "Avainsanat";
$lang['affluents_nbmotscles'] = "Eritellyt avainsanat : %s";
$lang['affluents_aucunmotscles'] = "Avainsanoja ei löytynyt.";
$lang['affluents_sitesinternet'] = "Sivustot";
$lang['affluents_nbautressites'] = "Muilta sivuilta koostuneet käynnit : %s";
$lang['affluents_nbautressitesdiff'] = "Muiden sivustojen lukumäärä : %s";
$lang['affluents_aucunautresite'] = "Käyntejä ei ole kertynyt muilta sivustoilta.";
$lang['affluents_entreedirecte'] = "Suorat pyynnöt";
$lang['affluents_nbentreedirecte'] = "Suorista pyynnöistä koostuvat käynnit : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Yksityiskohdat"; // Viittaajataulukon tuloksissa
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
$lang['purge_titre'] = "Käyntien ja viittaajien yhteenveto";
$lang['purge_intro'] = "Pyydetty aikajakso on poistettu ylläpidosta. Vain oleelliset tiedot on säilytetty.";
$lang['admin_purge'] = "Tietokannan hallinta";
$lang['admin_purgeintro'] = "Tämä osio antaa sinun hallita phpMyVisitesin käyttämiä taulukoita. Voit nähdä niiden viemän kiintolevytilan, optimoida niitä tai poistaa vanhoja tallenteita. Tämä antaa sinulle mahdollisuuden rajoittaa tietokannan taulukoiden kokoa.";
$lang['admin_optimisation'] = "Taulukon [ %s ] optimointi ..."; // Taulukon nimet
$lang['admin_postopt'] = "Koko pienentyi %chiffres% %unites%"; // 28 Kt
$lang['admin_purgeres'] = "Poista seuraavat aikajaksot: %s";
$lang['admin_purge_fini'] = "Taulukoiden poisto suoritettu...";
$lang['admin_bdd_nom'] = "Nimi";
$lang['admin_bdd_enregistrements'] = "Tallenteet";
$lang['admin_bdd_taille'] = "Taulukon koko";
$lang['admin_bdd_opt'] = "Optimoi";
$lang['admin_bdd_purge'] = "Puhdistuksen kriteria";
$lang['admin_bdd_optall'] = "Optimoi kaikki";
$lang['admin_purge_j'] = "Poista %s päivää vanhemmat tallenteet";
$lang['admin_purge_s'] = "Poista %s viikkoa vanhemmat tallenteet";
$lang['admin_purge_m'] = "Poista %s kuukautta vanhemmat tallenteet";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Poista kaikki lokitiedostot";
$lang['admin_purge_autres'] = "Puhdistus yleistä taululle '%s'";
$lang['admin_purge_none'] = "Toiminta ei mahdollinen";
$lang['admin_purge_cal'] = "Arvioi ja puhdista (tämä voi kestää useita minuutteja)";
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
$lang['admin_intro'] = "Tervetuloa phpMyVisitesin asetusten muokkausalueelle. Täällä voit kaikkia muokata asennukseesi liittyviä tietoja.Welcome to the phpMyVisites configuration area. Jos ongelmia ilmenee niin älä epäröi turvautua viralliseen %s phpMyVisitesin dokumentaatioon %s."; // linkki dokumentaatioon
$lang['admin_configetperso'] = "Yleiset asetukset";
$lang['admin_afficherjavascript'] = "Näytä Javascript statistiikkakoodi";
$lang['admin_cookieadmin'] = "Älä laske ylläpitäjää statistiikkoihin";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Tallennettuja sivustoja:";
$lang['admin_retour'] = "Takaisin";
$lang['admin_cookienavigateur'] = "Voit jättää ylläpitäjän pois tilastojen laskennasta. Menetelmä on evästepohjainen ja toimii vain sillä selaimella jota tällä hetkellä käytät. Voit muokata tätä valintaa milloin vain.";
$lang['admin_prendreencompteadmin'] = "Laske ylläpitäjä mukaan tilastoihin (poista eväste)";
$lang['admin_nepasprendreencompteadmin'] = "Älä laske ylläpitäjää mukaan tilastoihin (luo eväste)";
$lang['admin_etatcookieoui'] = "Ylläpitäjä lasketaan tämän sivuston tilastoihin (Tämä on vakioasetus, sinua käsitellään normaalina kävijänä)";
$lang['admin_etatcookienon'] = "Sinua ei lasketa mukaan tämän sivuston tilastoihin (Käyntejäsi ei lasketa tälle sivustolle)";
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
$lang['install_loginmysql'] = "Tietokannan käyttäjätunnus";
$lang['install_mdpmysql'] = "Tietokannan salasana";
$lang['install_serveurmysql'] = "Tietokantapalvelin";
$lang['install_basemysql'] = "Tietokannan nimi";
$lang['install_prefixetable'] = "Taulukoiden etuliite";
$lang['install_utilisateursavances'] = "Edistyneet käyttäjät (Valinnainen)";
$lang['install_oui'] = "Kyllä";
$lang['install_non'] = "Ei";
$lang['install_ok'] = "OK";
$lang['install_probleme'] = "Ongelma: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Ylläpidon käyttäjätunnus:";
$lang['install_mdpadmin'] = "Ylläpidon salasana:";
$lang['install_chemincomplet'] = "Täydellinen polku phpMyVisitesin sovellukseen (kuten http://www.minunsivu.com/hakemisto1/hakemisto2/phpmyvisites/). Hakemistopolun tulee päättyä <strong>/</strong>-merkkiin.";
$lang['install_afficherlogo'] = "Näytä logo sivostolla ? %s <br />By allowing the display of the logo on your site, you will help publicize phpMyVisites and help it evolve more rapidly.  It is also a way to thank the author who has spent many hours developing this Open Source, free application."; // %s korvattu logokuvalle
$lang['install_affichergraphique'] = "Näytä kaaviokuvat tilastoissa.";
$lang['install_valider'] = "Lähetä"; //  asennuksen aikana ja loginiin
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
$lang['install_txt2'] = "Asennuksen lopussa tullaan lähettämään pyyntö viralliselle kotisivulle, joka helpottaa meitä pitämään yllä phpMyVisitesin käyttäjien lukumäärää. Kiitos ymmärtämyksestänne.";
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
$lang['moistab']['01'] = "Tammikuu";
$lang['moistab']['02'] = "Helmikuu";
$lang['moistab']['03'] = "Maaliskuu";
$lang['moistab']['04'] = "Huhtikuu";
$lang['moistab']['05'] = "Toukokuu";
$lang['moistab']['06'] = "Kesäkuu";
$lang['moistab']['07'] = "Heinäkuu";
$lang['moistab']['08'] = "Elokuu";
$lang['moistab']['09'] = "Syyskuu";
$lang['moistab']['10'] = "Lokakuu";
$lang['moistab']['11'] = "Marraskuu";
$lang['moistab']['12'] = "Joulukuu";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Tam";
$lang['moistab_graph']['02'] = "Hel";
$lang['moistab_graph']['03'] = "Maa";
$lang['moistab_graph']['04'] = "Huh";
$lang['moistab_graph']['05'] = "Tou";
$lang['moistab_graph']['06'] = "Kes";
$lang['moistab_graph']['07'] = "Hei";
$lang['moistab_graph']['08'] = "Elo";
$lang['moistab_graph']['09'] = "Syy";
$lang['moistab_graph']['10'] = "Lok";
$lang['moistab_graph']['11'] = "Mar";
$lang['moistab_graph']['12'] = "Jou";

// Day of the week
$lang['jsemaine']['Mon'] = "Maanantai";
$lang['jsemaine']['Tue'] = "Tiistai";
$lang['jsemaine']['Wed'] = "Keskiviikko";
$lang['jsemaine']['Thu'] = "Torstai";
$lang['jsemaine']['Fri'] = "Perjantai";
$lang['jsemaine']['Sat'] = "Lauantai";
$lang['jsemaine']['Sun'] = "Sunnuntai";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Ma";
$lang['jsemaine_graph']['Tue'] = "Ti";
$lang['jsemaine_graph']['Wed'] = "Ke";
$lang['jsemaine_graph']['Thu'] = "To";
$lang['jsemaine_graph']['Fri'] = "Pe";
$lang['jsemaine_graph']['Sat'] = "La";
$lang['jsemaine_graph']['Sun'] = "Su";

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
$lang['eur'] = "Eurooppa";
$lang['afr'] = "Afrika";
$lang['asi'] = "Aasia";
$lang['ams'] = "Pohjois- ja Keski-Amerikka";
$lang['amn'] = "Pohjois-Amerikka";
$lang['oce'] = "Oseania";

// Oceans
$lang['oc_pac'] = "Pacific Ocean"; // TODO : translate
$lang['oc_atl'] = "Atlantic Ocean"; // TODO : translate
$lang['oc_ind'] = "Indian Ocean"; // TODO : translate

// Countries
$lang['domaines'] = array(
    "xx" => "Tuntematon",
    "ac" => "Ascension Saaret",
    "ad" => "Andorra",
    "ae" => "Yhdistyneet Arabiemiirikunnat",
    "af" => "Afganistan",
    "ag" => "Antigua ja Barbuda",
    "ai" => "Anguilla",
    "al" => "Albania",
    "am" => "Armenia",
    "an" => "Alankomaat",
    "ao" => "Angola",
    "aq" => "Pohjoisnapa",
    "ar" => "Argentiina",
    "as" => "Samoa",
    "at" => "Itävalta",
    "au" => "Australia",
    "aw" => "Aruba",
    "az" => "Azerbaijan",
    "ba" => "Bosnia ja Herzegovina",
    "bb" => "Barbados",
    "bd" => "Bangladesh",
    "be" => "Belgia",
    "bf" => "Burkina Faso",
    "bg" => "Bulgaria",
    "bh" => "Bahrain",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermuda",
    "bn" => "Bruneo",
    "bo" => "Bolivia",
    "br" => "Brasilia",
    "bs" => "Bahama",
    "bt" => "Bhutan",
    "bv" => "Bouvet Saari",
    "bw" => "Botswana",
    "by" => "Belarus",
    "bz" => "Belize",
    "ca" => "Kanada",
    "cc" => "Kookos (Keeling) Saaret",
    "cd" => "Kongon demokraattinen tasavalta",
    "cf" => "Keski-Afrikan tasavalta",
    "cg" => "Kongo",
    "ch" => "Sveitsi",
    "ci" => "Cote D'Ivoire",
    "ck" => "Cook Saaret",
    "cl" => "Chile",
    "cm" => "Kameron",
    "cn" => "Kiina",
    "co" => "Kolumbia",
    "cr" => "Costa Rica",
	"cs" => "Serbia Montenegro",
    "cu" => "Kuuba",
    "cv" => "Cape Verde",
    "cx" => "Christmas saaret",
    "cy" => "Kypros",
    "cz" => "Tsekkien tasavalta",
    "de" => "Saksa",
    "dj" => "Djibouti",
    "dk" => "Tanska",
    "dm" => "Dominica",
    "do" => "Dominikaaninen tasavalta",
    "dz" => "Algeria",
    "ec" => "Ecuador",
    "ee" => "Viro",
    "eg" => "Egypti",
    "eh" => "Läntinen sahrara",
    "er" => "Eritrea",
    "es" => "Espanja",
    "et" => "Etiopia",
    "fi" => "Suomi",
    "fj" => "Fiji",
    "fk" => "Falkland Saaret (Malvinas)",
    "fm" => "Micronesian yhtyneet osavaltiot",
    "fo" => "Faroe Saaret",
    "fr" => "Ranska",
    "ga" => "Gabon",
    "gd" => "Grenada",
    "ge" => "Georgia",
    "gf" => "Ranskan Guyana",
    "gg" => "Guernsey",
    "gh" => "Ghana",
    "gi" => "Gibraltar",
    "gl" => "Gröönlanti",
    "gm" => "Gambia",
    "gn" => "Guinea",
    "gp" => "Guadeloupe",
    "gq" => "Equatorial Guinea",
    "gr" => "Kreikka",
    "gs" => "Pohjois Georgia and the Pohjoiset Sandwich Islands",
    "gt" => "Guatemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard Saari and McDonald Saari",
    "hn" => "Honduras",
    "hr" => "Kroatia",
    "ht" => "Haiti",
    "hu" => "Unkari",
    "id" => "Indonesia",
    "ie" => "Irlanti",
    "il" => "Israel",
    "im" => "Man Saari",
    "in" => "Intia",
    "io" => "Britannian Intian Valtameren Alue",
    "iq" => "Irak",
    "ir" => "Iranin Islamilainen tasavalta",
    "is" => "Islanti",
    "it" => "Italia",
    "je" => "Jersey",
    "jm" => "Jamaica",
    "jo" => "Jordan",
    "jp" => "Japani",
    "ke" => "Kenia",
    "kg" => "Kyrgyzstan",
    "kh" => "Cambodza",
    "ki" => "Kiribati",
    "km" => "Comoros",
    "kn" => "Saint Kitts ja Nevis",
    "kp" => "Korea kansandemokraattinen tasavalta",
    "kr" => "Korea tasavalta",
    "kw" => "Kuwait",
    "ky" => "Cayman Saaret",
    "kz" => "Kazakhstan",
    "la" => "Laos",
    "lb" => "Libanon",
    "lc" => "Saint Lucia",
    "li" => "Liechtenstein",
    "lk" => "Sri Lanka",
    "lr" => "Liberia",
    "ls" => "Lesotho",
    "lt" => "Liettua",
    "lu" => "Luxemburg",
    "lv" => "Latvia",
    "ly" => "Libya",
    "ma" => "Marocco",
    "mc" => "Monaco",
    "md" => "Moldovan tasavalta",
    "mg" => "Madagaskar",
    "mh" => "Marshall Saaret",
    "mk" => "Makedonia",
    "ml" => "Mali",
    "mm" => "Myanmar",
    "mn" => "Mongolia",
    "mo" => "Macau",
    "mp" => "Pohjoiset Mariaanien saaret",
    "mq" => "Martinique",
    "mr" => "Mauritania",
    "ms" => "Montserrat",
    "mt" => "Malta",
    "mu" => "Mauritius",
    "mv" => "Maldives",
    "mw" => "Malawi",
    "mx" => "Mexico",
    "my" => "Malesia",
    "mz" => "Mosambik",
    "na" => "Namibia",
    "nc" => "Uusi Kaledonia",
    "ne" => "Nigeria",
    "nf" => "Norfolk Saari",
    "ng" => "Nigeria",
    "ni" => "Nicaragua",
    "nl" => "Alankomaat",
    "no" => "Norja",
    "np" => "Nepal",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "Uusi seelanti",
    "om" => "Oman",
    "pa" => "Panama",
    "pe" => "Peru",
    "pf" => "Ranskan Polynesia",
    "pg" => "Papua Uusi Guinea",
    "ph" => "Filippiinit",
    "pk" => "Pakistan",
    "pl" => "Puola",
    "pm" => "Saint Pierre ja Miquelon",
    "pn" => "Pitcairn",
    "pr" => "Puerto Rico",
    "pt" => "Portugal",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Qatar",
    "re" => "Reunion Saari",
    "ro" => "Romania",
    "ru" => "Venäjän Liittovalta",
    "rs" => "Venäjä",
    "rw" => "Ruanda",
    "sa" => "Saudi Arabia",
    "sb" => "Solomon saaret",
    "sc" => "Seychellit",
    "sd" => "Sudan",
    "se" => "Ruotsi",
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
    "st" => "Sao Tome ja Principe",
    "su" => "Vanha U.R.S.S.",
    "sv" => "El Salvador",
    "sy" => "Syyrian Arabitasavalta",
    "sz" => "Sveitsi",
    "tc" => "Turks ja Caicos saaret",
    "td" => "Chad",
    "tf" => "Ranskan pohjoisalueet",
    "tg" => "Togo",
    "th" => "Thaimaa",
    "tj" => "Tajikistan",
    "tk" => "Tokelau",
    "tm" => "Turkmenistan",
    "tn" => "Tunisia",
    "to" => "Tonga",
    "tp" => "Itä-Timor",
    "tr" => "Turkki",
    "tt" => "Trinidad ja Tobago",
    "tv" => "Tuvalu",
    "tw" => "Taiwan Kiinan provinssi",
    "tz" => "Tansanian yhdistynyt tasavalta",
    "ua" => "Ukraina",
    "ug" => "Uganda",
    "uk" => "Yhdistyneet kuningaskunnat",
    "gb" => "Iso-Britannia",
    "um" => "Yhdysvaltojen ulkoiset pienet saaret",
    "us" => "Yhdysvallat",
    "uy" => "Uruguay",
    "uz" => "Uzbekistan",
    "va" => "Vatikaanivaltio",
    "vc" => "Saint Vincent ja Grendiinit",
    "ve" => "Venezuela",
    "vg" => "Neitsytsaaret, Brittien",
    "vi" => "Neitsytsaaret, Yhdysvaltojen",
    "vn" => "Vietnam",
    "vu" => "Vanuatu",
    "wf" => "Wallis ja Futuna",
    "ws" => "Samoa",
    "ye" => "Jemen",
    "yt" => "Mayotte",
    "yu" => "Jugoslavia",
    "za" => "Pohjois Afrikka",
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