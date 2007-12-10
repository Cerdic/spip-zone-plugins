<?php
/*
|
*/

#
# Les lignes qui suivent servent à definir 
# les champs extra et/ou leur equivalent en table.
#

# def des champs supplementaire pour ce plugin
# nom de variable generique : champs_sap_[prefix_plugin]
# sur champ de type radio, mettre valeur par defaut en premier !

$GLOBALS['champs_sap_spipbb'] = array(
	"date_crea_spipbb" => array(
		"info" => _L('date de premiere saisie profil SpipBB'), ## petit texte infos pour SAP
		"sql" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"filtres_recup" => "", ## filtrage dans fichier (balise/) recup saisie
		#"form_milieu" => "hidden", ## type input, sur auteur_infos.php
		"extra" => "hidden|brut|"._T('spipbb:avatar_saisie_url'), ## pour usage Extra et form prive
		"extra_proposes" => "tous,6forum"
	),
	"avatar" => array(
		"info" => _L('URL de l\'avatar du visiteur'),
		"sql" => "VARCHAR(255) NOT NULL",
		"filtres_recup" => "corriger_caracteres",
		#"form_milieu" => "text",
		"extra" => "ligne|propre|"._T('spipbb:avatar_saisie_url'),
		"extra_proposes" => "6forum"
	),
	"signature_post" => array(
	 	"info" => _L('Court texte de signature des messages'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:signature_saisie_texte'),
	 	"extra_proposes" => "tous,6forum"
	 ),
	"annuaire_forum" => array(
		"info" => _L('Permet de refuser l\'affichage dans l\'annuaire des inscrits en zone public'),
	 	"sql" => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	 	"filtres_recup" => "",
	 	#"form_milieu" => "radio",
	 	"extra" => "radio|brut|"._T('spipbb:visible_annuaire')."|"._T('non').","._T('oui')."|non,oui",
	 	"extra_proposes" => "tous,6forum"
	),
	"Localisation" => array(
		"info" => _L('Localisation du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:localisation'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Emploi" => array(
		"info" => _L('Emploi du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:emploi'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Loisirs" => array(
		"info" => _L('Loisirs du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:loisirs'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Numero_ICQ" => array(
		"info" => _L('Numero ICQ du visiteur'),
	 	"sql" => "VARCHAR(14) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:numero_icq'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_AIM" => array(
		"info" => _L('Nom AIM du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_aim'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_Yahoo" => array(
		"info" => _L('Nom Yahoo du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_yahoo'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_MSNM" => array(
		"info" => _L('Nom MSNM du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_msnm'),
	 	"extra_proposes" => "tous,6forum"
	),
	"refus_suivi_thread" => array(
		"info" => _L('Liste des threads pour lesquels on ne souhaite plus recevoir de notification'),
		"sql" => "TEXT DEFAULT '' NOT NULL",
		"filtres_recup" => "",
		#"form_milieu" => "hidden",
		"extra" => "hidden|brut|"._T('spipbb:refus_suivi_thread'),
		"extra_proposes" => "tous,6forum"
	)
);


?>
