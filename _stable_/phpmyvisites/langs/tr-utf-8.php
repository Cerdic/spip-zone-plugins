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
$lang['auteur_nom'] = "Uğur Çetin"; // Translator's name
$lang['auteur_email'] = "ugur.jnmbk@gmail.com"; // Translator's email
$lang['charset'] = "utf-8"; // language file charset (utf-8 by default)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "tr"; // iso language code
$lang['lang_libelle_en'] = "Turkish"; // english language name
$lang['lang_libelle_fr'] = "Turque"; // french language name
$lang['unites_bytes'] = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
$lang['separateur_milliers'] = '.'; // three thousand spells 3,000 in english
$lang['separateur_decimaux'] = ','; // Separator for the float part of a number

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | açık kaynak ağ sayfası istatistikleri ve ağ trafiği analizi uygulaması"; // Pages header's title
$lang['head_keywords'] = "phpmyvisites, php, script, application, software, statistics, referals, stats, free, open source, gpl, visits, visitors, mysql, viewed pages, pages, views, number of visits, graphs, Browsers, os, operating system, resolutions, day, week, month, records, country, host, service providors, search enginge, key words, referrers, graphs, entry pages, exit pages, pie charts"; // Header keywords
$lang['head_description'] = "phpMyVisites | PHP/MySQL ile geliştirilmiş Gnu GPL lisansı ile dağıtılan, açık kaynaklı bir ağ sayfası istatistik uygulamasıdır."; // Header description
$lang['logo_description'] = "phpMyVisites : PHP/MySQL ile geliştirilmiş, GPL ile dağıtılan, açık kaynaklı ağ sayfası istatistik uygulamasıdır."; // This is the JS code description. Has to be short.

//
// Main menu & submenu
//
$lang['menu_visites'] = "ZİYARETLER";
$lang['menu_pagesvues'] = "SAYFALAR";
$lang['menu_suivi'] = "TAKİP";
$lang['menu_provenance'] = "KAYNAK";
$lang['menu_configurations'] = "AYARLAR";
$lang['menu_affluents'] = "BAŞVURULAR";
$lang['menu_listesites'] = "Siteleri Listele";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Gün";
$lang['menu_semaine'] = "Hafta";
$lang['menu_mois'] = "Ay";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Şu an görüntülenen: %s "; // Text formatted (e.g.: Studied period: Sunday, July the 14th)
$lang['liens_siteofficiel'] = "Resmi ağ sayfası";
$lang['liens_admin'] = "Yönetim";
$lang['liens_contacts'] = "İletişim";

//
// Divers
//
$lang['generique_nombre'] = "Sayı";
$lang['generique_tauxsortie'] = "Çıkış Sıklığı";
$lang['generique_ok'] = "Tamam";
$lang['generique_timefooter'] = "Sayfa %s saniyede oluşturuldu"; // Time in seconds
$lang['generique_divers'] = "Diğerleri"; // (for the graphs)
$lang['generique_inconnu'] = "Bilinmeyen"; // (for the graphs)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "Diğer?"; // Other language, translations wanted
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
$lang['generique_total'] = "Toplam";
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
$lang['login_login'] = "giriş : "; // lowercase
$lang['login_error'] = "Giriş yapılamadı. Yanlış giriş veya parola.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Kişiler";
$lang['contacts_langue'] = "Çeviriler";
$lang['contacts_merci'] = "Teşekkürler";
$lang['contacts_auteur'] = "phpMyVisites projesinin yazarı, belgeleyicisi, ve yaratıcısı <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "<strong>Teknik sorular, hata bildirimi ve öneriler</strong> için lütfen resmi ağ sayfası forumlarını kullanın %s. Diğer istekler için, lütfen resmi ağ sayfasındaki formu kullanarak yazarla görüşün."; // adresse du site
$lang['contacts_trad1'] = "phpMyVisites kendi dilinize çevirmek mi istiyorsunuz? Hiç tereddüt etmeyin çünkü <strong>phpMyVisites'in size ihtiyacı var!</strong>";
$lang['contacts_trad2'] = "phpMyVisites'i çevirmek epey zamanınızı alır (birkaç saat) ve içerilen diller hakkında iyi bir bilgi birikimi gerektirir; şunu unutmayın ki <strong>yaptığınız işten birçok kişi faydalanacaktır</strong>.  phpMyVisites'i kendi dilinize çevirmeyle ilgilenecekseniz ihtiyacınız olan tüm bilgiyi %s phpMyVisites resmi belgelerinde %sbulabilirsiniz."; // lien vers la doc
$lang['contacts_doc'] = "Size phpMyVisites'in kurulumu, yapılandırması ve işlevselliği hakkında birçok bilgi verecek %s phpMyVisites'in resmi belgelerine %s başvurmaktan çekinmeyin. Kullandığınız phpMySites sürümü için belgeler bulunmaktadır."; // lien vers la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "phpMyVisites yapımcılarının tam listesi için resmi ağ sayfasındaki teşekkürler bölümünü ziyaret edebilirsiniz.";
$lang['contacts_merci2'] = "phpMyVisites'i kendi dillerine çevirerek kültürünü bizimle paylaşan herkese çok teşekkür ederiz:";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Ziyaretçi bilgisi"; 
$lang['visites_statistiques'] = "İstatistikler";
$lang['visites_periodesel'] = "Seçili zaman aralığı için";
$lang['visites_visites'] = "Ziyaretler";
$lang['visites_uniques'] = "Tekil ziyaretçiler";
$lang['visites_pagesvues'] = "Sayfalar";
$lang['visites_pagesvisiteurs'] = "Ziyaretçi başına düşen sayfa"; 
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Ortalama ziyaret süresi";
$lang['visites_tempsmoyenpv'] = "Bir sayfaya düşen görüntüleme süresi";
$lang['visites_tauxvisite'] = "Bir sayfanın ziyaret artışı"; 
$lang['visites_recapperiode'] = "Haftalık özet";
$lang['visites_nbvisites'] = "Ziyaretler";
$lang['visites_aucunevivisite'] = "Ziyaret yok"; // in the table, must be short
$lang['visites_recap'] = "Özet";
$lang['visites_unepage'] = "1 sayfa"; // (graph)
$lang['visites_pages'] = "%s sayfa"; // 1-2 pages (graph)
$lang['visites_min'] = "%s dak"; // 10-15 min (graph)
$lang['visites_sec'] = "%s s"; // 0-30 s (seconds, graph)
$lang['visites_grapghrecap'] = "İstatistik özeti grafiği";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Ziyaret süresi grafiği";
$lang['visites_graphtempsvisitesimg'] = "Ziyaret süreleri";
$lang['visites_graphheureserveur'] = "Sunucu için saatlik ziyaret grafiği"; 
$lang['visites_graphheureserveurimg'] = "Sunucu saatine göre ziyaretler"; 
$lang['visites_graphheurevisiteur'] = "Ziyaretçi için saatlik ziyaret grafiği";
$lang['visites_graphheurelocalimg'] = "Yerel zamana göre ziyaretler"; 
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
$lang['pagesvues_titre'] = "Sayfa ziyaretleri";
$lang['pagesvues_joursel'] = "Seçilen günler";
$lang['pagesvues_jmoins7'] = "Gün - 7";
$lang['pagesvues_jmoins14'] = "Gün - 14";
$lang['pagesvues_moyenne'] = "(ortalama)";
$lang['pagesvues_pagesvues'] = "Sayfa ziyareti";
$lang['pagesvues_pagesvudiff'] = "Tekil sayfa ziyareti";
$lang['pagesvues_recordpages'] = "Bir ziyaretçinin ziyaret ettiği en fazla sayfa";
$lang['pagesvues_tabdetails'] = "Görüntülenen sayfalar (%s - %s aralığı)"; // (from 1 to 21)
$lang['pagesvues_graphsnbpages'] = "Görüntülenen sayfalara göre ziyaretler grafiği";
$lang['pagesvues_graphnbvisitespageimg'] = "Görüntülenen sayfalara göre ziyaretler";
$lang['pagesvues_graphheureserveur'] = "Sunucu saatine göre ziyaret grafiği";
$lang['pagesvues_graphheureserveurimg'] = "Sunucu saatine göre ziyaretler";
$lang['pagesvues_graphheurevisiteur'] = "Yerel saate göre ziyaret grafiği";
$lang['pagesvues_graphpageslocalimg'] = "Yerel saate göre ziyaretler";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Ziyaretçi hareketi";
$lang['suivi_pageentree'] = "Giriş sayfaları";
$lang['suivi_pagesortie'] = "Çıkış sayfaları";
$lang['suivi_tauxsortie'] = "Çıkış sıklığı";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Ziyaretçi Kaynağı";
$lang['provenance_recappays'] = "Ülke Özetleri";
$lang['provenance_pays'] = "Ülkeler";
$lang['provenance_paysimg'] = "Ülkeye Göre Ziyaretçiler";
$lang['provenance_fai'] = "İnternet Servis Sağlayıcıları";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Sağlayıcılar"; // same as $lang['provenance_fai'], but not if $lang['provenance_fai'] is too long
$lang['provenance_continent'] = "Kıta";
$lang['provenance_mappemonde'] = "Dünya haritası";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Ziyaretçi Ayarları";
$lang['configurations_os'] = "İşletim Sistemleri";
$lang['configurations_osimg'] = "Ziyaretçilerin işletim sistemleri grafiği";
$lang['configurations_navigateurs'] = "Tarayıcılar";
$lang['configurations_navigateursimg'] = "Ziyaretçilerin tarayıcıları grafiği";
$lang['configurations_resolutions'] = "Ekran Çözünürlükleri";
$lang['configurations_resolutionsimg'] = "Ziyaretçilerin ekran çözünürlükleri grafiği";
$lang['configurations_couleurs'] = "Renk Derinliği";
$lang['configurations_couleursimg'] = "Ziyaretçilerin renk derinliği grafiği";
$lang['configurations_rapport'] = "Normal/Geniş ekran";
$lang['configurations_large'] = "Genis ekran";
$lang['configurations_normal'] = "Normal";
$lang['configurations_double'] = "Dual Screen";
$lang['configurations_plugins'] = "Eklentiler";
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
$lang['affluents_titre'] = "Yönlendiriciler";
$lang['affluents_recapimg'] = "Yönlendiricilere Göre Ziyaretçiler";
$lang['affluents_directimg'] = "Doğrudan";
$lang['affluents_sitesimg'] = "Ağ Sayfaları";
$lang['affluents_moteursimg'] = "Motorlar";
$lang['affluents_referrersimg'] = "Yönlendiriciler";
$lang['affluents_moteurs'] = "Arama Motorları";
$lang['affluents_nbparmoteur'] = "Arama motorundan sağlanan ziyaretler : %s";
$lang['affluents_aucunmoteur'] = "Arama motorlarından ziyaret sağlanmadı.";
$lang['affluents_motscles'] = "Anahtar sözcükler";
$lang['affluents_nbmotscles'] = "Farklı anahtar sözcükler : %s";
$lang['affluents_aucunmotscles'] = "Anahtar sözcük bulunamadı.";
$lang['affluents_sitesinternet'] = "Ağ sayfaları";
$lang['affluents_nbautressites'] = "Diğer ağ sayfalarından gelen ziyaretler : %s";
$lang['affluents_nbautressitesdiff'] = "Farklı ağ sayfası sayısı : %s";
$lang['affluents_aucunautresite'] = "Ağ sayfalarından gelen ziyaret yok.";
$lang['affluents_entreedirecte'] = "Doğrudan İstek";
$lang['affluents_nbentreedirecte'] = "Doğrudan yapılan ziyaretler : %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Ayrıntılar"; // In the results of the referers array
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
$lang['purge_titre'] = "Ziyaret ve yönlendirici özetleri";
$lang['purge_intro'] = "Bu zaman aralığı yönetim içinden kaldırıldı, yalnızca önemli istatistikler kaldı.";
$lang['admin_purge'] = "Veritabanı bakımı";
$lang['admin_purgeintro'] = "Bu bölüm phpMyVisites'in kullandığı tabloları yönetmenizi sağlar. Tabloların kullandığı disk alanını görebilir, en iyi hale getirebilir ya da eski kayıtları silebilirsiniz. Bu işlem veritabanınızdaki tabloların kapladığı yeri sınırlamanıza izin verir.";
$lang['admin_optimisation'] = "[ %s ] en iyi hale getiriliyor..."; // Tables names
$lang['admin_postopt'] = "Toplam boyut %chiffres% %unites% azaltıldı"; // 28 Kb
$lang['admin_purgeres'] = "Şu aralığı sil: %s";
$lang['admin_purge_fini'] = "Tabloların silinmesi bitti...";
$lang['admin_bdd_nom'] = "İsim";
$lang['admin_bdd_enregistrements'] = "Kayıt";
$lang['admin_bdd_taille'] = "Tablo Boyutu";
$lang['admin_bdd_opt'] = "En İyi Hale Getir";
$lang['admin_bdd_purge'] = "Temizleme Ölçütü";
$lang['admin_bdd_optall'] = "Tümünü En İyi Hale Getir";
$lang['admin_purge_j'] = "%s günden eski olan kayıtları sil";
$lang['admin_purge_s'] = "%s haftadan eski olan kayıtları sil";
$lang['admin_purge_m'] = "%s aydan eski olan kayıtları sil";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Tüm kayıtları sil";
$lang['admin_purge_autres'] = "Genel olarak '%s' tablosuna temizle";
$lang['admin_purge_none'] = "Yapılabilecek işlem yok";
$lang['admin_purge_cal'] = "Hesapla ve temizle (birkaç dakika sürebilir)";
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
$lang['admin_intro'] = "phpMyVisites yapılandırma alanına hoş geldiniz. Kurulum ile ilgili tüm bilgilerinizi değiştirebilirsiniz. Herhangi bir sorunla karşılaşırsanız %s resmi phpMyVisites belgelerine %s başvurmaktan çekinmeyin."; // link to the doc
$lang['admin_configetperso'] = "Genel ayarlar";
$lang['admin_afficherjavascript'] = "JavaScript istatistik kodunu göster";
$lang['admin_cookieadmin'] = "Yönetici istatistiklere katılmasın";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Kayıtlı ağ sayfaları:";
$lang['admin_retour'] = "Geri";
$lang['admin_cookienavigateur'] = "Yöneticiyi istatistiklere katmayabilirsiniz. Bu yöntem çerez tabanlıdır ve bu seçenek sadece şu an kullandığınız tarayıcı için çalışacaktır. Bu seçeneği istediğiniz zaman değiştirebilirsiniz.";
$lang['admin_prendreencompteadmin'] = "Yöneticiyi istatistiklere kat (çerezi sil)";
$lang['admin_nepasprendreencompteadmin'] = "Yöneticiyi istatistiklere katma (çerez oluştur)";
$lang['admin_etatcookieoui'] = "Bu ağ sayfası için yönetici istatistiklere katılıyor (Bu varsayılan yapılandırmadır, normal bir ziyaretçi gibi hesaba katılırsınız)";
$lang['admin_etatcookienon'] = "Bu ağ sayfası için istatistiklere katılmıyorsunuz (Ziyaretleriniz bu ağ sayfası için sayılmayacak)";
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
$lang['install_loginmysql'] = "Veritabanı girişi";
$lang['install_mdpmysql'] = "Veritabanı şifresi";
$lang['install_serveurmysql'] = "Veritabanı sunucusu";
$lang['install_basemysql'] = "Veritabanı adı";
$lang['install_prefixetable'] = "Tablo öneki";
$lang['install_utilisateursavances'] = "Uzman kullanıcılar (isteğe bağlı)";
$lang['install_oui'] = "Evet";
$lang['install_non'] = "Hayır";
$lang['install_ok'] = "Tamam";
$lang['install_probleme'] = "Sorun: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Yönetici girişi:";
$lang['install_mdpadmin'] = "Yönetici parolası:";
$lang['install_chemincomplet'] = "phpMyVisites uygulamasının tam yolu (http://www.sayfam.com/rep1/rep3/phpmyvisites/ gibi). Yol mutlaka <strong>/</strong> işareti ile bitmelidir.";
$lang['install_afficherlogo'] = "Sayfalarınızda logo görünsün %s <br />By allowing the display of the logo on your site, you will help publicize phpMyVisites and help it evolve more rapidly.  It is also a way to thank the author who has spent many hours developing this Open Source, free application."; // %s replaced by the logo image
$lang['install_affichergraphique'] = "İstatistik grafiklerini göster.";
$lang['install_valider'] = "Gönder"; //  during installation and for login
$lang['install_popup_logo'] = "Lütfen bir logo seçin";
$lang['install_logodispo'] = "Mümkün olan çeşitli logoları gör";
$lang['install_welcome'] = "Welcome!";
$lang['install_system_requirements'] = "System Requirements";
$lang['install_database_setup'] = "Database Setup";
$lang['install_create_tables'] = "Table creation";
$lang['install_general_setup'] = "General Setup";
$lang['install_create_config_file'] = "Create Config File";
$lang['install_first_website_setup'] = "Add First Website";
$lang['install_display_javascript_code'] = "Display Javascript code";
$lang['install_finish'] = "Finished!";
$lang['install_txt2'] = "Kurulumun sonunda, phpMyVisites kullanıcılarının sayısını tutmamıza yardım etmek için bir istek gönderilecektir. Anlayışlı olduğunuz için teşekkür ederiz.";
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
$lang['update_jschange'] = "Uyarı! <br /> phpMyVisites javascript kodu değiştiirldi. Tüm ağ sayfalarınızı MUTLAKA yeni phpMyVisites Javascript kodunu kopyalayıp yapıştırarak güncellemelisiniz. <br /> javascript kodunda nadiren değişiklik yapılır, verdiğimiz rahatsılıktan dolayı özür dileriz.";

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
$lang['moistab']['01'] = "Ocak";
$lang['moistab']['02'] = "Şubat";
$lang['moistab']['03'] = "Mart";
$lang['moistab']['04'] = "Nisan";
$lang['moistab']['05'] = "Mayıs";
$lang['moistab']['06'] = "Haziran";
$lang['moistab']['07'] = "Temmuz";
$lang['moistab']['08'] = "Ağustos";
$lang['moistab']['09'] = "Eylül";
$lang['moistab']['10'] = "Ekim";
$lang['moistab']['11'] = "Kasım";
$lang['moistab']['12'] = "Aralık";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Oca";
$lang['moistab_graph']['02'] = "Şub";
$lang['moistab_graph']['03'] = "Mar";
$lang['moistab_graph']['04'] = "Nis";
$lang['moistab_graph']['05'] = "May";
$lang['moistab_graph']['06'] = "Haz";
$lang['moistab_graph']['07'] = "Tem";
$lang['moistab_graph']['08'] = "Ağu";
$lang['moistab_graph']['09'] = "Eyl";
$lang['moistab_graph']['10'] = "Eki";
$lang['moistab_graph']['11'] = "Kas";
$lang['moistab_graph']['12'] = "Ara";

// Day of the week
$lang['jsemaine']['Mon'] = "Pazartesi";
$lang['jsemaine']['Tue'] = "Salı";
$lang['jsemaine']['Wed'] = "Çarşamba";
$lang['jsemaine']['Thu'] = "Perşembe";
$lang['jsemaine']['Fri'] = "Cuma";
$lang['jsemaine']['Sat'] = "Cumartesi";
$lang['jsemaine']['Sun'] = "Pazar";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Pzt";
$lang['jsemaine_graph']['Tue'] = "Sal";
$lang['jsemaine_graph']['Wed'] = "Car";
$lang['jsemaine_graph']['Thu'] = "Per";
$lang['jsemaine_graph']['Fri'] = "Cum";
$lang['jsemaine_graph']['Sat'] = "Cmt";
$lang['jsemaine_graph']['Sun'] = "Paz";

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
$lang['eur'] = "Avrupa";
$lang['afr'] = "Afrika";
$lang['asi'] = "Asya";
$lang['ams'] = "Güney ve Orta Amerika";
$lang['amn'] = "Kuzey America";
$lang['oce'] = "Avustralya";

// Oceans
$lang['oc_pac'] = "Büyük Okyanus";
$lang['oc_atl'] = "Atlas Okyanusu";
$lang['oc_ind'] = "Hint Okyanusu";

// Countries
$lang['domaines'] = array(
    "xx" => "Bilinmeyen",
    "ac" => "Ascension Adaları",
    "ad" => "Andora",
    "ae" => "Birleşik Arap Emirlikleri",
    "af" => "Afganistan",
    "ag" => "Antigua ve Barbuda",
    "ai" => "Anguilla",
    "al" => "Albania",
    "am" => "Ermenistan",
    "an" => "Hollanda Antilleri",
    "ao" => "Angola",
    "aq" => "Antartika",
    "ar" => "Arjantin",
    "as" => "Amerikan Samoa",
    "at" => "Avusturya",
    "au" => "Avustralya",
    "aw" => "Aruba",
    "az" => "Azerbaycan",
    "ba" => "Bosna-Hersek",
    "bb" => "Barbados",
    "bd" => "Bangladeş",
    "be" => "Belçika",
    "bf" => "Burkina Faso",
    "bg" => "Bulgaristan",
    "bh" => "Bahreyn",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermuda",
    "bn" => "Bruneo",
    "bo" => "Bolivya",
    "br" => "Brezilya",
    "bs" => "Bahamalar",
    "bt" => "Bhutan",
    "bv" => "Bouvet Adası",
    "bw" => "Botsvana",
    "by" => "Belçika",
    "bz" => "Belize",
    "ca" => "Kanada",
    "cc" => "Cocos Adaları",
    "cd" => "Kongo Demokratik Cumhuriyeti",
    "cf" => "Orta Afrika Cumhuriyeti",
    "cg" => "Kongo",
    "ch" => "İsviçre",
    "ci" => "Cote D'Ivoire",
    "ck" => "Cook Adaları",
    "cl" => "Şili",
    "cm" => "Kamerun",
    "cn" => "Çin",
    "co" => "Kolombiya",
    "cr" => "Kosta Rika",
	"cs" => "Serbia Montenegro",
    "cu" => "Küba",
    "cv" => "Cape Verde",
    "cx" => "Christmas Adaları",
    "cy" => "Kıbrıs",
    "cz" => "Çek Cumhuriyeti",
    "de" => "Almanya",
    "dj" => "Cibuti",
    "dk" => "Danimarka",
    "dm" => "Dominik",
    "do" => "Dominik Cumhuriyeti",
    "dz" => "Algerya",
    "ec" => "Ekvador",
    "ee" => "Estonya",
    "eg" => "Mısır",
    "eh" => "Batı Sahra",
    "er" => "Eritre",
    "es" => "İspanya",
    "et" => "Etiyopya",
    "fi" => "Finlandiya",
    "fj" => "Fiji",
    "fk" => "Falkland Adaları (Malvinas)",
    "fm" => "Micronesia, Federal Devletleri",
    "fo" => "Faroe Adaları",
    "fr" => "Fransa",
    "ga" => "Gabon",
    "gd" => "Grenada",
    "ge" => "Corciya",
    "gf" => "Fransız Guyanası",
    "gg" => "Guernsey",
    "gh" => "Gana",
    "gi" => "Cebelitarık",
    "gl" => "Grönland",
    "gm" => "Gambiya",
    "gn" => "Gine",
    "gp" => "Guadeloupe",
    "gq" => "Ekvador Ginesi",
    "gr" => "Yunanistan",
    "gs" => "Güney Corciya ve Güney Sandwich adaları",
    "gt" => "Guetemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard and McDonald Adaları",
    "hn" => "Honduras",
    "hr" => "Croatia",
    "ht" => "Haiti",
    "hu" => "Macaristan",
    "id" => "Endonezya",
    "ie" => "Irlanda",
    "il" => "Israil",
    "im" => "Man Adası",
    "in" => "Hindistan",
    "io" => "İngiliz Hint Okyanusu Bölgesi",
    "iq" => "Irak",
    "ir" => "İran",
    "is" => "Iceland",
    "it" => "İtalya",
    "je" => "Jersey",
    "jm" => "Jamaika",
    "jo" => "Jordan",
    "jp" => "Japonya",
    "ke" => "Kenya",
    "kg" => "Kırgızistan",
    "kh" => "Kamboçya",
    "ki" => "Kiribati",
    "km" => "Komoros",
    "kn" => "Saint Kitts ve Nevis",
    "kp" => "Kore, Demokratik Halk Cumhuriyeti",
    "kr" => "Kore, Cumhuriyeti",
    "kw" => "Küveyt",
    "ky" => "Kayman Adaları",
    "kz" => "Kazakistan",
    "la" => "Laos",
    "lb" => "Lebanon",
    "lc" => "Saint Lucia",
    "li" => "Liechtenstein",
    "lk" => "Sri Lanka",
    "lr" => "Liberya",
    "ls" => "Lesotho",
    "lt" => "Litvanya",
    "lu" => "Lüksemburg",
    "lv" => "Litvanya",
    "ly" => "Libya",
    "ma" => "Moroko",
    "mc" => "Monako",
    "md" => "Moldova Cumhuriyeti",
    "mg" => "Madagaskar",
    "mh" => "Marshall Adaları",
    "mk" => "Makedonia",
    "ml" => "Mali",
    "mm" => "Myanmar",
    "mn" => "Moğolistan",
    "mo" => "Macau",
    "mp" => "Kuzey Mariana Adaları",
    "mq" => "Martinik",
    "mr" => "Moritanya",
    "ms" => "Montserrat",
    "mt" => "Malta",
    "mu" => "Mauritius",
    "mv" => "Maldivler",
    "mw" => "Malavi",
    "mx" => "Meksika",
    "my" => "Malezya",
    "mz" => "Mozambik",
    "na" => "Namibya",
    "nc" => "Yeni Kaledonya",
    "ne" => "Nijer",
    "nf" => "Norfolk Adası",
    "ng" => "Nijerya",
    "ni" => "Nikaragua",
    "nl" => "Hollanda",
    "no" => "Norveç",
    "np" => "Nepal",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "Yeni Zelanda",
    "om" => "Oman",
    "pa" => "Panama",
    "pe" => "Peru",
    "pf" => "Fransız Polinezyası",
    "pg" => "Papua Yeni Gine",
    "ph" => "Filipinler",
    "pk" => "Pakistan",
    "pl" => "Polonya",
    "pm" => "Saint Pierre ve Miquelon",
    "pn" => "Pitcairn Adaları",
    "pr" => "Porto Riko",
    "pt" => "Portekiz",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Katar",
    "re" => "Reunion",
    "ro" => "Romanya",
    "ru" => "Beyaz Rusya",
    "rs" => "Rusya",
    "rw" => "Ruanda",
    "sa" => "Suudi Arabistan",
    "sb" => "Solomon Adaları",
    "sc" => "Seyşel",
    "sd" => "Sudan",
    "se" => "İsveç",
    "sg" => "Singapur",
    "sh" => "Saint Helena",
    "si" => "Slovenya",
    "sj" => "Svalbard",
    "sk" => "Slovakya",
    "sl" => "Sierra Leone",
    "sm" => "San Marino",
    "sn" => "Senegal",
    "so" => "Somali",
    "sr" => "Surinam",
    "st" => "Sao Tome ve Principe",
    "su" => "Eski S.S.C.B",
    "sv" => "El Salvador",
    "sy" => "Suriye",
    "sz" => "İsviçre",
    "tc" => "Turks and Caicos Adaları",
    "td" => "Çat",
    "tf" => "Fransız Güney Bölgeleri",
    "tg" => "Togo",
    "th" => "Tayland",
    "tj" => "Tacikistan",
    "tk" => "Tokelau",
    "tm" => "Türkmenistan",
    "tn" => "Tunus",
    "to" => "Tonga",
    "tp" => "Doğu Timor",
    "tr" => "Türkiye",
    "tt" => "Trinidad ve Tobago",
    "tv" => "Tuvalu",
    "tw" => "Tayvan",
    "tz" => "Tanzanya, Birleşik Cumhuriyet",
    "ua" => "Ukrayna",
    "ug" => "Uganda",
    "uk" => "Birleşik Krallık",
    "gb" => "Büyük Britanya",
    "um" => "Birleşik Devletler Minor Outlying Adaları",
    "us" => "Birleşik Devletler",
    "uy" => "Uruguay",
    "uz" => "Özbekistan",
    "va" => "Vatikan",
    "vc" => "Saint Vincent ve Grenadinler",
    "ve" => "Venezuella",
    "vg" => "Virgin Adaları, İngiliz",
    "vi" => "Virgin Islands, A.B.D.",
    "vn" => "Vietnam",
    "vu" => "Vanuatu",
    "wf" => "Vallis ve Futuna",
    "ws" => "Samoa",
    "ye" => "Yemen",
    "yt" => "Mayotte",
    "yu" => "Yugoslavya",
    "za" => "Güney Africa",
    "zm" => "Zambiya",
    "zr" => "Zaire",
    "zw" => "Zimbabve",
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