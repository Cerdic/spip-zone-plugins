<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/autorite?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Verwaltung per Schlagwort aktivieren',
	'admin_complets' => 'Haupt-Administratoren',
	'admin_restreints' => 'Rubrik/Unteradministratoren?',
	'admin_tous' => 'Alle Administratoren (auch Unter-Administratoren)',
	'administrateur' => 'Administrator',
	'admins' => 'Administratoren',
	'admins_redacs' => 'Administratoren und Redakteure',
	'admins_rubriques' => 'Rubrikadministratoren haben:',
	'attention_crayons' => '<small><strong>Achtung!</strong> Die folgenden Einstellungen funktionieren nur, wenn Sie ein Plugin zur Textbearbeitung wie <a href="http://contrib.spip.net/Les-Crayons">den Stift</a>) nutzen.</small>',
	'attention_version' => 'Achtung, die folgenden Einstellungen funktionieren nicht mit Ihrer SPIP-Version:',
	'auteur_message_advitam' => 'Der Autor eines Beitrags, für immer',
	'auteur_message_heure' => 'Der Autor eines Beitrags, eine Stunde lang',
	'auteur_modifie_article' => '<strong>Autor ändert Artikel</strong>: jeder Redakteur kann seine veröffentlichten Artikel ändern.
	<br />
	<i>N.B.: Diese Einstellung betrifft auch angemeldete Leser, wenn ein entsprechendes Interface installiert ist.</i>',
	'auteur_modifie_email' => '<strong>Redakteur ändert Email</strong>: Jeder Redakteur kann seine Mailadresse auf seiner persönlichen Seite ändern.',
	'auteur_modifie_forum' => '<strong>Autor moderiert Forum</strong>: Jeder Autor kann die Foren seiner Artikel moderieren.',
	'auteur_modifie_petition' => '<strong>Autor moderiert Petition</strong>: Jeder Autor kann die Petitionen zu seinen Artikeln moderieren.',

	// C
	'config_auteurs' => 'Konfiguration der Autoren',
	'config_auteurs_rubriques' => 'Welche Arten Autor können <b>Rubriken zugeordnet werden</b>?',
	'config_auteurs_statut' => 'Welchen Status erhält ein neuer Autor <b>als Grundeinstellung</b> ?',
	'config_plugin_qui' => 'Wer darf <strong>Plugin-Einstellungen ändern</strong> (Aktivierung ...) ?',
	'config_site' => 'Website-Konfiguration',
	'config_site_qui' => 'Wer darf die <strong>Website-Konfiguration</strong> ändern?',
	'crayons' => 'Bleistift',

	// D
	'deja_defini' => 'Die folgenden Rechte sind schon woanders definiert worden:',
	'deja_defini_suite' => 'Das Plugin « Autorität » kann manche der folgenden Einstellungen nicht ändern, so dass sie u.U. nicht mehr funktionieren.
	<br />Um diese EInschränkung aufzuheben sollten Sie prüfen, ob Ihre Datei <tt>mes_options.php</tt> oder ein aktives Plugin) diese Funktionen definiert.',
	'descriptif_1' => 'Diese Konfigurationssseite ist nur für Webmaster zugänglich :',
	'descriptif_2' => '<p>Wenn Sie diese Liste ändern möchten, bearbeiten Sie bitte die Datei <tt>config/mes_options.php</tt> (sollte sie nicht existieren, muss sie angelegt werden) und tragen sie dort die IDs der Webmaster in diesem Format ein:</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>Ab SPIP 2.1 können Sie einem Administrator auch Webmaster-Rechte über die Seite zur Bearbetung von Autoren geben.</p>
<p>Achtung: Die so definierten Webmaster benötigen keine Authetifizierung per FTP für sicherheitsrelevante Aktionen wie Updates der Datenbank.</p>

<a href=\'https://contrib.spip.net/Autorite\' class=\'spip_out\'>Siehe Dokumentation</a>
',
	'details_option_auteur' => '<small><br />Zur Zeit funktioniert die Option « Autor » nur für eingeschriebene Autoren (z.B. in Foren mit Anmeldung).   Wenn sie aktiviert ist, können ausserdem Administratoren die Inhalte von Forumsbeiträgen bearbeiten. 
	</small>',
	'droits_des_auteurs' => 'Rechte der Autoren',
	'droits_des_redacteurs' => 'Rechte der Redakteure',
	'droits_idem_admins' => 'die selben Rechte wie alle Administratoren',
	'droits_limites' => 'auf diese Rubriken beschränkte Rechte',

	// E
	'effacer_base_option' => '<small><br />Die empfohlene Einstellung ist « niemand », die Grundeinstellung von SPIP ist « Administratoren » (jedoch immer mit Authetifizierung per FTP).</small>',
	'effacer_base_qui' => 'Wer darf die Datenbank mit allen Daten der Website <strong>löschen</strong>?',
	'espace_publieur' => 'Wiki-Bereich / offene Publikation',
	'espace_publieur_detail' => 'Wählen Sie einen der folgenden Bereiche für das offene Publizieren durch eingetragene Redakteure und / oder Besucher der Website (Voraussetzung sind ein Interface zur Texteingabe wie der {Stift} und ein Formular zum Anlegen des Artikels) :',
	'espace_publieur_qui' => 'Sie können das Veröffentlichen durch weitere Nutzer zusätzlich zu den Administratoren freigeben:',
	'espace_wiki' => 'Wiki-Bereich',
	'espace_wiki_detail' => 'Wählen Sie eine Hauptrubrik als Wiki aus, deren Inhalte im öffentlichen Bereich der Website durch jede/n bearbeitet werden können (vorausgesetzt die textbearbeitung wird z.B. durch den {Stift} ermöglicht):',
	'espace_wiki_mots_cles' => 'Wiki-Bereich per Schlagwort festlegen',
	'espace_wiki_mots_cles_detail' => 'Wählen Sie hier die Schlagworte, die den Wiki-Modus aktivieren (vorausgesetzt ein Interface zur Textbearbeitung wie der {Stift} ist verfügbar).',
	'espace_wiki_mots_cles_qui' => 'Sie können dieses Wiki anderen Benutzer als den Administratoren öffnen:',
	'espace_wiki_qui' => 'Sie können dieses Wiki anderen Benutzern als den Administratoren öffnen:',

	// F
	'forums_qui' => '<strong>Forum :</strong> Wer darf Foreninhalte ändern :',

	// I
	'icone_menu_config' => 'Autorität',
	'info_gere_rubriques' => 'Verwaltet die folgenden Rubriken:',
	'info_gere_rubriques_2' => 'Ich verwalte die folgenden Rubriken:',
	'infos_selection' => '(Sie können mehrere Bereiche mit der Umschalttaste auswählen.)',
	'interdire_admin' => 'Setzen Sie die folgenden Optionen, um Administratoren das Recht zum Anlegen neuer Objekte zu entziehen:',

	// M
	'mots_cles_qui' => '<strong>Schlagworte:</strong> Wer darf Schlagworte anlegen und bearbeiten:',

	// N
	'non_webmestres' => 'Diese Einstellung gilt nicht für Webmaster.',
	'note_rubriques' => '(Beachten Sie, dass nur Administratoren Rubriken anlegen dürfen, und Rubrik-Administratoren das nur in ihren Rubriken dürfen.)',
	'nouvelles_rubriques' => 'neue Rubriken der obersten Ebene',
	'nouvelles_sous_rubriques' => 'neue Unterrubriken in der Rubrikstruktur.',

	// O
	'ouvrir_redacs' => 'Für Redakteure der Website öffnen:',
	'ouvrir_visiteurs_enregistres' => 'Für eingetragene Besucher öffnen:',
	'ouvrir_visiteurs_tous' => 'Für alle Besucher der Website öffnen:',

	// P
	'pas_acces_espace_prive' => '<strong>Kein Zugang zum Redaktionssystem:</strong> Die Redakteure können nicht auf den internen Bereich zugreifen.',
	'personne' => 'Niemand',
	'petitions_qui' => '<strong>Unterschriften:</strong> Wer die Unterschriften von Petitionen ändern darf:',
	'publication' => 'Veröffentlichung',
	'publication_qui' => 'Wer auf dieser Website veröffentlichen darf:',

	// R
	'redac_tous' => 'Alle Redakteure',
	'redacs' => 'für Redakteure der Seite',
	'redacteur' => 'Redakteur',
	'redacteur_lire_stats' => '<strong>Statistik für Redakteure</strong>: Redakteure können die Zugriffsstatistiken einsehen.',
	'redacteur_modifie_article' => '<strong>Reakteure ändern Vorschläge</strong>: Jeder Redakteur darf vorgeschlagene Artikel ändern, auch wenn er nicht der Autor ist.',
	'redacteurs_voir_auteurs' => '<strong>Redakteur sieht Autoren</strong> : Sollen die Redakteure <strong>die Liste der Autoren und ihrer Mailadressen</strong>ebenso einsehen können wie deren <strong>interne Infoseiten</strong> ?',
	'refus_1' => '<p>Nur die Webmaster der Seite',
	'refus_2' => 'dürfen diese Einstellungen ändern.</p>
<p>Mehr Informationen bietet <a href="https://contrib.spip.net/Autorite">die Dokumentation</a>.</p>',
	'reglage_autorisations' => 'Rechte-Einstellungen',

	// S
	'sauvegarde_qui' => 'Wer darf <strong>Sicherungskopien</strong> anlegen?',

	// T
	'tous' => 'Alle',
	'tout_deselectionner' => 'alles abwählen',

	// V
	'valeur_defaut' => '(Standardwert)',
	'visiteur' => 'Besucher',
	'visiteurs_anonymes' => 'Anonyme Besucher dürfen neue Seiten anlegen.',
	'visiteurs_enregistres' => 'für angemeldete Besucher',
	'visiteurs_tous' => 'allen Besuchern der Seite.',

	// W
	'webmestre' => 'Der Webmaster',
	'webmestres' => 'Webmaster'
);
