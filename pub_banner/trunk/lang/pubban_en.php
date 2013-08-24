<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/pubban?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'actif' => 'Activated',
	'affi_txt' => 'display(s)',
	'apercu' => 'Preview',
	'apercu_indisponible' => 'Preview not available',
	'au' => ' at ',
	'aujourdhui' => 'Today',
	'auteur' => 'Author',

	// B
	'banner' => 'banner',
	'banner_banner' => 'Banner "Banner"',
	'banniere_desactivee' => 'This banner is disabled ... preview impossible.',
	'banniere_pub' => 'Banner',
	'bannieres_pub' => 'Banner(s)',
	'btn_active' => 'Enable',
	'btn_apercu' => 'Preview',
	'btn_desactive' => 'Disable',
	'btn_details' => 'Details',
	'btn_editer' => 'Edit',
	'btn_imprimer' => 'Print',
	'btn_inverser' => 'Invert the list',
	'btn_lister_empl' => 'List the banner ads',
	'btn_modifier' => 'Edit',
	'btn_reabiliter' => 'Get back',
	'btn_see_liste' => 'Show the list',
	'btn_supprimer' => 'Delete',
	'btn_voir' => 'View',

	// C
	'cacher_bordure' => 'Hide banners border',
	'campagne_date_debut' => 'Start of the campaign',
	'campagne_date_fin' => 'End of the campaign',
	'campagne_deroulement' => 'Progress of the campaign',
	'campagne_donnees_suivi' => 'Monitoring data',
	'campagne_presentation' => 'Campaign overview',
	'campagne_statistiques' => 'Statistical Analysis',
	'cf_navigation' => 'See [Navigation column->@url@]',
	'clics' => 'Hits',
	'clics_txt' => 'Hit(s)',
	'code_pub' => 'code or address of the object to display',
	'comment_code_pub' => '<em>For advertising such as type \\ "image \\" or \\ swf \', you must indicate here the url address of this image. For a flash object, you must specify the complete code ...</em>',
	'comment_dates' => 'Note the dates as \'YYYY-MM-DD\'',
	'comment_illimite' => '<em>Views and unlimited clicks ; you can specify a start date or an end one for the period display.</em>',
	'comment_multiple_empl' => ' can select multiple banners by using \'SHIFT KEY.\' on your keyboard.',
	'comment_ratio' => '(number of clicks / number of displays)',
	'comment_url_optionnel' => 'You can leave this field blank, clicking on the ad will then return to the page to purchase advertising space.',
	'confirm_delete' => 'Warning: you requested to put an advertisement in the trash ...\\n\\nPress OK to confirm:',
	'confirm_delete_empl' => 'Warning: you have asked to put banner in the trash ...\\n\\nPress OK to confirm:',
	'confirm_undelete' => 'Warning: you requested the rehabilitation of an advertisement ...\\n\\nPress OK to confirm.',
	'confirm_vider_poubelle' => 'Are you sure you want to empty the trash ?',
	'content_trash' => 'Bin content',
	'cube_banner' => '"Cube" Banner',

	// D
	'date_add' => 'Creation',
	'date_creation' => 'Creation Date',
	'date_debut' => 'Beginning date of validity',
	'date_fin' => 'Expiration date',
	'date_maj' => 'Last update',
	'dates_validite_pub' => 'Validity dates',
	'debut' => 'Start',
	'derniers_jours' => 'last days',
	'details_empl' => 'Details of a banner',
	'dimensions' => 'Size',
	'doc_chapo' => 'The plugin "Ads Banner" provides a banners management for SPIP skeletons.',
	'doc_en_ligne' => 'Plugin documentation on SPIP-contrib',
	'doc_info' => 'Please refer to the documentation for the plugin information :',
	'doc_titre_court' => 'Ad Banner Documentation',
	'doc_titre_page' => 'Documentation of plugin "Ad Banner"',
	'docskel_sep' => '----',
	'documentation_1' => 'The plugin "Ad Banner" can be installed in the same way that all SPIP plugins ({[dedicated article on spip.net->http://www.spip.net/fr_article3396.html]}).

Default values ​​are entered in the tables, namely:
-* Four "example" banners , the most popular on the web :
-** {{[skyscraper->#skyscraper]}} : long vertical banner, 160 or 180 by 600 pixels,
-** {{[leaderboard->#leaderboard]}} : the long horizontal banner, 728 by 90 pixels,
-** {{[banner->#banner]}} : {standard} horizontal banner, 468 by 60 pixels,
-** {{[cube->#cube]}} : a square of 250 by 250 pixels, ideal to insert flash banner
-* Five examples of advertising banner, one for each banner, two for the "banner", with various options whith the display limit (number og views, clicks and periods).

These banners fit in your skeletons simply stating the tag:
<cadre class="spip">
// Username "banner_id" of the banner
#PUBBAN{banner_id}

// Name of the banner
#PUBBAN{banner_name}

// ou banner ID
#PUBBAN{id_banner}
</cadre>
Followed by the name of the banner you want to display. The tag is replaced by a frame with the size of the banner.',
	'documentation_2' => 'For a practical reason [[Spip Bonux is used by Banner Pub for its functionality \'POUR\', that creates loops from the PHP array (Editor\'s note) ...]], the plugin "Ad Banner" requires you have previously installed the plugin {{Spip Bonux}} in version 1.3 minimum.

-* You can read a description of this plugin on the site Spip-Contrib : [->http://www.spip-contrib.net/SPIP-Bonux].
-* You can download it here : [->http://zone.spip.org/trac/spip-zone/browser/_plugins_/spip-bonux-2?rev=31575] ({here in version 2}).
',
	'documentation_3' => 'The plugin offers following campaign effectiveness in several ways :
-* Via the page "statistics" in the private area, which presents different graphs tracking views and clicks, according to several periods to choose for each banner,
-* Via a public page that summarizes the values ​​of each advertisement ({all, one or severam}) and allow export to CSV format ({[see for example pubs 1 and 2->@url_exemple@]}).',
	'documentation_info' => 'Documentation/Information',
	'download_flash_player' => 'The display of this object requires the Adobe Flash Player. Click here to get it for free.',
	'droits' => 'Open rights on the pub',
	'droits_aff_pub' => 'Number of displays',
	'droits_clic_pub' => 'Number of clicks',
	'droits_dates_pub' => 'Dates',

	// E
	'edit_pub_ok_bannieres_differents' => 'OK - values ​​recorded but the selected advertising banners have different sizes ... This may generate display errors.',
	'empl_is' => 'This banner is',
	'en_pixels' => '<em>(in pixels)</em>',
	'en_pourcent' => '<em>(in %)</em>',
	'en_secondes' => '<em>(in secondes)</em>',
	'erreur_code' => 'Please enter the code of advertising',
	'erreur_empl' => 'You didn\'t chose any banner for your advertising...',
	'erreur_img_not_img' => 'L\'url does not seem to capture an image ...',
	'erreur_img_not_url' => 'The web address entry is inaccessible ... ...',
	'erreur_nb_aff' => 'You didn\'t precise the number of display ...',
	'erreur_titre' => 'You must specify a title for your advertising (<em>it will appear on mouseover</em>)',
	'erreur_url' => 'You must specify a URL redirection for your advertising',
	'erreur_url_no_response' => 'The address entered does not respond ... Are you sure that it is valid ?',
	'erreur_url_not_url' => 'The address entered does not seem to be a web address ...',
	'error_dimensions_missing_empl' => 'You must specify the dimensions for your banner',
	'error_dimensions_numeric_empl' => 'It seems there was an error in dimensions',
	'error_global' => 'An error seems to have occurred...',
	'error_refresh_numeric_empl' => 'It seems that there has been an value error, you must specify a number of seconds',
	'error_titre_empl' => 'You must specify a title for your banner',
	'evo_empl' => 'Performance trends',
	'exemples_par_defaut' => 'Examples (default banners)',
	'exporter' => 'Export',
	'exporter_csv' => 'Export data in CSV format',
	'exporter_donnees' => 'Export data',

	// F
	'fermer' => 'Close',
	'fiche' => 'File',
	'fin' => 'End',

	// G
	'gestion_pubban' => 'Banners management',

	// H
	'height' => 'Height',
	'home' => 'Back to the ad manager',

	// I
	'icone_banniere' => 'banner',
	'icone_bannieres' => 'Banners',
	'icone_modifier_banniere' => 'Edit the banner',
	'icone_modifier_publicite' => 'Edit this advertising',
	'icone_nouvelle_banniere' => 'Create a new banner',
	'icone_nouvelle_publicite' => 'Create a new advertising',
	'icone_publicite' => 'Advertising',
	'icone_publicites' => 'Advertisings',
	'illimite' => 'Unlimited rights',
	'imprimer' => 'Print',
	'inactif' => 'Inactivated',
	'inactive' => 'Inactivated',
	'info_1_banniere' => 'A banner has been found',
	'info_1_publicite' => 'An advertisement has been found',
	'info_aucune_banniere' => 'No banner has been found',
	'info_aucune_publicite' => 'No advertising has been found',
	'info_banniere' => 'Banner status',
	'info_banniere_active' => 'Active banner',
	'info_banniere_inactive' => 'Inactive banner',
	'info_banniere_poubelle' => 'banner in the bin',
	'info_doc' => 'If you are having problems viewing this page, [click here->@link@].',
	'info_doc_titre' => 'Note on the display of this page',
	'info_evo' => '10 blocs * 10 days (100 last days)',
	'info_nb_bannieres' => '@nb@ banners were found',
	'info_nb_publicites' => '@nb@ ads were found',
	'info_publicite_active' => 'Active advertising',
	'info_publicite_creee' => 'Created advertising',
	'info_publicite_inactive' => 'Inactive advertising',
	'info_publicite_obsolete' => 'Obsolete advertising',
	'info_publicite_poubelle' => 'Advertising in the bin',
	'info_publicite_rompue' => 'Advertisement broken',
	'info_ratio' => 'Ratio (clicks/views)',
	'info_ratio_banniere' => 'Banner ratio (<em>optional</em>)',
	'info_refresh_banniere' => 'Refresh time',
	'info_search_box' => '<em>Search</em> > enter a reference, a word or phrase to search',
	'info_skel_doc' => 'This documentation page is designed as a SPIP skeleton working with the standard distribution ({files from "squelettes-dist/"}). If you are unable to view the page, or if your site uses its own skeletons, you can manage its display with the links below  :

-* [Mode "plain text"->@mode_brut@] ({simple html + INSERT_HEAD tag})
-* [Mode "skeleton Zpip"->@mode_zpip@] ({Z skeleton Compatible})
-* [Mode "SPIP skeleton"->@mode_spip@] ({distribution Compatible})',
	'info_stats' => 'Some figures ...',
	'info_statut_banniere_1' => 'This banner is :',
	'info_statut_publicite_1' => 'This ad is :',
	'info_taille_banniere' => 'Size of the banner',
	'info_titre_banniere' => 'banner title',
	'info_titre_banniere_active' => 'Active',
	'info_titre_banniere_inactive' => 'Inactive',
	'info_titre_banniere_poubelle' => 'In the bin',
	'info_titre_id_banniere' => 'Banner_ID',
	'info_titre_id_comment' => 'If the field is empty, this value will be generated from the title. <em>It is strongly recommended not to use accents or special characters, it may cause an error while calling PUBBAN tag...</em>',
	'info_titre_publicite_active' => 'Active',
	'info_titre_publicite_creee' => 'Created',
	'info_titre_publicite_inactive' => 'Inactive',
	'info_titre_publicite_obsolete' => 'Obsolete',
	'info_titre_publicite_poubelle' => 'In the bin',
	'info_titre_publicite_rompue' => 'Broken link',
	'infos_pub' => 'Advertising content',
	'infos_pubban' => 'Information and advice ...',
	'infos_texte' => '{{Advertising area performance space depends primarily on two components:
-* Its format,
-* Its position on the page.}}

{{{Format}}}

Generally, larger ad formats get superior performance due to their usability.
The information is more easily assimilated because the reader can read more text without changing of line.

{{{Positioning}}}

Numerous statistical studies show that the banners at the top of a web page, top of a page and top of content have better performance.

{{The advertising standards provide the information to propose ad banner or build ad.}}

{{{Classic banners sizes & maximum weight}}}

-* {{Banner}} : 468x60 px | 35 Ko
-* {{Skyscraper}} : 120x600 px | 50 Ko
-* {{Pad}} : 300x250 px | 50 Ko
-* {{Square}} : 250x250 px | 50 Ko
-* {{le Button}} (logos ...) : up up 120 px (120x60 px)

{{{Tips}}}

- * Files available for advertisements must be less than 50 KB, not to interfere with the loading of the page content.
- * For animations, it is advisable to recommend images of 15 seconds maximum.

{{{Rates}}}
{{Two main pricing methods :}}

-* {{CPM - cost per thousand}} ({views}) is the most used,  it seems to have become a standard in the area.
-* {{CPC - cost per click}}, just behind, it is more difficult to quantify.

{{Package}} is also used for non-standard campaigns: embedding in pages, intrusive promotional products, active campaign...
',
	'installation' => 'Installation',
	'integer_edit' => 'Banner edition ',
	'intro_admin' => '<em>ad banners</em> management',
	'intro_integer' => 'The <em>banners</em> : ad banners',
	'intro_integer_edit' => 'Banner edition',
	'intro_integer_edit_texte' => 'To call a banner in your skeletons, enter the tag: <br /><center><b># PUBBAN{banner_id}</b></center><br />If you leave the field "Banner ID" empty, it will be automatically generated using the title and replace spaces with <b>underscore</b>.<br /><br /><em>We draw your attention to the comments about the "banner_id" of the banners : <u>avoid special characters</u> ! If you want to use, do many tests before putting it online ...</em>',
	'intro_integer_texte' => 'Here is the list of banners listed on the site.<br />Banners are mainly characterized by their size and position in your skeletons.<br /><br />Here you can <b>activate</b> or <b>disable</b> them, <em>throw them in the trash</em>, and <b>edit them</b> ...<br />',
	'intro_pub' => 'Advertising inserts',
	'intro_pub_edit' => 'Advertising inserts edition',
	'intro_pub_edit_texte' => 'This page allows you to insert or modify an advertisement under specific conditions :<ul><li>for a precise <b>number of clicks</b>,</li><li>to set a <b>number of views</b> ,</li><li>according to <b>specific validity dates</b>.</li></ul>',
	'intro_pub_texte' => 'Here is the list of advertisements placed on the site.<br /><br />Here you can <b>activate</b> or <b>disable</b> them, <em>throw them in the trash</em>, <b>edit them</b> and <b>get a preview</b> ...<br /><br /> <em>Obsolete</em> ads have an exceeded validity : Number of clicks orviews obtained, past dates.',
	'intro_stats' => 'Statistics',
	'intro_stats_banner' => 'Reading statistics',
	'intro_stats_pub' => 'Reading statistics',
	'intro_texte_stats_banner' => 'Statistics figures allow to estimate effectiveness of advertisements : particularly, <b>the overall ratio</b> indicates the number of clicks per the number of display inserts.<br /><br />It is interesting to compare the ratios of banners based on their particular position.<br /><br /><em>(cf. LICENSES at the bottom of the page)</em><br />',
	'intro_texte_stats_pub' => '',

	// L
	'leaderboard_banner' => 'Banner "Leaderboard"',
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] licensed under [Creative Commons BY-SA|Creative Commons - Paternite - Identical distribution ->http://creativecommons.org/licenses/by-sa/3.0/].',
	'licence_stats' => '{{LICENSES :}}<br />{{\'wz_jsgraphics.js\'}} :: v. 2.33 - (c) 2002-2004 Walter Zorn ([www.walterzorn.com->http://www.walterzorn.com])<br />{{\'graph.js\', \'line.js\' & \'pie.js\'}} :: (c) Balamurugan S. 2005 ([www.jexp.com->http://www.jexp.com])',
	'lien_page' => 'View the page',
	'list_empl' => 'List of banners',
	'liste_pub' => 'List of ads',
	'listing_empl' => 'List of ads of this banner',

	// M
	'manque_date_fin' => 'Please specify an ending date',

	// N
	'nb_affichages' => 'Total displays',
	'nb_affires_pub' => 'Displays remaining',
	'nb_bannieres' => 'Number of banners',
	'nb_clicres_pub' => 'Number of clicks remaining',
	'nb_clics' => 'Total clicks',
	'nb_pub' => 'Total advertising',
	'nb_pub_actives' => 'Active ones',
	'nb_pub_inactives' => 'Inactive ones',
	'nb_pub_obsoletes' => 'Obsoletesones ',
	'new_window' => 'New Window',
	'no_clic_for_emp' => 'Some locations are not shown in the graph because it doesn\'t have any click in the selected period.',
	'no_clic_in_period' => 'There was no click in the selected period.',
	'no_datas_yet' => 'There \' s not yet usable statistic ...',
	'no_empl_found' => 'Banner not found ...',
	'no_empl_yet' => 'There \' s not yet a configured banner ...',
	'no_limit' => 'unlimited',
	'no_pub_active_yet' => 'There \' s not yet an activated advertising ...',
	'no_pub_found' => 'Advertising not found ...',
	'no_pub_yet' => 'There \' s not yet registered advertising ...',
	'no_results_match' => 'No entries match your search.',
	'non' => 'No',
	'nouveau_empl' => 'Create a new banner',
	'nouveau_pub' => 'Create a new advertising',
	'nouveau_pub_dans_banniere' => 'Add a new advertisement',
	'num_version_base' => 'Version of SQL tables',
	'num_version_svn' => 'SVN revision number',

	// O
	'obsolete' => 'Obsolete',
	'open_trash' => 'Open the bin',
	'oui' => 'Yes',
	'outils' => 'Tools',

	// P
	'page_infos' => 'Advice and information',
	'page_stats' => 'Statistics page',
	'pas_banniere_selectionne' => 'You didn\'t select any banner ...',
	'perf_empl' => 'Banners performance',
	'period' => 'Period from ',
	'plugin_spip' => 'A plugin for <b>SPIP 2.0+</b>',
	'poubelle' => 'In the bin',
	'poubelle_contenu' => 'Bin content',
	'pour' => 'For',
	'pratique' => 'In practice',
	'prerequis' => 'Required',
	'pub' => 'ad(s)',
	'pub_actives' => 'List of active ads',
	'pub_edit' => 'Edit an advertising',
	'pub_inactives' => 'List of inactive ads',
	'pub_is' => 'This ad is',
	'pub_obsoletes' => 'List of obsolete ads',
	'pubban' => 'Banner Ads',
	'pubban_stats_banner' => 'Banners statistics',
	'pubban_stats_pub' => 'Ads statistics',
	'pubban_titre' => 'Banners',
	'publicite_apercu' => 'Ad overview',

	// R
	'ratio' => 'Ratio (clicks / views)',
	'ratio_comment' => 'Ratio pages with banner / total pages.',
	'ratio_pages' => 'Pages ratio (visibility)',
	'ratio_txt' => 'Ratio',
	'refresh_comment' => 'Time after which the banner content is refreshed, to cancel this option, set the value <code>0</code>.',
	'refresh_time' => 'Refresh Time',
	'reponse_form_def_droits' => 'Please enter the advertising rights for (on one line)',
	'result_match' => 'entry matches your search.',
	'resultats_du' => 'Results of the last analysis ',
	'results_match' => 'entries match your search.',
	'retirer_arg' => 'Remove from page',
	'retour_liste_empl' => 'Back to the list of banners',
	'retour_liste_pub' => 'Back to the complete list of ads',
	'retour_search' => 'Back to Search',

	// S
	'search_pubban' => 'Search the banners',
	'search_results' => 'Search results',
	'secondes' => 'secondes',
	'see_doc' => 'See the documentation (internal)',
	'see_doc_in_new_window' => 'Open the documentation in new window',
	'see_doc_in_texte_brut' => 'See the documentation in plain text (skeletal problems)',
	'select_articles_choose' => '&gt; list of your articles',
	'site_web' => 'Website',
	'skyscraper_banner' => 'Banner "Skyscraper"',
	'statistiques' => 'Statistics',
	'statistiques_pubban' => 'Banner statistics',
	'stats' => 'Statistical data',
	'stats_pubban' => 'Ads statistics',
	'statut' => 'Status',
	'statut_actuel' => 'Current Status',

	// T
	'target_blank' => 'Redirection in a new window',
	'target_parent' => 'Redirection in current window',
	'testing_page_code' => 'Code : ',
	'texte_admin' => 'The ads are organized in different <b>banners</b>, the object displayed in the skeletons, have each an ad pannel, <b>inserts</b>, which are displayed alternately at every banner call.<br /><br />The plugin stores display numbers and clicks on each insert, to present <b>detailed statistics</b>, useful to estimate the performance of banners.',
	'texte_brut' => 'Plain text',
	'titre' => 'Title',
	'titre_cadre_ajouter_empl' => 'Banner creation',
	'titre_cadre_ajouter_pub' => 'New ad creation',
	'titre_cadre_modifier_empl' => 'Ad edition',
	'titre_cadre_modifier_pub' => 'Edition of an ad',
	'titre_info_empl' => 'BANNER NUMBER :',
	'titre_info_pub' => 'ADVERTISING NUMBER :',
	'titre_nouvel_empl' => 'NEW BANNER',
	'titre_tablo_banniere' => 'Banner',
	'titre_tablo_code' => 'Html code to display object',
	'titre_tablo_date' => 'Adding date',
	'titre_tablo_nom' => 'Title of advertising',
	'titre_tablo_url' => 'URL redirection (by clicking)',
	'trash_is_empty' => 'The bin is empty',
	'type' => 'Type',
	'type_empl' => 'Banner',
	'type_encart' => 'Insert',
	'type_flash' => 'another flash object',
	'type_img' => 'Object of image type',
	'type_swf' => 'Flash object .swf',

	// U
	'url_pub' => 'URL redirection (by clicking)',
	'url_stats_banniere' => 'public URL of the banner statistics :',
	'url_stats_publicite' => 'public URL of the ad statistics :',
	'url_traceur' => 'URL of the SVN development tracer (spip-zone)',
	'url_update' => 'download URL',

	// V
	'valider_pour_forcer' => 'Confirm again to force to record this value ...',
	'vider_trash' => 'Empty trash',
	'view_pub' => 'Details of an advertising insert',
	'voir_bordure' => 'See the borders banners',
	'voir_les_statistiques' => 'Show Stats (popup window)', # MODIF
	'voir_page' => '<br /><b>See the page :</b>',
	'voir_un_apercu' => 'See a preview (popup window)', # MODIF

	// W
	'width' => 'Width'
);

?>
