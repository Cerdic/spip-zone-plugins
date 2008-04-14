<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// actijour 1.54 - 4/2008
// "... et comme disait geraldine : cause mieux la france Scoty !"

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
'abrv_administrateur' => 'Admin.', ## 1.53
'abrv_redacteur' => 'redac.', ## 1.53
'abrv_visiteur' => 'Visit.', ## 1.53
'activite_du_jour' => 'Daily activity',
'afficher_stats_art' => 'Show stats of News',
'article_inexistant' => ':: <i>no Article</i> ::',
'aucun' => '&nbsp;None&nbsp;',
'aucun_article_visite' => 'Not any visited News till now',
'aucun_auteur_en_ligne' => 'No changes since 15 mn!<br />Except you!',
'aucune_moment' => 'None at this time!',
'auteurs_en_ligne' => 'were online last 15 min:',

// B
'bargraph_trimestre_popup' => 'Popup trimester Bargraph',

// C
'configuration_commune' => 'Common Configuration', ## 1.53
'configuration_perso' => 'Private Configuration of: @nom@', ## 1.53
'conf_nbl_art' => 'Number of lines per parts, of the array of the daily visited news.', ## 1.53
'conf_nbl_aut' => 'Number of authors shown in the
					"Last connections" array.', ## 1.53
'conf_nbl_mensuel' => 'Number of months shown in the array of monthly visits(gauges).', ## 1.53
'conf_nbl_topgen' => 'TopTen page. Move your Topten array "General Topten" into Top-15, 20..<br />
						Fill the number of news to show.', ## 1.53
'conf_nbl_topmois' => 'TopTen page. Move the array "30 days Topten" into Top-15, 20..<br />
						Fill the number of news to show.', ## 1.53
'conf_nbl_topsem' => 'TopTen Page. Move the array "8 days Topten" into Top-15, 20..<br />
						Fill the number of news to show.', ## 1.53
'conf_ordon_milieu' => 'Move the order of the center column blocks on the main page: "Daily activity".<br />
						- news array: <b>1</b><br />
						- sectors array: <b>2</b><br />
						- 8 days visits array: <b>3</b><br />
						- link in list: <b>4</b><br />
						Fill the block numbers separated by comma
						(eg.: 1,2,3,4).', ## 1.53

// D
'date_jour_maxi_vis' => 'The @date_max@: @visites_max@ vis.',
'depuis_date_visites_pg' => 'Since @heure@ @date@, @nb_visite@ Visits
							out of @nb_articles@ news
							(see Forecasts).', ## 1.53
'depuis_date_visites_prev' => 'Since @heure@ @date@, @nb_visite@ visits
							out of @nb_articles@ news.', ## 1.53
'depuis_le_prim_jour' => 'Since the <b>@prim_jour_stats@</b>',
'dernieres_connections' => 'Last Connections*',
'info_dernieres_connections' => '*The 20 last connections within this site\'s "Authors"', ## 1.53 modif

// E
'entete_tableau_art_jour' => '<b>@nb_art_visites_jour@ visited News</b>,
								or "hits"<b> @aff_date_now@</b>',
'entete_tableau_art_hier' => '<b>@nb_art_visites_jour@ visited News</b>,
								or "hits", <b>yesterday @aff_date_now@</b>',
'entete_tableau_mois' => '<i><b>For your info... by figures!</b></i><br />The visits by @nb_mois@ monthes.',

// G
'global_vis_hier' => 'Yesterday: <b>@global_jour@</b>',
'global_vis_jour' => 'Day: <b>@global_jour@</b>',
'global_vis_global' => 'Global: <b>@global_stats@</b>',
'graph_trimestre' => 'Trimester Graph:',
'grosse_journee_' => 'Biggest day...',

// H
'huit_derniers_jours' => 'Last 8 days...',
'haut_page' => 'Top of page', ## 1.53

// I
'info_colonnes_topten' => 'Info about array columns:<br />
							A - News id.<br />
							B - News title.<br />
							C - Total nr of visits of the moment.<br />
							D - Maximum visits (1 day) of the moment.<br />
							E - Total visits of the news.',
'info_page_actijour_prev' => 'Visits and figures not computed still in SPIP database',

// J
'jour' => 'Day',

// L
'liens_entrants_jour' => 'Incoming links',

// M
'message' => '&nbsp;message@ps@&nbsp;',
'mise_a_jour' => 'Update',
'mois_pipe' => 'Month |',
'moyenne_c' => 'Aver.',
'moyenne_mois' => 'Aver/month',

// N
'nombre_art' => 'News ID',
'nombre_visites_' => 'Amount of visits..',
'numero_' => 'number: &nbsp;',
'numero_court' => 'N&deg;',

// O
'onglet_actijour_hier' => 'Yesterday', ## 1.53
'onglet_actijour_pg' => 'Daily activity', ## 1.53
'onglet_actijour_prev' => 'forecasts', ## 1.53
'onglet_actijour_top' => 'TopTen', ## 1.53
'onglet_actijour_conf' => 'Config', ## 1.53

// P
#'page_activite' => 'Activit&eacute du jour',##1.53
'pages_article_vues' => 'Viewed News Pages',
'pages_art_cumul_jour' => 'day: <b>@cumul_vis_art_jour@</b> p.',
'pages_art_moyenne_jour' => 'So <b>@moy_pages_jour@</b> p./visit',
'pages_global_cumul_jour' => 'Global: <b>@global_pages_stats@</b> p.',
'pages_global_moyenne_jour' => 'So <b>@moy_pag_vis@</b> p./visit',
#'page_hier' => 'Hier',##1.53
'page_phpinfo' => 'phpinfo Page',
'pied_tableau_mois' => '* does not include today.',
#'page_topten' => 'Page TopTen',##1.53
'popularite' => 'Popul.',

//R
'repartition_visites_secteurs' => 'Visits of the day per Sectors/rubrics du jour (viewed news pages)', ## 1.53

// S
's' => 's',
'signature_plugin' => '<b>Daily activity - @version@</b><br />(4/10 - 2007/12)<br />
						Fews tricks... to have another view of visits.<br />
						By Scoty - <a href=\'http://www.koakidi.com\'>koakidi.com</a><br />
						<br />This is not useless!<br />But... !',
'signatures_petitions' => 'Petitions signatures',
'soit_nbre_jours' => 'So <b>@nb_jours_stats@ days</b>',
'soit_moyenne_par_jour' => 'So (raw) <b>@moy_global_stats@</b> visit/d',
'stats_actives_' => 'Active Stats...',

// T
'telechargements_dpt' => 'Downloads:', ## 1.53
'title_vers_page_graph' => 'To SPIP stats',
'title_vers_popup_graph' => 'Popup graphic stats',
'titre_actijour' => 'Dayly activity',
'titre_article' => 'News title',
'top_ten_article_8_j' => 'news TopTen over 8 days',
'top_ten_article_30_j' => 'news TopTen over 30 days',
'top_ten_article_gen' => 'General TopTen of news',
'total_visites' => 'TT Vis.',

// V
'visites_jour' => 'v. day', // faire court
'visites' => 'Visits',
'voir' => 'see',
#'voir_gafospip' => 'Voir page GAFoSPIP',##1.53
'voir_plugin' => 'See page: ', ## 1.53
'voir_suivi_forums' => 'see page forum management',
'voir_suivi_petitions' => 'See page Petitions management',

// Z
'z' => 'z'

);

?>
