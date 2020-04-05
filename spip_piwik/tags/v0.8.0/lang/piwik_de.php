<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/piwik?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Site anlegen',
	'action_recuperer_liste' => 'Liste der Websites abrufen',

	// C
	'cfg_description_piwik' => 'Hier können sie ihre PIWIK-Anmeldedaten sowie die Adresse des Servers für ihre Statistiken angeben.',
	'cfg_erreur_recuperation_data' => 'Kommunikation mit dem Server gescheitert, bitte überprüfen sie die Adresse und das Token.',
	'cfg_erreur_token' => 'Ihre Token-ID ist ungültig.',
	'cfg_erreur_user_token' => 'Bneutzername und Token stimmen nicht überein',

	// E
	'explication_adresse_serveur' => 'Geben sie die Adresse ohne "http://" oder "https://" und ohne Slash "/" am Ende an.',
	'explication_conformite_cnil' => 'Fügt eine <a href="http://www.cnil.fr/fileadmin/documents/approfondir/dossier/internet/Configuration_piwik.pdf">Javascript-Funktion</a> hinzu, mit der die <a href="http://www.cnil.fr/vos-obligations/sites-web-cookies-et-autres-traceurs/outils-et-codes-sources/la-mesure-daudience/"> CNIL-Regeln </a> zu Cookies umgesetzt werden.',
	'explication_creer_site' => 'Mit diesem Link können sie auf dem PIWIK-Server eine Site anlegen, welche dann in der Liste angezeigt wird. Bitte prüfen sie, ob sie den Namen und die Adresse ihrer SPIP-Website richtig eingetragen haben, denn diese Daten werden an PIWIK übertragen.',
	'explication_exclure_ips' => 'Um mehrere Adressen auszuschließen, trennen sie sie bitte mit einem Semikolon.',
	'explication_identifiant_site' => 'Die Liste der auf dem PIWIK-Server verfügbaren Websites wurde aufgrund der angegebenen Informationen übertragen. Bitte wählen sie in der untenstehenden Liste die gewünschte Site aus.',
	'explication_mode_insertion' => 'Es gibt zwei Methoden, den für das Funktionieren des Plugins erforderlichen Code in die Seiten einzufügen: Mit der Pipeline "insert_head" (vollautomatisch aber ohne erweiterte Konfigurationsmöglichkeiten) oder durch Einfügen des Tags #PIWIK in den Fußbereich ihrer Skelette (dann können sie alle Konfigurationsoptionen nutzen).',
	'explication_recuperer_liste' => 'Mit dem folgenden Link kann eine Liste der Websites abgerufen werden, die mit Ihrem Konto auf dem PIWIK-Server verwaltet werden können.',
	'explication_restreindre_statut_prive' => 'Wählen sie hier den Status der Besucher, deren Zugriffe auf das Redaktionssystem nicht in der Statistik erfaßt werden',
	'explication_restreindre_statut_public' => 'Wählen sie hier den Status der Besucher, deren Zugriffe auf den öffentlichen Teil der Website nicht in der Statistik erfaßt werden',
	'explication_token' => 'Das Identifikations-Token finden sie in ihren persönlichen Einstellungen oder im API-Bereich ihres PIWIK-Servers.',

	// I
	'info_aucun_site_compte' => 'Ihrem PIWIK-Konto sind keine Websites zugeordnet.',
	'info_aucun_site_compte_demander_admin' => 'Sie müssen einen Administrator des PIWIK-Servers bitten, Ihrem Konto eine Website hinzuzufügen.',

	// L
	'label_adresse_serveur' => 'Adresse (URL) des Servers (https:// oder http://)',
	'label_comptabiliser_prive' => 'Abrufe des Redaktionssystems erfassen',
	'label_conformite_cnil' => 'CNIL-Regeln',
	'label_creer_site' => 'Eine Site auf dem Piwik-Server anlegen',
	'label_exclure_ips' => 'Bestimmte IP-Adressen ausschließen',
	'label_identifiant_site' => 'ID ihrer Website auf dem Piwik-Server',
	'label_mode_insertion' => 'Typ des Einfügens in die öffentlichen Seiten',
	'label_piwik_user' => 'PIWIK Benutzerkonto',
	'label_recuperer_liste' => 'Website-Liste vom PIWIK-Server abrufen',
	'label_restreindre_auteurs_prive' => 'Einschränkungen für manche angemeldeten Besucher (Redaktion)',
	'label_restreindre_auteurs_public' => 'Einschränkungen für manche angemeldeten Besucher (öffentlicher Bereich)',
	'label_restreindre_statut_prive' => 'Einschränkungen für manche Mitglieder im Redaktionssystem',
	'label_restreindre_statut_public' => 'Einschränkungen für manche Mitglieder im öffentlichen Bereich',
	'label_token' => 'ID-Token auf dem Server',

	// M
	'mode_insertion_balise' => 'Einfügen mit dem Tag #PIWIK (erfordert Änderung ihrer Skelette)',
	'mode_insertion_pipeline' => 'Automatisches Einfügen mit der Pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Ihre ID',
	'textes_url_piwik' => 'Ihr Piwik-Server'
);
