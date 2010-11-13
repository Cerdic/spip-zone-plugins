<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Site anlegen',

	// C
	'cfg_description_piwik' => 'Hier k&ouml;nnen sie ihre PIWIK-Anmeldedaten sowie die Adresse des Servers f&uuml;r ihre Statistiken angeben.',
	'cfg_erreur_recuperation_data' => 'Kommunikation mit dem Server gescheitert, bitte &uuml;berpr&uuml;fen sie die Adresse und das Token.',
	'cfg_erreur_token' => 'Ihre Token-ID ist ung&uuml;ltig.',

	// E
	'explication_adresse_serveur' => 'Geben sie die Adresse ohne "http://" oder "https://" und ohne Slash "/" am Ende an.',
	'explication_creer_site' => 'Mit diesem Link k&ouml;nnen sie auf dem PIWIK-Server eine Site anlegen, welche dann in der Liste angezeigt wird. Bitte pr&uuml;fen sie, ob sie den Namen und die Adresse ihrer SPIP-Website richtig eingetragen haben, denn diese Daten werden an PIWIK &uuml;bertragen.',
	'explication_exclure_ips' => 'Um mehrere Adressen auszuschlie&szlig;en, trennen sie sie bitte mit einem Semikolon.',
	'explication_identifiant_site' => 'Die Liste der auf dem PIWIK-Server verf&uuml;gbaren Websites wurde aufgrund der angegebenen Informationen &uuml;bertragen. Bitte w&auml;hlen sie in der untenstehenden Liste die gew&uuml;nschte Site aus.',
	'explication_mode_insertion' => 'Es gibt zwei Methoden, den f&uuml;r das Funktionieren des Plugins erforderlichen Code in die Seiten einzuf&uuml;gen: Mit der Pipeline "indert_head" (vollautomatisch aber ohne erweiterte Konfigurationsm&ouml;glichkeiten) oder durch Einf&uuml;gen des Tags #PIWIK in den Fu&szlig;bereich ihrer Skelette (dann k&ouml;nnen sie alle Konfigurationsoptionen nutzen).',
	'explication_restreindre_statut_prive' => 'W&auml;hlen sie hier den Status der Besucher, deren Zugriffe auf das Redaktionssystem nicht in der Statistik erfa&szlig;t werden',
	'explication_restreindre_statut_public' => 'W&auml;hlen sie hier den Status der Besucher, deren Zugriffe auf den &ouml;ffentlichen Teil der Website nicht in der Statistik erfa&szlig;t werden',
	'explication_token' => 'Das Identifikations-Token finden sie in ihren pers&ouml;nlichen Einstellungen oder im API-Bereich ihres PIWIK-Servers.',

	// L
	'label_adresse_serveur' => 'Adresse (URL) des Servers (https:// oder http://)',
	'label_comptabiliser_prive' => 'Abrufe des Redaktionssystems erfassen',
	'label_creer_site' => 'Eine Site auf dem Piwik-Server anlegen',
	'label_exclure_ips' => 'Bestimmte IP-Adressen ausschlie&szlig;en',
	'label_identifiant_site' => 'ID ihrer Website auf dem Piwik-Server',
	'label_mode_insertion' => 'Typ des Einf&uuml;gens in die &ouml;ffentlichen Seiten',
	'label_restreindre_auteurs_prive' => 'Einschr&auml;nkungen f&uuml;r manche angemeldeten Besucher (Redaktion)',
	'label_restreindre_auteurs_public' => 'Einschr&auml;nkungen f&uuml;r manche angemeldeten Besucher (&ouml;ffentlicher Bereich)',
	'label_restreindre_statut_prive' => 'Einschr&auml;nkungen f&uuml;r manche Mitglieder im Redaktionssystem',
	'label_restreindre_statut_public' => 'Einschr&auml;nkungen f&uuml;r manche Mitglieder im &ouml;ffentlichen Bereich',
	'label_token' => 'ID-Token auf dem Server',

	// M
	'mode_insertion_balise' => 'Einf&uuml;gen mit dem Tag #PIWIK (erfordert &Auml;nderung ihrer Skelette)',
	'mode_insertion_pipeline' => 'Automatisches Einf&uuml;gen mit der Pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Ihre ID',
	'textes_url_piwik' => 'Ihr Piwik-Server'
);

?>
