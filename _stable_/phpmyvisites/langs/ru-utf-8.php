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
$lang['auteur_nom'] = "Иван Коваленко"; // Translator's name
$lang['auteur_email'] = "tronic@php.net"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "ru"; // iso language code
$lang['lang_libelle_en'] = "Russian"; // english language name
$lang['lang_libelle_fr'] = "Russe"; // french language name
$lang['unites_bytes'] = array('б', 'Кб', 'Мб', 'Гб', 'Тб', 'Пб', 'Эб', 'Зб', 'Йб');
$lang['separateur_milliers'] = ' '; // three thousands spells 300,000 in english
$lang['separateur_decimaux'] = ','; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | приложение с открытым исходным кодом для ведения статистики и анализа траффика на веб-сайтах"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, скрипт, приложение, программа, статистика, реферралы, статистика, бесплатный, open source, gpl, визиты, посетители, mysql, просмотр страниц, страницы, просмотры, количество посещений, графики, браузеры, ОС, операционная система, разрешения, день, неделя, месяц, записи, страна, хост, сервис-провайдеры, поисковая машина, ключевые слова, ссылки, графики, страницы входа, страницы выхода, круговые диаграммы"; // Header keywords
$lang['head_description'] = "phpMyVisites | Приложение с открытым исходным кодом для ведения статистики по веб-сайтам, использующее PHP/MySQL и распространяемое на условиях лицензии GNU GPL."; // Header description
$lang['logo_description'] = "phpMyVisites : приложение с открытым исходным кодом для ведения статистики по веб-сайтам, использующее PHP/MySQL и распространяемое на условиях лицензии GPL."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "Визиты";
$lang['menu_pagesvues'] = "Страницы";
$lang['menu_suivi'] = "Пути";
$lang['menu_provenance'] = "Локации";
$lang['menu_configurations'] = "Настройки";
$lang['menu_affluents'] = "Реферралы";
$lang['menu_listesites'] = "Список сайтов";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "День";
$lang['menu_semaine'] = "Неделя";
$lang['menu_mois'] = "Месяц";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Анализируемый период: %s"; // Text formated (e.g.: Studied period: Thuesday, september the 11th)
$lang['liens_siteofficiel'] = "Официальный веб-сайт";
$lang['liens_admin'] = "Администрирование";
$lang['liens_contacts'] = "Контакты";

//
// Divers
//
$lang['generique_nombre'] = "Кол-во";
$lang['generique_tauxsortie'] = "Процент ухода";
$lang['generique_ok'] = "OK";
$lang['generique_timefooter'] = "Страница сгенерирована за %s секунд(ы)"; // Time in seconds
$lang['generique_divers'] = "Другие"; // (for the graphs)
$lang['generique_inconnu'] = "Неизв."; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Другие?"; // Other language, translations wanted
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
$lang['generique_total'] = "Всего";
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
$lang['login_password'] = "пароль : "; // lowercase
$lang['login_login'] = "логин : "; // lowercase
$lang['login_error'] = "Неверный логин или пароль.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Контакты";
$lang['contacts_langue'] = "Переводы";
$lang['contacts_merci'] = "Благодарности";
$lang['contacts_auteur'] = "Автором программы, документации и проекта phpMyVisites является <strong>Матье Обри (Matthieu Aubry)</strong>.";
$lang['contacts_questions'] = "Чтобы <strong>задать вопрос технического характера, сообщить об ошибке, внести предложения</strong>, посетите форум на официальном веб-сайте %s. Информацию другого рода вы можете получить, отправив запрос с помощью специальной формы на официальном веб-сайте"; // adresse du site
$lang['contacts_trad1'] = "Вы хотите перевести phpMyVisites на ваш язык? Пожалуйста, потому что <strong>вы нужны phpMyVisites!</strong>";
$lang['contacts_trad2'] = "Локализация phpMyVisites может занять некоторое время (возможно, несколько часов) и требует хорошего знания языков; помните, что <strong>любая проделанная вами работа будет служить многим пользователям</strong>. Если вы заинтересованы в локализации phpMyVisites, все необходимые сведения вы сможете почерпнуть из %s официальной документации к phpMyVisites %s."; // lien vers la doc
$lang['contacts_doc'] = "Обратитесь к %s официальной документации к phpMyVisites %s, которая содержит сведения по установке, настройке и функционированию phpMyVisites. Документация находится в полученном вами дистрибутиве phpMyVisites."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "Посетите благодарственную страницу на официальном сайте, чтобы ознакомиться с полным списком принимавших участие в разработке или сопровождении phpMyVisites.";
$lang['contacts_merci2'] = "Большое спасибо всем принимавшим участие в локализации phpMyVisites:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Сведения о посетителях"; 
$lang['visites_statistiques'] = "Статистика";
$lang['visites_periodesel'] = "За выбранный период";
$lang['visites_visites'] = "Визитов";
$lang['visites_uniques'] = "Уникальных посетителей";
$lang['visites_pagesvues'] = "Просмотров страниц";
$lang['visites_pagesvisiteurs'] = "Просмотров на посетителя"; 
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Средняя продолжительность визитов";
$lang['visites_tempsmoyenpv'] = "Средняя продолжительность просмотров";
$lang['visites_tauxvisite'] = "Визитов на страницу"; 
$lang['visites_recapperiode'] = "Отчёты за период";
$lang['visites_nbvisites'] = "Визиты";
$lang['visites_aucunevivisite'] = "Нет данных"; // in the table, must be short
$lang['visites_recap'] = "Отчёт";
$lang['visites_unepage'] = "1 страница"; // (graph)
$lang['visites_pages'] = "%s страниц(ы)"; // 1-2 pages (graph)
$lang['visites_min'] = "%s мин"; // 10-15 min (graph)
$lang['visites_sec'] = "%s сек"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "Отчётный график";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "График продолжительности визитов";
$lang['visites_graphtempsvisitesimg'] = "Продолжительность визитов";
$lang['visites_graphheureserveur'] = "График визитов по времени сервера"; 
$lang['visites_graphheureserveurimg'] = "Визиты по времени сервера"; 
$lang['visites_graphheurevisiteur'] = "График визитов по времени посетителей";
$lang['visites_graphheurelocalimg'] = "Визиты по времени посетителей"; 
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
$lang['pagesvues_titre'] = "Сведения о просмотрах страниц";
$lang['pagesvues_joursel'] = "Выбранный день";
$lang['pagesvues_jmoins7'] = "День - 7";
$lang['pagesvues_jmoins14'] = "День - 14";
$lang['pagesvues_moyenne'] = "(сред.)";
$lang['pagesvues_pagesvues'] = "Просмотров страниц";
$lang['pagesvues_pagesvudiff'] = "Уникальных просмотров";
$lang['pagesvues_recordpages'] = "Макс.кол-во просмотров на посетителя";
$lang['pagesvues_tabdetails'] = "Страницы (%s - %s)"; // (de 1   21)
$lang['pagesvues_graphsnbpages'] = "График визитов по кол-ву просмотренных страниц";
$lang['pagesvues_graphnbvisitespageimg'] = "Визиты по кол-ву просмотренных страниц";
$lang['pagesvues_graphheureserveur'] = "График визитов по времени сервера";
$lang['pagesvues_graphheureserveurimg'] = "Визиты по времени сервера";
$lang['pagesvues_graphheurevisiteur'] = "График визитов по времени посетителей";
$lang['pagesvues_graphpageslocalimg'] = "Визиты по времени посетителей";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Пути посетителей";
$lang['suivi_pageentree'] = "Страницы входа";
$lang['suivi_pagesortie'] = "Страницы выхода";
$lang['suivi_tauxsortie'] = "Процент ухода";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Локации посетителей";
$lang['provenance_recappays'] = "Отчёт по странам";
$lang['provenance_pays'] = "Страны";
$lang['provenance_paysimg'] = "Диаграмма стран посетителей";
$lang['provenance_fai'] = "Интернет-провайдеры";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Провайдеры"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Континент";
$lang['provenance_mappemonde'] = "Карта мира";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Настройки посетителей";
$lang['configurations_os'] = "Операционные системы";
$lang['configurations_osimg'] = "График по операционным системам";
$lang['configurations_navigateurs'] = "Браузеры";
$lang['configurations_navigateursimg'] = "График по браузерам";
$lang['configurations_resolutions'] = "Разрешение экрана";
$lang['configurations_resolutionsimg'] = "График по разрешению экрана";
$lang['configurations_couleurs'] = "Глубина цвета";
$lang['configurations_couleursimg'] = "График по глубине цвета";
$lang['configurations_rapport'] = "Нормальный/широкоэкранный";
$lang['configurations_large'] = "Широкоэкранный";
$lang['configurations_normal'] = "Нормальный";
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
$lang['affluents_titre'] = "Реферралы";
$lang['affluents_recapimg'] = "График визитов по реферралам";
$lang['affluents_directimg'] = "Прямые";
$lang['affluents_sitesimg'] = "С веб-сайтов";
$lang['affluents_moteursimg'] = "С поиск.машин";
$lang['affluents_referrersimg'] = "Реферралы";
$lang['affluents_moteurs'] = "Поисковые машины";
$lang['affluents_nbparmoteur'] = "Заходов с поисковых машин : %s";
$lang['affluents_aucunmoteur'] = "Заходов с поисковых машин не было.";
$lang['affluents_motscles'] = "Ключевые слова";
$lang['affluents_nbmotscles'] = "Уникальные ключевые слова : %s";
$lang['affluents_aucunmotscles'] = "Ключевые слова отсутствуют.";
$lang['affluents_sitesinternet'] = "Веб-сайты";
$lang['affluents_nbautressites'] = "Заходов с других веб-сайтов : %s";
$lang['affluents_nbautressitesdiff'] = "Количество веб-сайтов : %s";
$lang['affluents_aucunautresite'] = "Заходов с веб-сайтов не было.";
$lang['affluents_entreedirecte'] = "Прямые заходы";
$lang['affluents_nbentreedirecte'] = "Прямых заходов : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Подробно"; // In the results of the referers array
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
$lang['purge_titre'] = "Отчёт по визитам и реферралам";
$lang['purge_intro'] = "Все данные, кроме существенных, за этот период были удалены.";
$lang['admin_purge'] = "Обслуживание базы данных";
$lang['admin_purgeintro'] = "Этот раздел предназначен для управления таблицами, используемыми в phpMyVisites. Вы можете контролировать объём дискового пространства, используемого таблицами, оптимизировать их или удалить старые данные, что позволит вам ограничить размер таблиц в вашей БД.";
$lang['admin_optimisation'] = "Оптимизируется [ %s ]..."; // Tables names
$lang['admin_postopt'] = "Общий объем уменьшился на %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "Удалить следующие периоды: %s";
$lang['admin_purge_fini'] = "Удаление таблиц завершено...";
$lang['admin_bdd_nom'] = "Имя";
$lang['admin_bdd_enregistrements'] = "Записей";
$lang['admin_bdd_taille'] = "Размер таблицы";
$lang['admin_bdd_opt'] = "Оптимизировать";
$lang['admin_bdd_purge'] = "Критерий очистки";
$lang['admin_bdd_optall'] = "Оптимизировать все";
$lang['admin_purge_j'] = "Удалить записи старше %s дней";
$lang['admin_purge_s'] = "Удалить записи старше %s недель";
$lang['admin_purge_m'] = "Удалить записи старше %s месяцев";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Удалить все журналы";
$lang['admin_purge_autres'] = "Удалить связи в таблице '%s'";
$lang['admin_purge_none'] = "Доступных действий нет";
$lang['admin_purge_cal'] = "Удалить (может занять неск.минут)";
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
$lang['admin_intro'] = "Добро пожаловать в область настроек phpMyVisites. Вы можете изменить любую информацию, относящуюся к установке. Если у вас появятся вопросы, обратитесь к %s официальной документации к phpMyVisites %s."; // link to the doc
$lang['admin_configetperso'] = "Общие сведения";
$lang['admin_afficherjavascript'] = "Получить код JavaScript";
$lang['admin_cookieadmin'] = "Учёт заходов администратора";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Ранее добавленные сайты:";
$lang['admin_retour'] = "Назад";
$lang['admin_cookienavigateur'] = "Вы можете исключить заходы администратора из статистики. Этот метод базируется на cookies. Вы сможете изменить эту настройку в любое время.";
$lang['admin_prendreencompteadmin'] = "Учитывать заходы администратора (cookie будет удален)";
$lang['admin_nepasprendreencompteadmin'] = "Не учитывать заходы администратора (будет создан cookie)";
$lang['admin_etatcookieoui'] = "Для этого веб-сайта ведётся учёт заходов администратора (как для любого другого посетителя - настройка по умолчанию)";
$lang['admin_etatcookienon'] = "Для этого веб-сайта заходы администратора не учитываются (Ваши заходы не будут учтены)";
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
$lang['install_loginmysql'] = "Пользователь базы данных";
$lang['install_mdpmysql'] = "Пароль";
$lang['install_serveurmysql'] = "Сервер баз данных";
$lang['install_basemysql'] = "Имя базы данных";
$lang['install_prefixetable'] = "Префикс имён таблиц";
$lang['install_utilisateursavances'] = "Для опытных пользователей (необязательные параметры)";
$lang['install_oui'] = "Да";
$lang['install_non'] = "Нет";
$lang['install_ok'] = "OK";
$lang['install_probleme'] = "Внимание: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Логин администратора:";
$lang['install_mdpadmin'] = "Пароль администратора:";
$lang['install_chemincomplet'] = "Полный путь к приложению phpMyVisites (например, http://www.mysite.com/rep1/rep3/phpmyvisites/). Путь должен оканчиваться символом <strong>/</strong>.";
$lang['install_afficherlogo'] = "Показывать логотип на ваших страницах? %s <br />By allowing the display of the logo on your site, you will help publicize phpMyVisites and help it evolve more rapidly.  It is also a way to thank the author who has spent many hours developing this Open Source, free application."; // %s replaced by the logo image
$lang['install_affichergraphique'] = "Показывать статистические графики.";
$lang['install_valider'] = "Продолжить"; //  during installation and for login
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
$lang['install_txt2'] = "После завершения установки будет передан запрос на официальный веб-сайт с тем, чтобы мы могли узнать, сколько пользователей установили phpMyVisites. Надеемся, вы отнесётесь к этому с пониманием.";
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
$lang['moistab']['01'] = "Январь";
$lang['moistab']['02'] = "Февраль";
$lang['moistab']['03'] = "Март";
$lang['moistab']['04'] = "Апрель";
$lang['moistab']['05'] = "Май";
$lang['moistab']['06'] = "Июнь";
$lang['moistab']['07'] = "Июль";
$lang['moistab']['08'] = "Август";
$lang['moistab']['09'] = "Сентябрь";
$lang['moistab']['10'] = "Октябрь";
$lang['moistab']['11'] = "Ноябрь";
$lang['moistab']['12'] = "Декабрь";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Янв";
$lang['moistab_graph']['02'] = "Фев";
$lang['moistab_graph']['03'] = "Мар";
$lang['moistab_graph']['04'] = "Апр";
$lang['moistab_graph']['05'] = "Май";
$lang['moistab_graph']['06'] = "Июн";
$lang['moistab_graph']['07'] = "Июл";
$lang['moistab_graph']['08'] = "Авг";
$lang['moistab_graph']['09'] = "Сен";
$lang['moistab_graph']['10'] = "Окт";
$lang['moistab_graph']['11'] = "Ноя";
$lang['moistab_graph']['12'] = "Дек";

// Day of the week
$lang['jsemaine']['Mon'] = "Понедельник";
$lang['jsemaine']['Tue'] = "Вторник";
$lang['jsemaine']['Wed'] = "Среда";
$lang['jsemaine']['Thu'] = "Четверг";
$lang['jsemaine']['Fri'] = "Пятница";
$lang['jsemaine']['Sat'] = "Суббота";
$lang['jsemaine']['Sun'] = "Воскресенье";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Пон";
$lang['jsemaine_graph']['Tue'] = "Вто";
$lang['jsemaine_graph']['Wed'] = "Сре";
$lang['jsemaine_graph']['Thu'] = "Чет";
$lang['jsemaine_graph']['Fri'] = "Пят";
$lang['jsemaine_graph']['Sat'] = "Суб";
$lang['jsemaine_graph']['Sun'] = "Вос";

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
$lang['eur'] = "Европа";
$lang['afr'] = "Африка";
$lang['asi'] = "Азия";
$lang['ams'] = "Южная и Центральная Америки";
$lang['amn'] = "Северная Америка";
$lang['oce'] = "Океания";

// Oceans
$lang['oc_pac'] = "Pacific Ocean"; // TODO : translate
$lang['oc_atl'] = "Atlantic Ocean"; // TODO : translate
$lang['oc_ind'] = "Indian Ocean"; // TODO : translate

// Countries
$lang['domaines'] = array(
    "xx" => "Неизв.",
    "ac" => "О-ва Асенсьон",
    "ad" => "Андора",
    "ae" => "ОАЭ",
    "af" => "Афганистан",
    "ag" => "Антигуа и Барбуда",
    "ai" => "Ангвила",
    "al" => "Албания",
    "am" => "Армения",
    "an" => "Антильские о-ва",
    "ao" => "Ангола",
    "aq" => "Антарктика",
    "ar" => "Аргентина",
    "as" => "Американское Самоа",
    "at" => "Австрия",
    "au" => "Австралия",
    "aw" => "Аруба",
    "az" => "Азербайджан",
    "ba" => "Босния и Герцеговина",
    "bb" => "Барбадос",
    "bd" => "Бангладеш",
    "be" => "Бельгия",
    "bf" => "Буркина Фасо",
    "bg" => "Болгария",
    "bh" => "Бахрейн",
    "bi" => "Бурунди",
    "bj" => "Бенин",
    "bm" => "Бермудские о-ва",
    "bn" => "Бруней",
    "bo" => "Боливия",
    "br" => "Бразилия",
    "bs" => "Багамы",
    "bt" => "Бутан",
    "bv" => "О-в Буве",
    "bw" => "Бодсвана",
    "by" => "Беларусь",
    "bz" => "Белиз",
    "ca" => "Канада",
    "cc" => "Кокосовые (Килинга) о-ва",
    "cd" => "Конго, демократическая республика",
    "cf" => "Центральная Африканская республика",
    "cg" => "Конго",
    "ch" => "Швейцария",
    "ci" => "Кот Д'ивуар",
    "ck" => "О-ва Кука",
    "cl" => "Чили",
    "cm" => "Камерун",
    "cn" => "Китай",
    "co" => "Колумбия",
    "cr" => "Коста Рика",
	"cs" => "Serbia Montenegro",
    "cu" => "Куба",
    "cv" => "Капе Верде",
    "cx" => "О-в Рождества",
    "cy" => "Кипр",
    "cz" => "Чешская республика",
    "de" => "Германия",
    "dj" => "джибути",
    "dk" => "Дания",
    "dm" => "Доминик",
    "do" => "Доминиканская республика",
    "dz" => "Алжир",
    "ec" => "Эквадор",
    "ee" => "Эстония",
    "eg" => "Египет",
    "eh" => "Западная Сахара",
    "er" => "Эритрея",
    "es" => "Испания",
    "et" => "Эфиопия",
    "fi" => "Финляндия",
    "fj" => "Фиджи",
    "fk" => "Фольклендские о-ва",
    "fm" => "Микронезия",
    "fo" => "О-ва Фаро",
    "fr" => "Франция",
    "ga" => "Габон",
    "gd" => "Гренада",
    "ge" => "Грузия",
    "gf" => "Гуана",
    "gg" => "Джернси",
    "gh" => "Гнаа",
    "gi" => "Гибралтар",
    "gl" => "Гренладния",
    "gm" => "Гамбия",
    "gn" => "Гвинея",
    "gp" => "Гваделупа",
    "gq" => "Экваториальная Гвинея",
    "gr" => "Греция",
    "gs" => "Южная Грузия",
    "gt" => "Гватемала",
    "gu" => "Гуам",
    "gw" => "Гвинея-Диссо",
    "gy" => "Гуйана",
    "hk" => "Гонконг",
    "hm" => "О-ва Херда и Макдональда",
    "hn" => "Гондурас",
    "hr" => "Хорватия",
    "ht" => "Гаити",
    "hu" => "Венгрия",
    "id" => "Индонезия",
    "ie" => "Ирландия",
    "il" => "Израиль",
    "im" => "О-в Мэн",
    "in" => "Индия",
    "io" => "Британская территория Индийского океана",
    "iq" => "Ирак",
    "ir" => "Иран",
    "is" => "исландия",
    "it" => "Италия",
    "je" => "Джерси",
    "jm" => "Ямайка",
    "jo" => "Иордания",
    "jp" => "Япония",
    "ke" => "Кения",
    "kg" => "Кыргызстан",
    "kh" => "Камбоджа",
    "ki" => "Кирибати",
    "km" => "Коморос",
    "kn" => "Св.Киттс и Невис",
    "kp" => "КНДР",
    "kr" => "Корея",
    "kw" => "Кувейт",
    "ky" => "Каймановы о-ва",
    "kz" => "Казахстан",
    "la" => "Лаос",
    "lb" => "Ливан",
    "lc" => "Санталючия",
    "li" => "Лихтенштейн",
    "lk" => "Шри Ланка",
    "lr" => "Либерия",
    "ls" => "Лесото",
    "lt" => "Литва",
    "lu" => "люксембург",
    "lv" => "Латвия",
    "ly" => "Ливия",
    "ma" => "Марокко",
    "mc" => "Монако",
    "md" => "Молдова",
    "mg" => "Мадагаскар",
    "mh" => "Маршалловы о-ва",
    "mk" => "Македония",
    "ml" => "Мали",
    "mm" => "Мьянма",
    "mn" => "Монголия",
    "mo" => "Макау",
    "mp" => "Северные о-ва Марьяна",
    "mq" => "Мартиник",
    "mr" => "Мавритания",
    "ms" => "Монсеррат",
    "mt" => "Мальта",
    "mu" => "Маврикий",
    "mv" => "Мальдивы",
    "mw" => "Малави",
    "mx" => "Мексика",
    "my" => "Малайзия",
    "mz" => "Мозамбик",
    "na" => "Намибия",
    "nc" => "Новая Каледония",
    "ne" => "Нигер",
    "nf" => "Норфолкские о-ва",
    "ng" => "Нигерия",
    "ni" => "Никарагуа",
    "nl" => "Нидерланды",
    "no" => "Норвегия",
    "np" => "Непал",
    "nr" => "науру",
    "nu" => "Ние",
    "nz" => "Ноавя Зеландия",
    "om" => "Оман",
    "pa" => "Панама",
    "pe" => "Перу",
    "pf" => "Полинезия",
    "pg" => "Папуа Новая Гвинея",
    "ph" => "Филиппины",
    "pk" => "Пакистан",
    "pl" => "Польша",
    "pm" => "Сен-Пьер и Микелон",
    "pn" => "Питкерн",
    "pr" => "Пуэрто-Рико",
    "pt" => "Португалия",
    "pw" => "Пало",
    "py" => "Парагвай",
    "qa" => "Катар",
    "re" => "О-ва Реюнион",
    "ro" => "Румыния",
    "ru" => "Российская Федерация",
    "rs" => "Россия",
    "rw" => "Руанда",
    "sa" => "Саудовская Аравия",
    "sb" => "Соломоновы о-ва",
    "sc" => "Сейшелы",
    "sd" => "Судан",
    "se" => "Швеция",
    "sg" => "Сингапур",
    "sh" => "О-в св.Елены",
    "si" => "Словения",
    "sj" => "Свольбар",
    "sk" => "Словакия",
    "sl" => "Сиерра Леоне",
    "sm" => "Сан Марино",
    "sn" => "Сенегал",
    "so" => "Сомали",
    "sr" => "Суринам",
    "st" => "Сан Томе и Принсип",
    "su" => "СССР (бывш.)",
    "sv" => "Эль Сальвадор",
    "sy" => "Сирия",
    "sz" => "швейцария",
    "tc" => "О-ва Турции и Каикоса",
    "td" => "Чад",
    "tf" => "Южные территории Франции",
    "tg" => "Того",
    "th" => "Таиланд",
    "tj" => "Таджикистан",
    "tk" => "Токело",
    "tm" => "Туркменистан",
    "tn" => "Тунис",
    "to" => "Тонго",
    "tp" => "Вост.Тимор",
    "tr" => "Турция",
    "tt" => "Тринидад и Тобаго",
    "tv" => "Тувалу",
    "tw" => "Тайвань",
    "tz" => "Танзания",
    "ua" => "Украина",
    "ug" => "Уганда",
    "uk" => "Соединённое Королевство",
    "gb" => "Великобритания",
    "um" => "О-ва США",
    "us" => "США",
    "uy" => "Уругвай",
    "uz" => "Узбекистан",
    "va" => "Ватикан",
    "vc" => "Вне Винсент",
    "ve" => "Венесуэла",
    "vg" => "Виржинские о-ва, Великобритания",
    "vi" => "Виржинские о-ва, США",
    "vn" => "Вьетнам",
    "vu" => "Вануату",
    "wf" => "Уоллис и Футуна",
    "ws" => "Самоа",
    "ye" => "Йемен",
    "yt" => "Майотта",
    "yu" => "Югославия",
    "za" => "Южная Африка",
    "zm" => "Замбия",
    "zr" => "Заир",
    "zw" => "Зимбабве",
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
    "tv" => "Тувалу",
    "ws" => "Самоа",
);
?>