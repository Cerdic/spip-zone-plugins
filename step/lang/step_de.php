<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	// Actions
	'action_activer' => 'Aktivieren',
	'action_telecharger_et_activer' => 'Laden und aktivieren',
	'action_desactiver' => 'Deaktivieren',
	'action_maj' => 'Updaten',
	'action_maj_activer' => 'Updaten und Aktivieren',
	'action_telecharger' => 'Laden',
	'action_desinstaller' => 'Deinstallieren',
	'action_supprimer' => 'Löschen',
	'action_rien_faire' => 'Nichts tun ...',
	'actions_proposees' => 'Anstehende Änderungen',
	'actualiser' => 'Aktualisieren',
	'appliquer_actions' => 'Änderungen durchführen',
	'annuler' => 'Abbrechen',

	// E
	'erreur_absence_paquets' => 'Kein Paket ausgewählt',
	'erreur_action' => 'Die gewählte Aktion "@action@" ist unbekannt',

	// L
	'label_erreurs' => 'Fehler:',
	'librairie' => 'Bibliothek: @nom@',

	// G
	'gestion_des_plugins' => 'Pluginverwaltung',

	// M
	'message_action_end_get' => 'Laden des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_install' => 'Installation des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_kill' => 'Löschen des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_off' => 'Deaktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_on' => 'Aktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_stop' => 'Deinstallieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_end_up' => 'Updaten des Plugins &laquo;@plugin@&raquo; (Version :@version@)',
	'message_action_get' => 'Herunterladen des Plugins &laquo;@plugin@&raquo; (version : @version@)',
	'message_action_install' => 'Das Plugin &laquo;@plugin@&raquo; (Version: @version@) wird installiert',
	'message_action_kill' => 'Löschen der Dateien des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_off' => 'Deaktivieren des Plugins &laquo;@plugin@&raquo; (Version :@version@)',
	'message_action_on' => 'Aktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_stop' => 'Deinstallieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_up' => 'Updaten des Plugins &laquo;@plugin@&raquo; (Version :@version@)',
	'message_action_upon' => 'Updaten und AKtivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@)',
	'message_action_finale_get_fail' => 'Das Plugin &laquo;@plugin@&raquo; (Version :@version@) konnte nicht fehlerfrei heruntergeladen werden.',
	'message_action_finale_get_ok' => 'Das Plugin &laquo;@plugin@&raquo; (version : @version@) wurde heruntegeladen.',
	'message_action_finale_install_fail' => 'Die Installation des Plugins &laquo;@plugin@&raquo; (Version: @version@) ist fehlgeschlagen.',
	'message_action_finale_install_ok' => 'Die Installation des Plugins &laquo;@plugin@&raquo; (Version :@version@) war erfolgreich.',
	'message_action_finale_kill_fail' => 'Die Dateien des Plugins &laquo;@plugin@&raquo; (Version: @version@) konnten nicht vollständig gelöscht werden.',
	'message_action_finale_kill_ok' => 'Die Dateien des Plugins &laquo;@plugin@&raquo; (Version: @version@) wurden vollständig gelöscht.',
	'message_action_finale_off_fail' => 'Das Deaktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) ist fehlgeschlagen.',
	'message_action_finale_off_ok' => 'Das Deaktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) war erfolgreich',
	'message_action_finale_on_fail' => 'Das Aktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) ist fehlgeschlagen.',
	'message_action_finale_on_ok' => 'Das Aktivieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) war erfolgreich.',
	'message_action_finale_stop_fail' => 'Das Deinstallieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) ist fehlgeschlagen.',
	'message_action_finale_stop_ok' => 'Das Deinstallieren des Plugins &laquo;@plugin@&raquo; (Version: @version@) war erfolgreich.',
	'message_action_finale_up_fail' => 'Das Updaten des Plugins &laquo;@plugin@&raquo; (Version: @version@) ist fehlgeschlagen.',
	'message_action_finale_up_ok' => 'Das Updaten des Plugins &laquo;@plugin@&raquo; (Version: @version@) war erfolgreich.',
	'message_action_lib' => 'Herunterladen und Installieren der Bibliothek &laquo;@plugin@&raquo; (von: <a href="@version" class="spip_out">@version@</a>)',
	'message_action_finale_lib_fail' => 'Die Installation der Bibliothek &laquo;@plugin@&raquo; ist fehlgeschlagen.',
	'message_action_finale_lib_ok' => 'Die Bibliotehk &laquo;@plugin@&raquo; wurde installiert.',
	'messages_actions_realisees' => 'Alle Änderungen wurden erfolgreich durchgeführt..',
	'message_dependance_plugin' => 'Das Plugin @plugin@ ist abhängig von @dependance@.',
	'message_dependance_plugin_version' => 'Das Plugin @plugin@ ist abhängig von @dependance@ @version@',
	'message_erreur_ecriture_lib' => '&laquo;@plugin@&raquo; benötiget die Bibliothek <a href=\'@lib_url@\'>@lib@</a>
								im Verzeichnis <var>lib/</var> &agrave; im Wurzelverzeichnis ihrer Website.
								In dieses Verzeichnis kann nicht geschrieben werden.
								Sie müssen es anlegen und SPIP Schreibrechte geben.',
	'message_erreur_maj_inconnu' => 'Das Update des unbekannten Plugins (@id@) ist nicht möglich.',
	'message_erreur_plugin_introuvable' => 'Das Plugin @plugin@ für die Aktion @action@ wurde nicht gefunden.',
	'message_erreur_plugin_non_actif' => 'Ein inaktives Plugin kann nicht deaktiviert werden.',
	'message_incompatibilite_spip' => '@plugin@  ist nicht kompatibel mit ihrer Version von SPIP.',
	'message_maj_introuvable' => 'Das Update des Plugins @plugin@ wurde nicht gefunden (@id@).',
	'message_non_modifiable_extension' => 'Dieses Plugin ist als Erweiterung installiert. Änderungen sind nicht erlaubt.',
	'message_plugin_inexistant' => 'Das Plugin (@plugin@) liegt ist nicht vorhanden.',


	// N
	'nb_plugins' => '@nb@ Plugins',

	// P
	'paquets_activer_info' => 'Ein Plugin zum Aktivieren auswählen.',
	'paquets_liste' => 'Liste der Plugins',
	'paquets_telecharger_info' => 'Wenn das Plugin nicht installiert ist, wird es automatisch heruntergeladen.',
	'paquet_info' => 'Informationen über das Paket',
	'paquet_voir' => 'Quellen des Pakets ansehen',
	'paquet_telecharger' => 'Herunterladen',
	'paquets_telechargement' => 'Pakete herunterladen',
	'paquet_telechargement_ok' => 'Pakete wurden heruntergeladen',
	'paquet_telechargement_erreur' => 'Herunterladen fehlgeschlagen',
	'paquet_telechargement_prochain' => 'Nächster Download',
	'paquet_telechargement_todo' => 'Herunterzuladende Pakete',
	'paquets_nb_telechargements_err' => 'Bei @nb@ Paketen ist ein Fehler aufgetreten.',
	'paquets_telechargements_termines' => 'Alle Downloads abgeschlossen.',
	'paquets_telecharger' => 'Herunterladen',
	'paquets_telechargeables' => 'Herunterladbare Pakete',

	// champs des plugins
	'pas_de_plugin_trouve' => 'Es wurde kein Plugin gefunden, dass ihren Kriterien entspricht.',
	'plugin_nom' => 'Name',
	'plugin_trouve' => 'Ein Plugin entspricht ihren Kriterien.',
	'plugins_trouves' => '@nb@ Plugins entsprechen ihren Kriterien.',
	'plugin_upgrade_possible' => 'Update verfügbar',
	'plugin_upgrade_possible_version' => 'Die Version @version@ ist verfügbar',
	'plugins_utilises_recemment' => 'Zuletzt verwendete Plugins',
	'plugin_version' => 'Version',

	// R
	'recharger_la_page' => 'Seite neu laden',
	'rechercher' => 'Suchen:&nbsp;',

	//S
	'selectionner_maj' => 'Updates auswählen',
	'source_actualiser' => 'Aktualisieren',

	'source_voir' => 'Quelle ansehen',
	'sources_update' => 'Alle Quellen aktualisieren',

	//T
	'info_toutes_zones_presentes' => 'Alle Plugin-Zonen',

	// recherche
	'rechercher' => 'Suchen',
	'rechercher_plus' => "Mehr Suchoptionen",
	'rechercher_moins' => "Weniger Suchoptionen",

	// ou
	'ou' => 'Wo',
	'ou_tous' => 'Überalle',
	'ou_local' => 'Vorhandene Plugins',
	'ou_distant' => 'Herunterladebare Plugins',

	// statut
	'statut' => 'Status',
	'statut_tous' => 'Alle',
	'statut_actif' => 'Aktiv',
	'statut_inactif' => 'Inaktiv',
	'statut_inactif_present' => 'Inaktiv (vorhanden)',

	// categorie
	'categorie' => 'Kategorie',
	'categorie_tous' => 'Alle',

	// etat (des plugins)
	'etat' => 'Entwicklungsstatus',
	'etat_tous' => 'Alle',
	'etat_stable' => 'Stabil',
	'etat_test' => 'Im Test',
	'etat_developpement' => 'In Entwicklung',
	'etat_experimental' => 'Experimentell',

	// zones
	'zone' => 'Zone',
	'zone_tous' => 'Alle',
	'zone_plugins_locaux' => 'Lokal',


	// obsolete
	'obsolete' => 'Obsolet',
	'obsolete_tous' => 'Alle',

	// superieurs
	'superieur' => 'Neuere',
	'superieur_tous' => 'Alle',

	// champs
	'plugin_champ_etat' => 'Status',
	'plugin_champ_prefixe' => 'Prefix',
	'plugin_champ_auteur' => 'Autor',
	'plugin_champ_description' => 'Beschreibung',
	'plugin_champ_constante' => 'Konstante',
	'plugin_champ_dossier' => 'Ordner',
	'plugin_champ_version' => 'Version',
	'derniere_version' => 'letzte Version:',

	// Z
	'zone_adresse' => 'Adresse der Zone',
	'zone_adresse_incorrecte' => 'falsche oder syntaktisch falsche Adresse',
	'zone_adresse_presente' => 'Adresse der Zone bereits vorhanden',
	'zone_supprimer' => 'Zone löschen',
	'zones_ajouter' => 'Plugin-Zone hinzufügen',
	'zones_ajouter_info' => 'Sie können eine Zone hinzufügen, indem sie ihre Adresse in das dafür vorgesehene Feld eintragen.',
	'zones_exemple' => 'Beispiel:',
	'zones_info' => 'Eine Zone beschreibt eine Sammlung von Plugins, die SPIP automatisch herunterladen kann.',
	'zones_liste' => 'Liste der Zonen',
	'zones_paquets' => 'Plugin-Zonen',
);
?>
