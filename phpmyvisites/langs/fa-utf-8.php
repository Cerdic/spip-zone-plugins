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
$lang['auteur_nom'] = "ahmad mohamady nasab"; // Translator's name
$lang['auteur_email'] = "mohamadynasab@gmail.com"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "rtl"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "fa"; // iso language code
$lang['lang_libelle_en'] = "Persian"; // english language name
$lang['lang_libelle_fr'] = "Perse"; // french language name
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
$lang['separateur_milliers'] = ''; // three thousand spells 3,000 in english
$lang['separateur_decimaux'] = '.'; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | برنامه ای کد باز برای تجزیه و تحلیل ترافیک سایت"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, script, application, software, statistics, referals, stats, free, open source, gpl, visits, visitors, mysql, viewed pages, pages, views, number of visits, graphs, Browsers, os, operating system, resolutions, day, week, month, records, country, host, service providors, search enginge, key words, referrers, graphs, entry pages, exit pages, pie charts"; // Header keywords
$lang['head_description'] = "phpMyVisites | برنامه ای که توسط php /My SQL نوشته شده و تحت قانون GPL قرار دارد.."; // Header description
$lang['logo_description'] = "phpMyVisites : برنامه ای که توسط php /My SQL نوشته شده و تحت قانون GPL قرار دارد."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "بازدید کنندگان";
$lang['menu_pagesvues'] = "صفحات بازدید شده";
$lang['menu_suivi'] = "پی گیری کردن";
$lang['menu_provenance'] = "منبع";
$lang['menu_configurations'] = "تنظیمات";
$lang['menu_affluents'] = "مراجعه کنندگان";
$lang['menu_listesites'] = "List Sites";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "روز";
$lang['menu_semaine'] = "هفته";
$lang['menu_mois'] = "ماه";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "دوره مطالعاتی: %s"; // Text formatted (e.g.: Studied period: Sunday, July the 14th)
$lang['liens_siteofficiel'] = "سایت برنامه";
$lang['liens_admin'] = "کنترل پانل برنامه";
$lang['liens_contacts'] = "ارتباطات";

//
// Divers
//
$lang['generique_nombre'] = "شماره";
$lang['generique_tauxsortie'] = "زمان خروج";
$lang['generique_ok'] = "قبول";
$lang['generique_timefooter'] = "مدت زمان ایجاد صفحه در %s ثانیه"; // Time in seconds
$lang['generique_divers'] = "سایر"; // (for the graphs)
$lang['generique_inconnu'] = "ناشناخته"; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "سایر؟"; // Other language, translations wanted
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
$lang['generique_total'] = "کل";
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
$lang['login_password'] = "رمز ورود : "; // lowercase
$lang['login_login'] = "نام کاربری : "; // lowercase
$lang['login_error'] = "قادر به ورود نمی باشد. رمز ورود اشتباه است.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "ارتباطات";
$lang['contacts_langue'] = "ترجمه";
$lang['contacts_merci'] = "تشکر";
$lang['contacts_auteur'] = "نویسنده و مستند ساز و ایجاد کننده برنامه  <strong>Matthieu Aubry</strong> می باشد .";
$lang['contacts_questions'] = "برای<strong> پرسش سوالات, گزارش اشکالات, پیشنهادات</strong> از تالارهای گفتگوی سایت دیدن کنید %s. برای سایر درخواست ها, با نویسنده برنامه از طریق لینک سایت اصلی برنامه ارتباط برقرار کنید.<br>ضمنا با مترجم برنامه می توانید از طریق جدول زیر و انتخاب نام مترجم ارتباط برقرار کنید."; // adresse du site
$lang['contacts_trad1'] = "آیا می خواهید برنامه را به زبان خود ترجمه کنید.در این کار تامل نکنید. زیرا phpmyvisit به این کار شما نیاز فراوان دارد.";
$lang['contacts_trad2'] = "اگر شما علاقمن به ترجمه برنامه دارید اطلاعاتی را می توانید از طریق این لینک کسب کنید %s the official documentation of phpMyVisites %s."; // lien vers la doc
$lang['contacts_doc'] = "به آدرس %s the official documentation of phpMyVisites %s مراجعه کنید تا اطلاعاتی را در مورد نصب و پیکر بندی و یا هر سوال دیگری و همچنین در مورد نسخه برنامه کسب کنید."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "در کمک و همفکری با سایت phpmyvisit تامل نکنید. لیستی از دوستان همکار ما را در زیر مشاهده می کنید.";
$lang['contacts_merci2'] = "یک تشکر ویژه از کلیه کسانی که برنامه را به زبان های زیر ترجمه کرده اند:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "اطلاعات بازدید کنندگان"; 
$lang['visites_statistiques'] = "آمار ها";
$lang['visites_periodesel'] = "برای دوره زمانی انتخاب شده";
$lang['visites_visites'] = "بازدید کنندگان";
$lang['visites_uniques'] = "بازدید کنندگان منحصر بفرد";
$lang['visites_pagesvues'] = "صفحات دیده شده";
$lang['visites_pagesvisiteurs'] = "درصد صفحات بازدید شده"; 
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "میانگین دوره بازدید";
$lang['visites_tempsmoyenpv'] = "مینگین زمان برای صفحه دیده شده";
$lang['visites_tauxvisite'] = "1 page visit rate"; 
$lang['visites_recapperiode'] = "خلاصه دوره";
$lang['visites_nbvisites'] = "بازدید کنندگان";
$lang['visites_aucunevivisite'] = "بدون بازدید کننده"; // in the table, must be short
$lang['visites_recap'] = "خلاصه";
$lang['visites_unepage'] = "1 صفحه"; // (graph)
$lang['visites_pages'] = "%s صفحات"; // 1-2 pages (graph)
$lang['visites_min'] = "%s دقیقه"; // 10-15 min (graph)
$lang['visites_sec'] = "%s ثانیه"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "نمودار برای نمایش خلاصه آمارها";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "نمودار برای نمایش مدت زمانی که توسط بازدید کننده بازدید شده";
$lang['visites_graphtempsvisitesimg'] = "مدت زمان ملاقات ها توسط بازدید کننده";
$lang['visites_graphheureserveur'] = "نمودار برای نمایش درصد ساعات بازدید در این سرور"; 
$lang['visites_graphheureserveurimg'] = "بازدیدها توسط زمان سرور"; 
$lang['visites_graphheurevisiteur'] = "نمودار برای نمایش درصد ساعات بازدید برای این بازدید کننده";
$lang['visites_graphheurelocalimg'] = "بازدید ها توسط زمان محلی"; 
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
$lang['pagesvues_titre'] = "اطلاعات صفحات بازدید شده";
$lang['pagesvues_joursel'] = "روز انتخاب شده";
$lang['pagesvues_jmoins7'] = "روز - 7";
$lang['pagesvues_jmoins14'] = "روز - 14";
$lang['pagesvues_moyenne'] = "(میانگین)";
$lang['pagesvues_pagesvues'] = "بازدید های صفحه";
$lang['pagesvues_pagesvudiff'] = "بازدید های منحصر بفرد صفحه";
$lang['pagesvues_recordpages'] = "بالاترین تعداد صفحات برای یک بازدید کننده";
$lang['pagesvues_tabdetails'] = "صفحه بازدید شده %s به %s"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "نمودار برای نمایش تعداد درصد صفحات بازدید شده";
$lang['pagesvues_graphnbvisitespageimg'] = "بازدید توسط تعداد صفحات بازدید شده";
$lang['pagesvues_graphheureserveur'] = "نمایش نمودار برای بازدیدها توسط زمان سرور";
$lang['pagesvues_graphheureserveurimg'] = "بازدیدها بوسیله زمان محلی";
$lang['pagesvues_graphheurevisiteur'] = "نمودار برای نمایش بازدیدها توسط زمان محلی";
$lang['pagesvues_graphpageslocalimg'] = "بازدید ها توسط زمان محلی";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "حرکات بازدید کنندگان";
$lang['suivi_pageentree'] = "ورودی صفحات";
$lang['suivi_pagesortie'] = "خروج صفحات";
$lang['suivi_tauxsortie'] = "زمان خروج";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "منشاء بازدید کنندگان";
$lang['provenance_recappays'] = "وضعیت کشورها";
$lang['provenance_pays'] = "کشور ها";
$lang['provenance_paysimg'] = "نمودار بازدید کنندگان بر اساس کشور";
$lang['provenance_fai'] = "ISP";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "ارائه دهنده خدمات اینترنتی"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "قاره";
$lang['provenance_mappemonde'] = "نقشه جهان";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "تنظیمات بازدید کننده";
$lang['configurations_os'] = "سیستم عامل ها";
$lang['configurations_osimg'] = "نمودار برای نمایش سیستم عامل بازدید کنندگان";
$lang['configurations_navigateurs'] = "مرورگرها";
$lang['configurations_navigateursimg'] = "نمودار برای نمایش مرورگر بازدید کنندگان";
$lang['configurations_resolutions'] = "وضوح دید مانیتور بازدید کنندگان";
$lang['configurations_resolutionsimg'] = "نمودار برای نمایش وضوح دید مانیتور بازدید کنندگان";
$lang['configurations_couleurs'] = "عمق رنگ مانیتور";
$lang['configurations_couleursimg'] = "نمودار برای نمایش عمق رنگ مانیتور کاربر";
$lang['configurations_rapport'] = "نرمال / پهن";
$lang['configurations_large'] = "پهن";
$lang['configurations_normal'] = "نرکال";
$lang['configurations_double'] = "Dual Screen";
$lang['configurations_plugins'] = "پلاگین";
$lang['configurations_navigateursbytype'] = "مرورگرها براساس نوع";
$lang['configurations_navigateursbytypeimg'] = "نمودار برای نمایش نوع مرورگرها";
$lang['configurations_os_interest'] = "Operating Systems Interest";
$lang['configurations_navigateurs_interest'] = "Browsers Interest";
$lang['configurations_resolutions_interest'] = "Screen Resolutions Interest";
$lang['configurations_couleurs_interest'] = "Color Depth Interest";
$lang['configurations_configurations'] = "Top settings";

//
// Referers
//
$lang['affluents_titre'] = "مراحعه کنندگان";
$lang['affluents_recapimg'] = "نمودار بازدید کنندگان بر اساس مراجعه";
$lang['affluents_directimg'] = "مستقیم";
$lang['affluents_sitesimg'] = "سایت ها";
$lang['affluents_moteursimg'] = "موتور جستجو ها";
$lang['affluents_referrersimg'] = "مراجعه کنندگان";
$lang['affluents_moteurs'] = "موتورهای حرفه ای جستجو";
$lang['affluents_nbparmoteur'] = "مدت زمان بازدید ها توسط موتور جستجو : %s";
$lang['affluents_aucunmoteur'] = "هیچ بهبودی توسط موتور جستجو داده نشده.";
$lang['affluents_motscles'] = "کلمات کلیدی";
$lang['affluents_nbmotscles'] = "کلملت کلیدی مشخص : %s";
$lang['affluents_aucunmotscles'] = "کلمه کلیدی پیدا نشد.";
$lang['affluents_sitesinternet'] = "سایت ها";
$lang['affluents_nbautressites'] = "بهبود بازدید ها توسط سایر سایت ها : %s";
$lang['affluents_nbautressitesdiff'] = "تعداد سایت های متفاوت : %s";
$lang['affluents_aucunautresite'] = "هیچ بهبودی توسط سایت ها داده نشده.";
$lang['affluents_entreedirecte'] = "درخواست مستقیم";
$lang['affluents_nbentreedirecte'] = "بازدید های درخواست مستقیم : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "جزئیات"; // In the results of the referers array
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
$lang['purge_titre'] = "خلاصه بازدید ها و ارجاعات";
$lang['purge_intro'] = "این دوره از طریق مدیریت حذف شده. فقط آمار ضروری نگه داشته شده است.";
$lang['admin_purge'] = "نگهداری بانک اطلاعاتی";
$lang['admin_purgeintro'] = "این بخش این اجازه را به شما می دهد که جدول های مورد استفاده توسط برنامه را مدیریت کنید. شما می توانید جدول ها را حذف و یا بهینه و یا فضای مورد استفاده توسط آنها را تعیین کنید. همچنین می توانید یادداشت های قدیمی را حذف کنید.";
$lang['admin_optimisation'] = "بهینه کردن [ %s ]..."; // Tables names
$lang['admin_postopt'] = "کل فضا کاهش داده شده بوسیله %chiffres% %unites%"; // 28 Kb
$lang['admin_purgeres'] = "حذف دوره های زیر: %s";
$lang['admin_purge_fini'] = "حذف جدول ها پایان یافت...";
$lang['admin_bdd_nom'] = "نام";
$lang['admin_bdd_enregistrements'] = "ثبت شده ها";
$lang['admin_bdd_taille'] = "اندازه جدول";
$lang['admin_bdd_opt'] = "بهینه سازی";
$lang['admin_bdd_purge'] = "پاک کردن محتوی";
$lang['admin_bdd_optall'] = "بهینه سازی همه";
$lang['admin_purge_j'] = "حذف  محتوی جدول ها از %s روز";
$lang['admin_purge_s'] = "حذف محتوی جدول ها از %s هفته";
$lang['admin_purge_m'] = "حذف مختوی جدول ها از %s ماه";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "حذف تمام گزارشات";
$lang['admin_purge_autres'] = "پاک کردن معمولی  جدول '%s'";
$lang['admin_purge_none'] = "انجام عمل امکان ندارد";
$lang['admin_purge_cal'] = "محاسبه و پاک کردن (چند دقیقه می تواند طول بکشد)";
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
$lang['admin_intro'] = "به حوزه پیکر بندی برنامه phpmyvisit خوش آمدید . شما می توانید تنظیمات مربوط به نصب و غیره را در این بخش انجام دهیدو در صورت داشتن هر گونه مشکلی به این لینک مراجعه کنید. %s the official documentation of phpMyVisites %s."; // link to the doc
$lang['admin_configetperso'] = "تنظیمات کلی";
$lang['admin_afficherjavascript'] = "نمایش کد آمار با جاوا اسکریپت";
$lang['admin_cookieadmin'] = "بازدید مدیریت از این صفحات در شمارش به حساب نمی آید.";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "سایت های ثبت شده:";
$lang['admin_retour'] = "بازگشت";
$lang['admin_cookienavigateur'] = "شما ممکن است که مدیریت را نخواهید در شمارش ها به حساب آورید. از طریق این گزینه می توانید آن را تنظیم کنید. این کار توسط کوکی ها و از طریق محتوای مرورگر تشخیص داده می شود و شما هر زمان که بخواهید می توانید آن را حذف کنید.";
$lang['admin_prendreencompteadmin'] = "به حساب آوردن مدیریت در شمارش ها (حذف کوکی ها)";
$lang['admin_nepasprendreencompteadmin'] = "عدم شمارش بازدید مدیریت (ایجاد یک کوکی)";
$lang['admin_etatcookieoui'] = "در حال حاضر بازدید مدیریت در شمارش ها به حساب می آید. شما می توانید برای جلوگیری از این کار از طریق لینک زیر عمل کنید.";
$lang['admin_etatcookienon'] = "در حال حاضر بازدید مدیریت در شمارش ها به حساب نمی آید. برای جلوگیری از این کار می توانید از طریق لینک زیر عمل کنید.";
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
$lang['install_loginmysql'] = "نام کاربر بانک اطلاعاتی";
$lang['install_mdpmysql'] = "رمز ورود بانک اطلاعاتی";
$lang['install_serveurmysql'] = "سرور بانک اطلاعاتی";
$lang['install_basemysql'] = "نام بانک اطلاعاتی";
$lang['install_prefixetable'] = "پیشوند جدول ها";
$lang['install_utilisateursavances'] = "(optionalکاربر پیشرفته )";
$lang['install_oui'] = "بله";
$lang['install_non'] = "خیر";
$lang['install_ok'] = "قبول";
$lang['install_probleme'] = "مشکل: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "نام کاربری:";
$lang['install_mdpadmin'] = "رمز ورود:";
$lang['install_chemincomplet'] = "مسیر برنامه را مشخص کنید http://www.mysite.com/rep1/rep3/phpmyvisites/  مثال <br>در پایان آن علامت / را قرار دهید";
$lang['install_afficherlogo'] = "این لوگو در صفحات شما نشان داده می شود %s <br />"; // %s replaced by the logo image
$lang['install_affichergraphique'] = "نمایش نمودارهای آماری";
$lang['install_valider'] = "قبول"; //  during installation and for login
$lang['install_popup_logo'] = "انتخاب یک لوگو";
$lang['install_logodispo'] = "....لوگوهای بیشتر ";
$lang['install_welcome'] = "Welcome!";
$lang['install_system_requirements'] = "System Requirements";
$lang['install_database_setup'] = "Database Setup";
$lang['install_create_tables'] = "Table creation";
$lang['install_general_setup'] = "General Setup";
$lang['install_create_config_file'] = "Create Config File";
$lang['install_first_website_setup'] = "Add First Website";
$lang['install_display_javascript_code'] = "Display Javascript code";
$lang['install_finish'] = "Finished!";
$lang['install_txt2'] = "در پایان نصب درخواستی مبنی بر کمک به  ما در ارائه بهتر برنامه برای استفاده کننگان فرستاده می شود.";
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
$lang['update_jschange'] = "خطر! <br /> کد جاوا اسکریپت phpMyVisites ویرایش شده است. شما باید صفحاتتان را بروزرسانی کنید و کد جاوا اسکریپت جدید را برای تمام سایت های پیکربندی شده copy/paste نمایید. <br />";

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
$lang['moistab']['01'] = "ژانویه";
$lang['moistab']['02'] = "فوریه";
$lang['moistab']['03'] = "مارس";
$lang['moistab']['04'] = "آوریل";
$lang['moistab']['05'] = "می";
$lang['moistab']['06'] = "ژوئن";
$lang['moistab']['07'] = "جولای";
$lang['moistab']['08'] = "آگوست";
$lang['moistab']['09'] = "سپتامبر";
$lang['moistab']['10'] = "اکتبر";
$lang['moistab']['11'] = "نوامبر";
$lang['moistab']['12'] = "دسامبر";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "ژانویه";
$lang['moistab_graph']['02'] = "فوریه";
$lang['moistab_graph']['03'] = "مارس";
$lang['moistab_graph']['04'] = "آوریل";
$lang['moistab_graph']['05'] = "می";
$lang['moistab_graph']['06'] = "ژوئن";
$lang['moistab_graph']['07'] = "جولای";
$lang['moistab_graph']['08'] = "آگوست";
$lang['moistab_graph']['09'] = "سپتامبر";
$lang['moistab_graph']['10'] = "اکتبر";
$lang['moistab_graph']['11'] = "نوامبر";
$lang['moistab_graph']['12'] = "دسامبر";

// Day of the week
$lang['jsemaine']['Mon'] = "دوشنبه";
$lang['jsemaine']['Tue'] = "سه شنبه";
$lang['jsemaine']['Wed'] = "چهارشنبه";
$lang['jsemaine']['Thu'] = "پنجشنبه";
$lang['jsemaine']['Fri'] = "جمعه";
$lang['jsemaine']['Sat'] = "شنبه";
$lang['jsemaine']['Sun'] = "یکشنبه";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "دوشنبه";
$lang['jsemaine_graph']['Tue'] = "سه شنبه";
$lang['jsemaine_graph']['Wed'] = "چهارشنبه";
$lang['jsemaine_graph']['Thu'] = "پنجشنبه";
$lang['jsemaine_graph']['Fri'] = "جمعه";
$lang['jsemaine_graph']['Sat'] = "شنبه";
$lang['jsemaine_graph']['Sun'] = "یکشنبه";

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
$lang['eur'] = "اروپا";
$lang['afr'] = "آفریقا";
$lang['asi'] = "آسیا";
$lang['ams'] = "امریکای مرکزی و جنوبی";
$lang['amn'] = "امریکای شمالی";
$lang['oce'] = "اقیانوسیه";

// Oceans
$lang['oc_pac'] = "اقیانوس آرام";
$lang['oc_atl'] = "اقیانوس اطلس";
$lang['oc_ind'] = "اقیانوس هند";

// Countries
$lang['domaines'] = array(
    "xx" => "ناشناخته",
    "ac" => "جزیره عیسی",
    "ad" => "آندورا",
    "ae" => "امارات متحده عربی",
    "af" => "افغانستان",
    "ag" => "Antigua and Barbuda",
    "ai" => "Anguilla",
    "al" => "آلبانی",
    "am" => "ارمنستان",
    "an" => "هلند",
    "ao" => "آنگولا",
    "aq" => "Antarctica",
    "ar" => "آرژانتین",
    "as" => "آمریکا",
    "at" => "اطریش",
    "au" => "استرالیا",
    "aw" => "Aruba",
    "az" => "آذزبایجان",
    "ba" => "بوسنی و هرزگوین",
    "bb" => "باربادوس",
    "bd" => "بنگلادش",
    "be" => "بلژیک",
    "bf" => "بورکینا فاسو",
    "bg" => "بلغارستان",
    "bh" => "بحرین",
    "bi" => "بروندی",
    "bj" => "Benin",
    "bm" => "برمودا",
    "bn" => "Bruneo",
    "bo" => "بولیوی",
    "br" => "برزیل",
    "bs" => "باهاماس",
    "bt" => "Bhutan",
    "bv" => "Bouvet Island",
    "bw" => "یوتسوانا",
    "by" => "روسیه سفید",
    "bz" => "Belize",
    "ca" => "کانادا",
    "cc" => "کاکاس",
    "cd" => "کنگو",
    "cf" => "جمهوری افریقای مرکزی",
    "cg" => "کنگو",
    "ch" => "سوئیس",
    "ci" => "Cote D'Ivoire",
    "ck" => "جزایر کوک",
    "cl" => "شیبی",
    "cm" => "کامرون",
    "cn" => "چین",
    "co" => "کلمبیا",
    "cr" => "کاستاریکا",
	"cs" => "Serbia Montenegro",
    "cu" => "کوبا",
    "cv" => "دماغه وردا",
    "cx" => "جزایر عید نوروز",
    "cy" => "قبرس",
    "cz" => "چکسلواکی",
    "de" => "آلمان",
    "dj" => "دیجیبوتی",
    "dk" => "دانمارک",
    "dm" => "دومینیک",
    "do" => "جمهوری دومینیک",
    "dz" => "الجزایر",
    "ec" => "اکوادور",
    "ee" => "استونی",
    "eg" => "مصر",
    "eh" => "صحرای غربی",
    "er" => "ادیتره",
    "es" => "اسپانیا",
    "et" => "اتیوپی",
    "fi" => "فنلاند",
    "fj" => "فیجی",
    "fk" => "Falkland Islands (Malvinas)",
    "fm" => "Micronesia, Federated States of",
    "fo" => "جزیره فارائو",
    "fr" => "فرانسه",
    "ga" => "گابن",
    "gd" => "Grenada",
    "ge" => "گرجستان",
    "gf" => "گوین",
    "gg" => "Guernsey",
    "gh" => "غنا",
    "gi" => "جبل طارق",
    "gl" => "گرینلند",
    "gm" => "گامبیا",
    "gn" => "گینه",
    "gp" => "گوادلوپ",
    "gq" => "گینه استوایی",
    "gr" => "یونان",
    "gs" => "گرجستان جنوبی",
    "gt" => "گواتمالا",
    "gu" => "گوام",
    "gw" => "Guinea-Bissau",
    "gy" => "گویانا",
    "hk" => "هنگ کنگ",
    "hm" => "جزیره مک دونالد",
    "hn" => "هندوراس",
    "hr" => "کرواسی",
    "ht" => "هاییتی",
    "hu" => "هنگاری",
    "id" => "اندونزی",
    "ie" => "ایرلند",
    "il" => "اسرائیل",
    "im" => "Man Island",
    "in" => "هند",
    "io" => "سرزمین اقیاونسی هند",
    "iq" => "عراق",
    "ir" => "جمهوری اسلامی ایران",
    "is" => "ایسلند",
    "it" => "ایتالیا",
    "je" => "جرسی",
    "jm" => "جامائیکا",
    "jo" => "اردن",
    "jp" => "ژاپن",
    "ke" => "کنیا",
    "kg" => "قرقیزستان",
    "kh" => "کامبودیا",
    "ki" => "کیریباتی",
    "km" => "کامرون",
    "kn" => "Saint Kitts and Nevis",
    "kp" => "جمهوری دموکراتیک کره",
    "kr" => "جمهوری کره",
    "kw" => "کویت",
    "ky" => "جزیره سوسمار",
    "kz" => "قزاقستان",
    "la" => "لائوس",
    "lb" => "لبنان",
    "lc" => "Saint Lucia",
    "li" => "Liechtenstein",
    "lk" => "سریلانکا",
    "lr" => "لیبری",
    "ls" => "لوسوتو",
    "lt" => "لیتوانی",
    "lu" => "لوکزامبورگ",
    "lv" => "لتویا",
    "ly" => "لیبی",
    "ma" => "مراکش",
    "mc" => "موناکو",
    "md" => "مولدوا",
    "mg" => "مادگاسکار",
    "mh" => "جزایر مارشال",
    "mk" => "مقدونیه",
    "ml" => "مالی",
    "mm" => "میانمار",
    "mn" => "مغولستان",
    "mo" => "ماکائو",
    "mp" => "جزایر مارینای شمالی",
    "mq" => "Martinique",
    "mr" => "موریتانی",
    "ms" => "مونتسرات",
    "mt" => "مالت",
    "mu" => "ماریتائوس",
    "mv" => "مالدیو",
    "mw" => "مالاوی",
    "mx" => "مکزیک",
    "my" => "مالزی",
    "mz" => "موزامبک",
    "na" => "نامیبیا",
    "nc" => "New Caledonia",
    "ne" => "نیچریه",
    "nf" => "Norfolk Island",
    "ng" => "نیجریه",
    "ni" => "نیکاراگوئه",
    "nl" => "هلند",
    "no" => "نروژ",
    "np" => "نپال",
    "nr" => "نائورا",
    "nu" => "Niue",
    "nz" => "نیوزیلند",
    "om" => "عمان",
    "pa" => "پاناما",
    "pe" => "پرو",
    "pf" => "French Polynesia",
    "pg" => "گینه جدید",
    "ph" => "Philippines",
    "pk" => "پاکستان",
    "pl" => "لهستان",
    "pm" => "Saint Pierre and Miquelon",
    "pn" => "Pitcairn",
    "pr" => "پرو",
    "pt" => "پرتغال",
    "pw" => "پالائو",
    "py" => "پاراگوئه",
    "qa" => "قطر",
    "re" => "Reunion Island",
    "ro" => "رومانی",
    "ru" => "روسیه فدرال",
    "rs" => "روسیه",
    "rw" => "روندا",
    "sa" => "عربستان سعودی",
    "sb" => "Solomon Islands",
    "sc" => "Seychelles",
    "sd" => "سودان",
    "se" => "سودان",
    "sg" => "سنگاپور",
    "sh" => "Saint Helena",
    "si" => "اسلوونی",
    "sj" => "Svalbard",
    "sk" => "اسلواکی",
    "sl" => "Sierra Leone",
    "sm" => "سن مارینو",
    "sn" => "سنگال",
    "so" => "سومالی",
    "sr" => "سورینام",
    "st" => "Sao Tome and Principe",
    "su" => "Old U.R.S.S.",
    "sv" => "السالوادور",
    "sy" => "جمهوری سوریه",
    "sz" => "سوئیس",
    "tc" => "Turks and Caicos Islands",
    "td" => "چاد",
    "tf" => "French Southern Territories",
    "tg" => "توگو",
    "th" => "تایلند",
    "tj" => "تاجیکستان",
    "tk" => "Tokelau",
    "tm" => "ترکمنستان",
    "tn" => "تونس",
    "to" => "Tonga",
    "tp" => "تیمور شرقی",
    "tr" => "ترکیه",
    "tt" => "ترینیداد",
    "tv" => "تاوالو",
    "tw" => "تایوان",
    "tz" => "جمهوری تانزانیا",
    "ua" => "اوکراین",
    "ug" => "اوگاندا",
    "uk" => "انگلیس",
    "gb" => "بریتانیای کبیر",
    "um" => "جزایر دور افتاده ایالات متحده امریکا",
    "us" => "ایالات متحده امریکا",
    "uy" => "اروگوئهُ",
    "uz" => "ازبکستان",
    "va" => "واتیکان",
    "vc" => "Saint Vincent and the Grenadines",
    "ve" => "ونزوئلا",
    "vg" => "جزیره ویرجینای انگلیس",
    "vi" => "جزیره ویرجینای آمریکا",
    "vn" => "ویتنام",
    "vu" => "Vanuatu",
    "wf" => "والیس و فوتونا",
    "ws" => "ساموا",
    "ye" => "یمن",
    "yt" => "مایوتا",
    "yu" => "یوگسلاوی",
    "za" => "افریقای جنوبی",
    "zm" => "زامبیا",
    "zr" => "زئیر",
    "zw" => "زیمباوه",
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
    "tv" => "تاوالو",
    "ws" => "ساموا",
);
?>