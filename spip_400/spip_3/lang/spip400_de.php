<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/spip400?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 4
	'401_error' => 'Sie haben nicht die notwendigen Berechtigungen die angefragte Seite bzw. das angefragte Dokument anzuzeigen...',
	'401_error_comment_connected' => '{{Bitte kontaktieren sie den Webmaster um Zugriffsrechte zu erhalten...}}

Der Zugriff auf diese Seite bzw. dieses Dokument ist beschränkt. Es scheint, dass ihre Zugriffsrechte nicht ausreichen ...',
	'401_error_comment_notconnected' => '{{Bitte loggen Sie sich unten ein um zuzugreifen...}}

Der Zugriff auf diese Inhalte ist beschränkt. Wenn Sie eine Zugangsberechtigung haben, loggen Sie sich über das Formular unten ein. ',
	'404_error' => 'Die Seite bzw. das Dokument, dass Sie aufgerufen haben konnte nicht gefunden werden...',
	'404_error_comment' => '{{Wir bitten die Unannehmlichkeit zu entschuldigen ...}}

Manche Seiten sind nur vorübergehend aufrufbar oder ändern ihre URL ({Adresszeile im Browser}). 

Um solche Fehler zu vermeiden empfehlen wir folgendes:
-* überprüfen Sie die URL in der Adresszeile des Browsres auf Vollständigkeit;
-* besuchen Sie die  [Sitemap|Vollständige Liste der Seiten dieser Webpräsenz->@plan@] um ihre gewünschte Seite zu finden;
-* führen sie über das Suchformular eine Suche nach Schlüsselwörtern des gesuchten Inhalts durch;
-* kehren Sie zur [Startseite|Zurück zur Startseite->@sommaire@] zurück um neu zu beginnen;
-* falls Sie über einen Link auf diese Seite gekommen sind benutzen Sie das Formular unten um diesen Fehler dem Administrator der Seite mitzuteilen.

Zu guter Letzt, viele Seiten haben geschlossene Bereiche für Adminstratoren oder angemeldete Besucher, falls sie eine Zugangsberechtigung haben [klicken Sie hier um isch anzumelden | Zugansgdaten benötigt->@ecrire@]. ',

	// B
	'backtrace' => 'PHP Backtrace',

	// C
	'cfg_comment_email' => 'Benutzen Sie das Formular um die E-Mail-Adressen für Absender und Empfänger von Fehlerberichten auszuwählen ({Diese Berichte werden versendet wenn der Nutzer den entsprechenden Button anklickt, Voreinstellung ist die E-Mail des Webmasters}).',
	'cfg_descr' => 'Hier können Sie einige Optionen für das Plugin  "HTTP-Fehler Managmenent" bearbeiten.',
	'cfg_label_receipt_email' => 'E-Mail-Adresse des Absender von Fehlerberichten',
	'cfg_label_sender_email' => 'E-Mail-Adresse des Empfängers von Fehlerberichten',
	'cfg_label_titre' => 'Konfiguration des HTTP 400 Fehler Managements',

	// E
	'email_webmestre' => 'E-Mail des Webmasters',
	'email_webmestre_ttl' => 'Automatisches Einfügen der Webmaster E-Mail',

	// H
	'http_headers' => 'HTTP Headers',

	// R
	'referer' => 'Referer',
	'report_a_bug' => 'Fehlerbericht',
	'report_a_bug_comment' => 'Sie können den Fehler an die Administratoren der Seite melden, indem Sie auf den folgenden Button klicken.',
	'report_a_bug_envoyer' => 'Fehler melden',
	'report_a_bug_message_envoye' => 'OK - Der fehler wurde gemeldet. Vielen Dank.',
	'report_a_bug_texte_mail' => 'Die Seite "@url@" hat einen HTTP Fehler @code@ am @date@ erzeugt.',
	'report_a_bug_titre_mail' => '[@sitename@]Fehlerbericht HTTP @code@',
	'report_an_authorized_bug_comment' => 'Wenn Sie denken, dass dies ein Fehler oder eine falsche Wertung ihrer Berechtigungen ist, können Sie einen Fehlerbericht an den Webmaster schicken, indem sie auf den Button am Ende  klicken. Die benötigten Informationen werden automatisch übertragen (<i>aufgerufene Seite und ihr Benutzername</i>).',
	'request_auth_message_envoye' => 'OK - Ihre Anfrage wurde weitergeleitet. Vielen Dank.',
	'request_auth_texte_mail' => 'Der Benutzer "@user@" hat am @date@ Zugang zur Seite "@url@" benatragt.',

	// S
	'session' => 'User Session',
	'session_only_notempty_values' => '(nur nicht leere Werte werden gelistet)',
	'spip_400' => 'SPIP 400',

	// U
	'url_complete' => 'Komplette URL',
	'utilisateur_concerne' => 'Betroffener Benutzer: '
);
