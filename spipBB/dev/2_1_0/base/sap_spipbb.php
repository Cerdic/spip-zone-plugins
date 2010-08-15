<?php
/*
|
*/

if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);
#
# Les lignes qui suivent servent à definir
# les champs extra et/ou leur equivalent en table.
#

# def des champs supplementaire pour ce plugin
# nom de variable generique : champs_sap_[prefix_plugin]
# sur champ de type radio, mettre valeur par defaut en premier !
# attention a reseter en minuscules pour les declarations

$GLOBALS['champs_sap_spipbb'] = array(
	"date_crea_spipbb" => array(
		"info" => _T('spipbb:extra_date_crea_info'), ## petit texte infos pour SAP
		"sql" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"filtres_recup" => "", ## filtrage dans fichier (balise/) recup saisie
		#"form_milieu" => "hidden", ## type input, sur auteur_infos.php
		"extra" => "hidden|brut|"._T('spipbb:extra_date_crea'), ## pour usage Extra et form prive
		"extra_proposes" => "tous,6forum"
	),
	"avatar" => array(
		"info" => _T('spipbb:extra_avatar_saisie_url_info'),
		"sql" => "VARCHAR(255) NOT NULL",
		"filtres_recup" => "corriger_caracteres",
		#"form_milieu" => "text",
		"extra" => "ligne|propre|"._T('spipbb:extra_avatar_saisie_url'),
		"extra_proposes" => "6forum"
	),
	"signature_post" => array(
	 	"info" => _T('spipbb:extra_signature_saisie_texte_info'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_signature_saisie_texte'),
	 	"extra_proposes" => "tous,6forum"
	 ),
	"annuaire_forum" => array(
		"info" => _T('spipbb:extra_visible_annuaire_info'),
	 	"sql" => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	 	"filtres_recup" => "",
	 	#"form_milieu" => "radio",
	 	"extra" => "radio|brut|"._T('spipbb:extra_visible_annuaire')."|"._T('non').","._T('oui')."|non,oui",
	 	"extra_proposes" => "tous,6forum"
	),
	"localisation" => array(
		"info" => _T('spipbb:extra_localisation'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_localisation'),
	 	"extra_proposes" => "tous,6forum"
	),
	"emploi" => array(
		"info" => _T('spipbb:extra_emploi'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_emploi'),
	 	"extra_proposes" => "tous,6forum"
	),
	"loisirs" => array(
		"info" => _T('spipbb:extra_loisirs'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_loisirs'),
	 	"extra_proposes" => "tous,6forum"
	),
	"numero_icq" => array(
		"info" => _T('spipbb:extra_numero_icq'),
	 	"sql" => "VARCHAR(14) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_numero_icq'),
	 	"extra_proposes" => "tous,6forum"
	),
	"nom_aim" => array(
		"info" => _T('spipbb:extra_nom_aim'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_nom_aim'),
	 	"extra_proposes" => "tous,6forum"
	),
	"nom_yahoo" => array(
		"info" => _T('spipbb:extra_nom_yahoo'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_nom_yahoo'),
	 	"extra_proposes" => "tous,6forum"
	),
	"nom_msnm" => array(
		"info" => _T('spipbb:extra_nom_msnm'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:extra_nom_msnm'),
	 	"extra_proposes" => "tous,6forum"
	),
	"refus_suivi_thread" => array(
		"info" => _T('spipbb:extra_refus_suivi_thread_info'),
		"sql" => "TEXT DEFAULT '' NOT NULL",
		"filtres_recup" => "",
		#"form_milieu" => "hidden",
		"extra" => "hidden|brut|"._T('spipbb:extra_refus_suivi_thread'),
		"extra_proposes" => "tous,6forum"
	)
);


?>
