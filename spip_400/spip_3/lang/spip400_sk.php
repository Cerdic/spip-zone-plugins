<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/spip400?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 4
	'401_error' => 'K požadovanej stránke alebo požadovanému dokumentu nemáte dostatočné prístupové práva.',
	'401_error_comment_connected' => '{{Veuillez contacter le webmestre du site pour y accéder...}}

L\'accès à cette page ou ce document nécessite d\'être dûment autorisé et identifié. Il semble que vos droits d\'accès ne soient pas suffisants ...', # NEW
	'401_error_comment_notconnected' => '{{Veuillez vous identifier ci-dessous pour y accéder...}}

L\'accès à cette page ou ce document nécessite d\'être dûment autorisé et identifié. Si vous y êtes autorisé, connectez-vous via le formulaire ci-dessous.', # NEW
	'404_error' => 'Požadovaná stránka alebo požadovaný dokument sa na stránke nedá nájsť.',
	'404_error_comment' => '{{Veuillez nous excuser pour ce contre-temps...}}

Certaines pages web ne sont pas permanentes ou changent d’URL régulièrement ({adresse d’accès saisie dans la barre de navigateur}). 

Afin de faciliter votre navigation, nous vous conseillons les actions suivantes :
-* vérifiez l’URL que vous avez saisie dans la barre d’adresse de votre navigateur et assurez-vous qu’elle soit complète,
-* accédez [au plan du site|Liste exhaustive des pages du site->@plan@] pour rechercher la page souhaitée,
-* effectuez une recherche dans la zone de recherche de la page en saisissant des mots clés de la page souhaitée,
-* retournez à l’[accueil du site|Retour en page d’accueil->@sommaire@] pour pour repartir depuis la racine de la hiérarchie,
-* transmettez un rapport d’erreur aux administrateurs du site afin de corriger le lien rompu en utilisant le bouton ci-dessous.

En dernier lieu, de nombreux sites web disposent d’un ou plusieurs espaces réservés à leurs administrateurs ou abonnés nécessitant une connexion. Si vous y êtes autorisé, [cliquez ici pour accéder à la plateforme de connexion du site|Des identifiants vous seront demandés->@ecrire@].', # NEW

	// B
	'backtrace' => 'Spätne vystopovať PHP',

	// C
	'cfg_comment_email' => 'Utilisez les champs ci-dessous pour choisir les adresses email d\'envoi et réception des rapports d\'erreurs ({ces rapports sont envoyés lorsque l\'internaute clique sur le bouton concerné - par défaut, le mail du webmestre est utilisé}).', # NEW
	'cfg_descr' => 'Tu môžete nastaviť niektoré funkcie zásuvného modulu "Správa chýb HTTP".',
	'cfg_label_receipt_email' => 'E-mailová adresa príjemcu správ o chybách',
	'cfg_label_sender_email' => 'E-mailová adresa na posielanie správ o chybách',
	'cfg_label_titre' => 'Nastavenia manažéra chybovej stránky HTTP 400',

	// E
	'email_webmestre' => 'Napísať webmasterovi',
	'email_webmestre_ttl' => 'Automatické vkladanie e-mailu  webmastera',

	// H
	'http_headers' => 'Hlavičky HTTP',

	// R
	'referer' => 'Referer',
	'report_a_bug' => 'Nahlásiť chybu',
	'report_a_bug_comment' => 'Vous pouvez soumettre un rapport d\'incident sur l\'erreur que vous rencontrez au webmestre du site en cliquant sur le bouton ci-dessous.', # NEW
	'report_a_bug_envoyer' => 'Poslať správu',
	'report_a_bug_message_envoye' => 'OK – Správa o chybe bola odoslaná. Ďakujeme.',
	'report_a_bug_texte_mail' => 'Stránka "@url@" vypísala @date@ chybový kód HTTP @code@.',
	'report_a_bug_titre_mail' => '[@sitename@] Správa o chybe HTTP @code@',
	'report_an_authorized_bug_comment' => 'Si vous pensez qu\'il s\'agit d\'une erreur ou d\'une mauvaise évaluation de vos droits, vous pouvez soumettre un rapport d\'incident au webmestre du site en cliquant sur le bouton ci-dessous. Les informations sont transmises automatiquement (<i>page demandée et vos identifiants</i>).', # NEW
	'request_auth_message_envoye' => 'OK – Vaša požiadavka bola odoslaná. Ďakujeme.',
	'request_auth_texte_mail' => 'L\'utilisateur "@user@" a sollicité d\'être autorisé à accéder à la page "@url@" au @date@.', # NEW

	// S
	'session' => 'Session utilisateur', # NEW
	'session_only_notempty_values' => '(vypísané sú iba hodnoty, ktoré nie sú prázdne)',
	'spip_400' => 'SPIP 400',

	// U
	'url_complete' => 'Celá internetová adresa',
	'utilisateur_concerne' => 'Utilisateur concerné : ' # NEW
);

?>
