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
$lang['auteur_nom'] = "Clément Herbo"; // nombre del traductor
$lang['auteur_email'] = "clement.herbo@free.fr"; //// mail del traductor
$lang['charset'] = "utf-8"; // charset del archivo de idioma (utf-8 predefinido)
$lang['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$lang['lang_iso'] = "sp"; // iso language code
$lang['lang_libelle_en'] = "Spanish"; // nombre del idioma en inglés
$lang['lang_libelle_fr'] = "Espagnol"; // nombre del idioma en francés
$lang['unites_bytes'] = array('Octetos', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo');
$lang['separateur_milliers'] = ' '; // tres cientos mil se escribe en español 300 000
$lang['separateur_decimaux'] = ','; // entre la parte entera de un número y la parte decimal

//
// HTML Markups
//
$lang['head_titre'] = "phpMyVisites | Aplicación libre y gratuita de gestión de estadísticas y de medida de audiencia de sitios Internet"; // Título de las  páginas de estadísticas en el header html
$lang['head_keywords'] = "phpmyvisites, php, script, aplicación, logicial, estadísticas, medida de audiencia, audiencia, estadísticas, gratuito, open source, gpl, visitas, visitantes, mysql, páginas vistas, páginas, vistas, tiempo de visitas, gráficos, navegadores, os, sistemas de explotación, resoluciones, día, semana, mes, récord, país, host, provider, buscadores, palabras claves, seguimiento, referencias, gráficos, páginas de llegadas, páginas de salidas, gráficos redondos"; // Palabras claves del header html
$lang['head_description'] = "phpMyVisites | Aplicación de estadísticas y de medida de audiencia de sitios Internet | Logicial gratuito y open source distribuido bajo licencia GPL, desarrollado en php/MySQL"; // Descripción en el header html 
$lang['logo_description'] = "phpMyVisites : logicial gratuito de medida de estadísticas y de audiencia de sitios Internet (licencie libre GPL, logicial en php/MySQL)"; // descripción para el código JS : ir al grano

//
// Main menu & submenu
//
$lang['menu_visites'] = "Visitas";
$lang['menu_pagesvues'] = "Páginas vistas";
$lang['menu_suivi'] = "Seguimiento";
$lang['menu_provenance'] = "Procedencia";
$lang['menu_configurations'] = "Configuraciones";
$lang['menu_affluents'] = "Afluentes";
$lang['menu_listesites'] = "Lista de sitios";
$lang['menu_bilansites'] = "Summary";
$lang['menu_jour'] = "Día";
$lang['menu_semaine'] = "Semana";
$lang['menu_mois'] = "Mes";
$lang['menu_annee'] = "Year";
$lang['menu_periode'] = "Período estudiado : %s"; // Período estudiado : Lunes 11 de Noviembre
$lang['liens_siteofficiel'] = "Sitio oficial";
$lang['liens_admin'] = "Instalación &amp; configuración";
$lang['liens_contacts'] = "Contactos";

//
// Divers
//
$lang['generique_nombre'] = "Número";
$lang['generique_tauxsortie'] = "Tasa de salidas";
$lang['generique_ok'] = "ok";
$lang['generique_timefooter'] = "La generación de la página a tomado %s segundos"; // tiempo en segundos
$lang['generique_divers'] = "Diversos"; // (para los gráficos)
$lang['generique_inconnu'] = "Desconocido"; // (para los gráficos)
$lang['generique_vous'] = "... You ?";
$lang['generique_traducteur'] = "Translator";
$lang['generique_langue'] = "Language";
$lang['generique_autrelangure'] = "¿Otro?"; // Autre langue, appel à contribution
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
$lang['generique_total'] = "TOTAL";
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
$lang['login_password'] = "contraseña :"; // en minúsculas
$lang['login_login'] = "usuario :"; // en minúsculas
$lang['login_error'] = "Error en de la conexión. Malos usario/contraseña.";
$lang['login_protected'] = "You wish to enter a %sphpMyVisites%s protected area.";

//
// Contacts & "Others ?"
//
$lang['contacts_titre'] = "Contactos";
$lang['contacts_langue'] = "Traducciones";
$lang['contacts_merci'] = "Agradecimientos";
$lang['contacts_auteur'] = "El autor de la aplicación, de la documentación y creador del proyecto phpMyVisites es <strong>Matthieu Aubry</strong>.";
$lang['contacts_questions'] = "Para toda <strong>pregunta técnica, informe de bug, o sugestión</strong>, por favor <strong>utilice los foros previstos a este efecto</strong> en la página oficial %s. Para las demás demandas, por favor contacte el autor gracias al formulario de la página oficial."; // dirección del sitio
$lang['contacts_trad1'] = "¿Quisiera usted utilizar phpMyVisites en otro idioma? Puede contribuir a las traducciones de la aplicación, <strong>¡phpMyVisites le necesita!</strong>";
$lang['contacts_trad2'] = "Traducir phpMyVisites es un trabajo que pide un poco de tiempo (algunas horas) y que necesita un perfecto dominio de ambos idiomas; pero <strong>el trabajo así realizado será útil a numerosos otros utilizadores</strong>, los cuales podrán utilizar plenamente phpMyVisites. Si esta interesado en traducir phpMyVisites, encontrara todas les informaciones necesarias en %s la documentación oficial de phpMyVisites %s."; // vínculo hacia la doc
$lang['contacts_doc'] = "No dude en consultar %s la documentación oficial de phpMyVisites %s que le dará muchas informaciones sobre la instalación, la configuración, las funcionalidades de phpMyVisites, etc. Está disponible directamente en vuestra versión de phpMyVisites."; // vínculo hacia la doc
$lang['contacts_thanks_dev'] = "Thank you <strong>%s</strong>, co-developers of phpMyVisites, for their high quality work on the project.";
$lang['contacts_merci3'] = "No dude en consultar la página de agradecimientos en el sitio oficial de phpMyVisites para tener una lista más completa de los amigos de phpMyVisites.";
$lang['contacts_merci2'] = "Un gran agradecimiento también a todos los que han compartido su cultura al traducir phpMyVisites :";

//
// Rss & Mails
//
$lang['rss_titre'] = "Site %s on %s"; // Site MyHomePage on Sunday 29 
$lang['rss_go'] = "Go to detailed statistics";

//
// Visits Part
//
$lang['visites_titre'] = "Informaciones visitas";
$lang['visites_statistiques'] = "Estadísticas";
$lang['visites_periodesel'] = "Para el período seleccionado";
$lang['visites_visites'] = "Visitas";
$lang['visites_uniques'] = "Visitantes únicos";
$lang['visites_pagesvues'] = "Páginas vistas";
$lang['visites_pagesvisiteurs'] = " Páginas vistas por visitante";
$lang['visites_pagesvisites'] = "Pages per visit"; 
$lang['visites_pagesvisitessign'] = "Pages per significant visit"; 
$lang['visites_tempsmoyen'] = "Tiempo medio de visita";
$lang['visites_tempsmoyenpv'] = "Tiempo medio por página vista";
$lang['visites_tauxvisite'] = "Tasa de visitas de una página";
$lang['visites_recapperiode'] = "Recapitulativo del período";
$lang['visites_nbvisites'] = "Visitas";
$lang['visites_aucunevivisite'] = "Ninguna visita"; // en un cuadro, ir al grano 
$lang['visites_recap'] = "Recapitulativo";
$lang['visites_unepage'] = "1 página"; // (gráfico)
$lang['visites_pages'] = "%s páginas"; // 1-2 páginas (gráfico)
$lang['visites_min'] = "%s min"; // 10-15 min (gráfico)
$lang['visites_sec'] = "%s s"; // 0-30 s (segundos, gráfico)
$lang['visites_grapghrecap'] = "Gráfico recapitulativo de las estadísticas";
$lang['visites_grapghrecaplongterm'] = "Graph to show long term statistics summary";
$lang['visites_graphtempsvisites'] = "Gráfico de duraciones de visitas por visitante";
$lang['visites_graphtempsvisitesimg'] = "Duración de visitas por visitante";
$lang['visites_graphheureserveur'] = "Gráfico de visitas por hora del servidor";
$lang['visites_graphheureserveurimg'] = "Visitantes horas servidor";
$lang['visites_graphheurevisiteur'] = "Gráfico de visitas por hora del visitante";
$lang['visites_graphheurelocalimg'] = "Visitas horas locales";
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
$lang['pagesvues_titre'] = "Informaciones páginas vistas";
$lang['pagesvues_joursel'] = "Día seleccionado";
$lang['pagesvues_jmoins7'] = "Día J-7";
$lang['pagesvues_jmoins14'] = "Día J-14";
$lang['pagesvues_moyenne'] = "(media)";
$lang['pagesvues_pagesvues'] = "Páginas vistas";
$lang['pagesvues_pagesvudiff'] = "Páginas vistas diferentes";
$lang['pagesvues_recordpages'] = "Récord de páginas vistas por 1 visitante";
$lang['pagesvues_tabdetails'] = "Cuadro de detalles de las páginas vistas (de %s a %s)"; // (de 1 à 21)
$lang['pagesvues_graphsnbpages'] = "Gráfico de las visitas por número de páginas vistas";
$lang['pagesvues_graphnbvisitespageimg'] = "Visitas por número de páginas vistas";
$lang['pagesvues_graphheureserveur'] = "Gráfico de las páginas vistas por hora del servidor";
$lang['pagesvues_graphheureserveurimg'] = "Páginas vistas horas servidor";
$lang['pagesvues_graphheurevisiteur'] = "Gráfico de las páginas vistas por hora del visitante";
$lang['pagesvues_graphpageslocalimg'] = "Páginas vistas horas locales";
$lang['pagesvues_tempsparpage'] = "Time by page";
$lang['pagesvues_total_time'] = "Total time";
$lang['pagesvues_avg_time'] = "Average time";

//
// Follows-Up
//
$lang['suivi_titre'] = "Seguimiento del visitante";
$lang['suivi_pageentree'] = "Páginas de entrada";
$lang['suivi_pagesortie'] = "Páginas de salida";
$lang['suivi_tauxsortie'] = "Tasa de salida";
$lang['suivi_pageentreehits'] = "Entry hits";
$lang['suivi_pagesortiehits'] = "Exit hits";
$lang['suivi_singlepage'] = "Single Pages visits";

//
// Origin
//
$lang['provenance_titre'] = "Procedencia";
$lang['provenance_recappays'] = "Recapitulativo de los países";
$lang['provenance_pays'] = "País";
$lang['provenance_paysimg'] = "Gráfico de los países de los visitantes";
$lang['provenance_fai'] = "Providers";
$lang['provenance_nbpays'] = "Number of different countries : %s";
$lang['provenance_provider'] = "Providers"; // el mismo que $lang['provenance_fai'], excepto si $lang['provenance_fai'] demasiado largo
$lang['provenance_continent'] = "Continente";
$lang['provenance_mappemonde'] = "Mapamundi";
$lang['provenance_interetspays'] = "Countries Interests";

//
// Setup
//
$lang['configurations_titre'] = "Configuraciones visitantes";
$lang['configurations_os'] = "Sistemas de explotación (OS)";
$lang['configurations_osimg'] = "Gráfico de OS por visitante";
$lang['configurations_navigateurs'] = "Navegadores";
$lang['configurations_navigateursimg'] = "Graficó de los navegadores por visitante";
$lang['configurations_resolutions'] = "Resolución de la pantalla";
$lang['configurations_resolutionsimg'] = "Gráfico de las resoluciones por visitante";
$lang['configurations_couleurs'] = "Colores";
$lang['configurations_couleursimg'] = "Gráfico de los colores por visitante";
$lang['configurations_rapport'] = "Pantalla ancha/normal";
$lang['configurations_large'] = "Pantalla ancha";
$lang['configurations_normal'] = "Pantalla normal";
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
$lang['affluents_titre'] = "Afluentes";
$lang['affluents_recapimg'] = "Gráfico recapitulativo afluentes";
$lang['affluents_directimg'] = "Directo";
$lang['affluents_sitesimg'] = "Sitios";
$lang['affluents_moteursimg'] = "Buscadores";
$lang['affluents_referrersimg'] = "Afluentes";
$lang['affluents_moteurs'] = "Buscadores";
$lang['affluents_nbparmoteur'] = "Número de visitantes que han llegado al sitio gracias a buscadores: %s";
$lang['affluents_aucunmoteur'] = "Ningún visitante a llegado al sitio gracias a buscadores.";
$lang['affluents_motscles'] = "Palabras claves";
$lang['affluents_nbmotscles'] = "Número de distintas palabras claves: %s";
$lang['affluents_aucunmotscles'] = "Ninguna palabra clave ha sido encontrada.";
$lang['affluents_sitesinternet'] = "Sitios Internet";
$lang['affluents_nbautressites'] = "Número de visitantes que han llegado al sitio gracias a vínculos en otro sitios Internet: %s";
$lang['affluents_nbautressitesdiff'] = "Número de sitios diferentes: %s";
$lang['affluents_aucunautresite'] = "Ningún visitante a llegado al sitio gracias a otro sitios Internet.";
$lang['affluents_entreedirecte'] = "Entradas directas";
$lang['affluents_nbentreedirecte'] = "Número de visitantes que han llegado directamente al sitio: %s";
$lang['affluents_nbpartenaires'] = "Visits provided by partners : %s";
$lang['affluents_aucunpartenaire'] = "No visits were provided by partners sites.";
$lang['affluents_nbnewsletters'] = "Visits provided by newsletters : %s";
$lang['affluents_aucunnewsletter'] = "No visits were provided by newsletters.";
$lang['affluents_details'] = "Detalles"; // en el cuadro bajo los resultados de sitios referrers
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
$lang['purge_titre'] = "Resumen de las visitas y de los afluentes";
$lang['purge_intro'] = "Este período a sido purgado en la administración: sólo las estadísticas más relevantes han sido guardadas.";
$lang['admin_purge'] = "Purgación y optimización de la base de datos";
$lang['admin_purgeintro'] = "Esta sección le permite gestionar los datos de phpMyVisites. Puede consultar el espacio ocupado por los datos, optimizarlos (aconsejado para los datos de gran tamaño), o purgar remotos datos. Esto le permitirá limitar el tamaño de los datos el la base de datos.";
$lang['admin_optimisation'] = "Optimización de [ %s ]..."; // nombre de las tablas
$lang['admin_postopt'] = "El tamaño total a disminuido de %chiffres% %unites%"; // Ex : 28 Ko
$lang['admin_purgeres'] = "Purgación de períodos siguientes: %s";
$lang['admin_purge_fini'] = "Final de la operación de purgamiento de las tablas...";
$lang['admin_bdd_nom'] = "Nombre";
$lang['admin_bdd_enregistrements'] = "Grabaciones";
$lang['admin_bdd_taille'] = "Tamaño tabla";
$lang['admin_bdd_opt'] = "Optimizar";
$lang['admin_bdd_purge'] = "Purga de tablas";
$lang['admin_bdd_optall'] = "Optimizarlo todo";
$lang['admin_purge_j'] = "Purga de elementos viejos de %s días";
$lang['admin_purge_s'] = "Purga de elementos viejos de %s semanas";
$lang['admin_purge_m'] = "Purga de elementos viejos de %s meses";
$lang['admin_purge_y'] = "Remove records older than %s years";
$lang['admin_purge_logs'] = "Purga de los logs (Archivar cada día acabado)";
$lang['admin_purge_autres'] = "Purga comuna a la tabla '%s'";
$lang['admin_purge_none'] = "Ninguna acción posible";
$lang['admin_purge_cal'] = "Calcular y purgar (esto puede tomar algunos minutos)";
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
$lang['admin_intro'] = "Bienvenido en la sección de configuración de phpMyVisites. Puede modificar y configurar todas las informaciones relativas a su instalación. Si tiene problemas al utilizar algunas funcionalidades, no dude en visitar %s la documentación oficial de phpMyVisites %s."; // vínculo hacia la documentación
$lang['admin_configetperso'] = "Configuración general";
$lang['admin_afficherjavascript'] = "Visualizar el código javascript a incluir en las páginas";
$lang['admin_cookieadmin'] = "No tomarme en cuenta en las estadísticas";
$lang['admin_ip_ranges'] = "Don't count IP/IP ranges in the statistics";
$lang['admin_sitesenregistres'] = "Lista de los sitios registrados:";
$lang['admin_retour'] = "Vuelta";
$lang['admin_cookienavigateur'] = "Tiene la posibilidad de no ser tomado en cuenta cuando visita el sitio y de no ser contado por phpMyVisites. El método utilizado es el de los cookies, entonces esta opción sólo será válida con el navegador que usa al configurar phpMyVisites. Por supuesto puede cambiar aquí esta configuración cuando lo desea.";
$lang['admin_prendreencompteadmin'] = "Tomar en cuenta al administrador en las estadísticas (borrar el cookie)";
$lang['admin_nepasprendreencompteadmin'] = "No tomar en cuenta en administrador en las estadísticas (poner un cookie)";
$lang['admin_etatcookieoui'] = "Actualmente usted está tomado en cuenta en las estadísticas de este sitio (es la configuración de base, usted está considerado como visitante normal).";
$lang['admin_etatcookienon'] = "Actualmente usted no está tomado en cuenta en las estadísticas de este sitio (cuando visita este sitio, usted no está contabilizado por phpMyVisites).";
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
$lang['install_loginmysql'] = "Usuario MySQL";
$lang['install_mdpmysql'] = "Contraseña MySQL";
$lang['install_serveurmysql'] = "Servidor MySQL";
$lang['install_basemysql'] = "Base MySQL";
$lang['install_prefixetable'] = "Prefijo de las tablas";
$lang['install_utilisateursavances'] = "Utilizadores entendidos (facultativo)";
$lang['install_oui'] = "Sí";
$lang['install_non'] = "No";
$lang['install_ok'] = "ok";
$lang['install_probleme'] = "Problema: ";
$lang['install_problemedroitrepertoire'] = "Cannot write in the folder %s : please verify that you have the rights necessary to create files on the server (try to CHMOD 777 the folder with your FTP software (right click on the directory -> Permissions (or CHMOD))."; // Cannot access the file config.php...
$lang['install_loginadmin'] = "Usuario de acceso a la administración :";
$lang['install_mdpadmin'] = "Contraseña de acceso a la administración :";
$lang['install_chemincomplet'] = "Camino completo de acceso a phpMyVisites (de la forma 'http://www.misitio.com/carp1/carp3/phpmyvisites/'). El camino debe terminar por '<strong>/</strong>'.";
$lang['install_afficherlogo'] = "¿Mostrar el logo %s en las paginas indexadas? <strong>Autorizar el logo en su sitio Internet permitirá que phpMyVisites mejor conocido y así que su evolución sea más rápida: es una manera de agradecer el equipo quien lo programó</strong> que a dado horas de trabajo para concebir una aplicación libre y gratuita."; // %s imagen del logo
$lang['install_affichergraphique'] = "¿Visualizar los gráficos cuando se consultan las estadísticas ?";
$lang['install_valider'] = "Validar"; // a la instalación y al login
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
$lang['install_txt2'] = "Al final de la instalación, una información sera mandada al sitio oficial, <strong>en la unica meta de contabilizar el nuumero de utilizadores de phpMyVisites</strong> (por supuesto, ninguna información confidencial será transmitida). Gracias por su comprensión.";
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
$lang['moistab']['01'] = "Enero";
$lang['moistab']['02'] = "Febrero";
$lang['moistab']['03'] = "Marzo";
$lang['moistab']['04'] = "Abril";
$lang['moistab']['05'] = "Mayo";
$lang['moistab']['06'] = "Junio";
$lang['moistab']['07'] = "Julio";
$lang['moistab']['08'] = "Agosto";
$lang['moistab']['09'] = "Septiembre";
$lang['moistab']['10'] = "Octubre";
$lang['moistab']['11'] = "Noviembre";
$lang['moistab']['12'] = "Diciembre";

// Months (Graph purpose, 4 chars max)
$lang['moistab_graph']['01'] = "Ene";
$lang['moistab_graph']['02'] = "Feb";
$lang['moistab_graph']['03'] = "Marz";
$lang['moistab_graph']['04'] = "Abr";
$lang['moistab_graph']['05'] = "May";
$lang['moistab_graph']['06'] = "Jun";
$lang['moistab_graph']['07'] = "Jul";
$lang['moistab_graph']['08'] = "Ago";
$lang['moistab_graph']['09'] = "Sept";
$lang['moistab_graph']['10'] = "Oct";
$lang['moistab_graph']['11'] = "Nov";
$lang['moistab_graph']['12'] = "Dic";

// Day of the week
$lang['jsemaine']['Mon'] = "Lunes";
$lang['jsemaine']['Tue'] = "Martes";
$lang['jsemaine']['Wed'] = "Miércoles";
$lang['jsemaine']['Thu'] = "Jueves";
$lang['jsemaine']['Fri'] = "Viernes";
$lang['jsemaine']['Sat'] = "Sábado";
$lang['jsemaine']['Sun'] = "Domingo";

// Day of the week (Graph purpose, 4 chars max)
$lang['jsemaine_graph']['Mon'] = "Lun";
$lang['jsemaine_graph']['Tue'] = "Mar";
$lang['jsemaine_graph']['Wed'] = "Mier";
$lang['jsemaine_graph']['Thu'] = "Jue";
$lang['jsemaine_graph']['Fri'] = "Vie";
$lang['jsemaine_graph']['Sat'] = "Sáb";
$lang['jsemaine_graph']['Sun'] = "Dom";

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
$lang['afr'] = "África";
$lang['asi'] = "Asia";
$lang['ams'] = "América Central/Sur";
$lang['amn'] = "América del Norte";
$lang['oce'] = "Oceanía";

// Oceans
$lang['oc_pac'] = "Pacific Ocean"; // TODO : translate
$lang['oc_atl'] = "Atlantic Ocean"; // TODO : translate
$lang['oc_ind'] = "Indian Ocean"; // TODO : translate

// Countries
$lang['domaines'] = array(
    "xx" => "Desconocido",
    "ac" => "La Ascensión (isla)",
    "ad" => "Andorra",
    "ae" => "Emiratos Árabes Unidos",
    "af" => "Afganistán",
    "ag" => "Antigua y Barbuda",
    "ai" => "Anguilla",
    "al" => "Albania",
    "am" => "Armenia",
    "an" => "Antillas Neerlandeses",
    "ao" => "Angola",
    "aq" => "Antarctica",
    "ar" => "Argentina",
    "as" => "American Samoa",
    "at" => "Austria",
    "au" => "Australia",
    "aw" => "Aruba",
    "az" => "Azerbaiján",
    "ba" => "Bosnia Herzegovina",
    "bb" => "Barbada",
    "bd" => "Bangladesh",
    "be" => "Bélgica",
    "bf" => "Burkina Faso",
    "bg" => "Bulgaria",
    "bh" => "Bahrain",
    "bi" => "Burundi",
    "bj" => "Benin",
    "bm" => "Bermudas",
    "bn" => "Brunei Darussalam",
    "bo" => "Bolivia",
    "br" => "Brasil",
    "bs" => "Bahamas",
    "bt" => "Bhután",
    "bv" => "Bouvet (isla)",
    "bw" => "Botswana",
    "by" => "Bielorrusia",
    "bz" => "Belice",
    "ca" => "Canadá",
    "cc" => "Cocos (Keeling) islas",
    "cd" => "Rep. dem. del Congo",
    "cf" => "Centrafricana (Rep)",
    "cg" => "Congo",
    "ch" => "Suiza",
    "ci" => "Costa de Marfil",
    "ck" => "Cook (islas)",
    "cl" => "Chili",
    "cm" => "Camerún",
    "cn" => "China",
    "co" => "Colombia",
    "cr" => "Costa Rica",
	"cs" => "Serbia Montenegro",
    "cu" => "Cuba",
    "cv" => "Cabo Verde",
    "cx" => "Christmas (isla)",
    "cy" => "Chipre",
    "cz" => "Chequia",
    "de" => "Alemania",
    "dj" => "Yibuti",
    "dk" => "Dinamarca",
    "dm" => "Dominica",
    "do" => "Dominicana (Rep)",
    "dz" => "Argelia",
    "ec" => "Ecuador",
    "ee" => "Estonia",
    "eg" => "Egipto",
    "eh" => "Sahara Occidental",
    "er" => "Eritreo",
    "es" => "España",
    "et" => "Etiopia",
    "fi" => "Finlandia",
    "fj" => "Fiji",
    "fk" => "Falkland (Malvinas) islas",
    "fm" => "Micronesia",
    "fo" => "Faroe (islas)",
    "fr" => "Francia",
    "ga" => "Gabón",
    "gd" => "Grenada",
    "ge" => "Georgia",
    "gf" => "Guyana Francesa",
    "gg" => "Guernesey",
    "gh" => "Ghana",
    "gi" => "Gibraltar",
    "gl" => "Groenlandia",
    "gm" => "Gambia",
    "gn" => "Guinea",
    "gp" => "Guadalupe",
    "gq" => "Guinea Ecuatorial",
    "gr" => "Grecia",
    "gs" => "Georgia del Sur",
    "gt" => "Guatemala",
    "gu" => "Guam",
    "gw" => "Guinea-Bissau",
    "gy" => "Guyana",
    "hk" => "Hong Kong",
    "hm" => "Heard y McDonald (islas)",
    "hn" => "Honduras",
    "hr" => "Croacia",
    "ht" => "Haití",
    "hu" => "Hungría",
    "id" => "Indonesia",
    "ie" => "Irlanda",
    "il" => "Israel",
    "im" => "Isla de Man",
    "in" => "India",
    "io" => "Ter. Brit. Océano Indiano",
    "iq" => "Irak",
    "ir" => "Irán",
    "is" => "Islandia",
    "it" => "Italia",
    "je" => "Yérsey",
    "jm" => "Jamaica",
    "jo" => "Jordania",
    "jp" => "Japón",
    "ke" => "Kenya",
    "kg" => "Kirgizstán",
    "kh" => "Camboya",
    "ki" => "Kiribati",
    "km" => "Comores",
    "kn" => "Santo Kitts y Nevis",
    "kp" => "Corea del Norte",
    "kr" => "Corea del Sur",
    "kw" => "Kuwait",
    "ky" => "Caimanes (islas)",
    "kz" => "Kazajstán",
    "la" => "Laos",
    "lb" => "Líbano",
    "lc" => "Santa Lucia",
    "li" => "Liechtenstein",
    "lk" => "Sri Lanka",
    "lr" => "Liberia",
    "ls" => "Lesotho",
    "lt" => "Lituania",
    "lu" => "Luxemburgo",
    "lv" => "Letonia",
    "ly" => "Libyan Arab Jamahiriya",
    "ma" => "Marruecos",
    "mc" => "Mónaco",
    "md" => "Moldavia",
    "mg" => "Madagascar",
    "mh" => "Marshall (islas)",
    "mk" => "Macedonia",
    "ml" => "Malí",
    "mm" => "Myanmar",
    "mn" => "Mongolia",
    "mo" => "Macao",
    "mp" => "Mariannes del Norte (islas)",
    "mq" => "Martinica",
    "mr" => "Mauritania",
    "ms" => "Montserrat",
    "mt" => "Malta",
    "mu" => "Mauricio (isla)",
    "mv" => "Maldivas",
    "mw" => "Malawi",
    "mx" => "México",
    "my" => "Malasia",
    "mz" => "Mozambique",
    "na" => "Namibia",
    "nc" => "Nueva Caledonia",
    "ne" => "Níger",
    "nf" => "Norfolk (isla)",
    "ng" => "Nigeria",
    "ni" => "Nicaragua",
    "nl" => "Países Bajos",
    "no" => "Noruega",
    "np" => "Nepal",
    "nr" => "Nauru",
    "nu" => "Niue",
    "nz" => "Nueva Zelanda",
    "om" => "Omán",
    "pa" => "Panamá",
    "pe" => "Perú",
    "pf" => "Polinesia Francesa",
    "pg" => "Papúa Nueva Guinea",
    "ph" => "Filipinas",
    "pk" => "Pakistán",
    "pl" => "Polonia",
    "pm" => "Sto. Pierre y Miquelon",
    "pn" => "Pitcairn (isla)",
    "pr" => "Porto Rico",
    "pt" => "Portugal",
    "pw" => "Palau",
    "py" => "Paraguay",
    "qa" => "Qatar",
    "re" => "Reunión (isla de la)",
    "ro" => "Rumania",
    "ru" => "Rusia",
    "rs" => "Rusia",
    "rw" => "Rwanda",
    "sa" => "Arabia Saudí",
    "sb" => "Salomón (islas)",
    "sc" => "Seychelles",
    "sd" => "Sudán",
    "se" => "Suecia",
    "sg" => "Singapur",
    "sh" => "Sta. Helena",
    "si" => "Eslovenia",
    "sj" => "Svalbard/Jan Mayen (islas)",
    "sk" => "Eslovaquia",
    "sl" => "Sierra Leone",
    "sm" => "Saint Marín",
    "sn" => "Senegal",
    "so" => "Somalia",
    "sr" => "Surinam",
    "st" => "Sao Tome y Príncipe",
    "su" => "Ex URSS",
    "sv" => "Salvador",
    "sy" => "Siria",
    "sz" => "Swazilandia",
    "tc" => "Turks y Caiques (islas)",
    "td" => "Chad",
    "tf" => "Territorios Fr. del Sur",
    "tg" => "Togo",
    "th" => "Tailandia",
    "tj" => "Tayikistán",
    "tk" => "Tokelau",
    "tm" => "Turkmenistán",
    "tn" => "Túnez",
    "to" => "Tonga",
    "tp" => "Timor Oriental",
    "tr" => "Turquía",
    "tt" => "Trinidad y Tobago",
    "tv" => "Tuvalu",
    "tw" => "Taiwán",
    "tz" => "Tanzania",
    "ua" => "Ucrania",
    "ug" => "Uganda",
    "uk" => "Reino Unido",
    "gb" => "Gran Bretaña",
    "um" => "US Minor Outlying (islas)",
    "us" => "Estados Unidos",
    "uy" => "Uruguay",
    "uz" => "Uzbekistán",
    "va" => "Vaticano",
    "vc" => "Sto. Vicente Granadinas",
    "ve" => "Venezuela",
    "vg" => "Vírgenes Brit. (islas)",
    "vi" => "Vírgenes EEUU (islas)",
    "vn" => "Vietnam",
    "vu" => "Vanuatu",
    "wf" => "Wallis y Futuna (islas)",
    "ws" => "Western Samoa",
    "ye" => "Yemen",
    "yt" => "Mayote",
    "yu" => "Yugoslavia",
    "za" => "Sudáfrica",
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
    "ws" => "Western Samoa",
);
?>