<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/spip400?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 4
	'401_error' => 'K požadovanej stránke alebo požadovanému dokumentu nemáte dostatočné prístupové práva.',
	'401_error_comment_connected' => '{{Na získanie prístupu, prosím, kontaktujte webmastera}}

Prístup k tejto stránke alebo k tomuto dokumentu musí byť autorizovaný a identifikovaný. Zdá sa, že vaše prístupové práva nie sú dostatočné.',
	'401_error_comment_notconnected' => '{{Na získanie prístupu sa treba prihlásiť}}

Prístup k tejto stránke alebo k tomuto dokumentu musí byť autorizovaný a identifikovaný. Ak máte dostatočné práva, prihláste sa pomocou formulára, ktorý sa nachádza nižšie.',
	'404_error' => 'Požadovaná stránka alebo požadovaný dokument sa na stránke nedá nájsť.',
	'404_error_comment' => '{{Ospravedlňujeme sa za všetky nepríjemnosti.}}

Niektoré stránky nie sú trvalé alebo stále menia svoju URL ({internetovú adresu, ktorá sa zadáva do panela v prehliadači}). 

Na vylepšenie navigácie vám radíme urobiť tieto veci:
-* vérifiez l’URL que vous avez saisie dans la barre d’adresse de votre navigateur et assurez-vous qu’elle soit complète,
-* accédez [au plan du site|Liste exhaustive des pages du site->@plan@] pour rechercher la page souhaitée,
-* effectuez une recherche dans la zone de recherche de la page en saisissant des mots clés de la page souhaitée,
-* retournez à l’[accueil du site|Retour en page d’accueil->@sommaire@] pour pour repartir depuis la racine de la hiérarchie,
-* transmettez un rapport d’erreur aux administrateurs du site afin de corriger le lien rompu en utilisant le bouton ci-dessous.

En dernier lieu, de nombreux sites web disposent d’un ou plusieurs espaces réservés à leurs administrateurs ou abonnés nécessitant une connexion. Si vous y êtes autorisé, [cliquez ici pour accéder à la plateforme de connexion du site|Des identifiants vous seront demandés->@ecrire@].', # MODIF

	// B
	'backtrace' => 'PHP na spätné vystopovanie',

	// C
	'cfg_comment_email' => 'V poliach, ktoré sa nachádzajú nižšie, si zvoľte e-mailové adresy na posielanie a prijímanie správ o chybách ({tieto správy sa posielajú, keď používateľ klikne na určité tlačidlo – v predvolených nastaveniach sa používa e-mail webmestera}).',
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
	'report_a_bug_comment' => 'Správu o chybe, s ktorou ste sa stretli, môžete poslať webmasterovi stránky kliknutím na tlačidlo.',
	'report_a_bug_envoyer' => 'Poslať správu',
	'report_a_bug_message_envoye' => 'OK – Správa o chybe bola odoslaná. Ďakujeme.',
	'report_a_bug_texte_mail' => 'Stránka "@url@" vypísala @date@ chybový kód HTTP @code@.',
	'report_a_bug_titre_mail' => '[@sitename@] Správa o chybe HTTP @code@',
	'report_an_authorized_bug_comment' => 'Ak si myslíte, že došlo k chybe alebo nesprávnemu vyhodnoteniu vašich práv, kliknutím na tlačidlo nižšie môžete napísať webmasterovi správu o chybe. Údaje (<i>požadovaná stránka a vaše prihlasovacie údaje</i>) sa posielajú automaticky.',
	'request_auth_message_envoye' => 'OK – Vaša požiadavka bola odoslaná. Ďakujeme.',
	'request_auth_texte_mail' => 'Používateľ "@user@" požiadal o povolenie k "@url@" dňa @date@.',

	// S
	'session' => 'Používateľské session',
	'session_only_notempty_values' => '(vypísané sú iba hodnoty, ktoré nie sú prázdne)',
	'spip_400' => 'SPIP 400',

	// U
	'url_complete' => 'Celá internetová adresa',
	'utilisateur_concerne' => 'Dotknutý používateľ: '
);

?>
