<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/spip_400/spip_3/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 4
	'401_error' => 'Vous n’avez pas les autorisations suffisantes pour accéder à la page ou au document demandé...',
	'401_error_comment_connected' => '{{Veuillez contacter le webmestre du site pour y accéder...}}

L’accès à cette page ou ce document nécessite d’être dûment autorisé et identifié. Il semble que vos droits d’accès ne soient pas suffisants ...',
	'401_error_comment_notconnected' => '{{Veuillez vous identifier ci-dessous pour y accéder...}}

L’accès à cette page ou ce document nécessite d’être dûment autorisé et identifié. Si vous y êtes autorisé, connectez-vous via le formulaire ci-dessous.',
	'404_error' => 'La page ou le document que vous demandez est introuvable sur le site...',
	'404_error_comment' => '{{Veuillez nous excuser pour ce contre-temps...}}

Certaines pages web ne sont pas permanentes ou changent d’URL régulièrement ({adresse d’accès saisie dans la barre de navigateur}). 

Afin de faciliter votre navigation, nous vous conseillons les actions suivantes :
-* vérifiez l’URL que vous avez saisie dans la barre d’adresse de votre navigateur et assurez-vous qu’elle soit complète,
-* accédez [au plan du site|Liste exhaustive des pages du site->@plan@] pour rechercher la page souhaitée,
-* effectuez une recherche dans la zone de recherche de la page en saisissant des mots clés de la page souhaitée,
-* retournez à l’[accueil du site|Retour en page d’accueil->@sommaire@] pour repartir depuis la racine de la hiérarchie,
-* transmettez un rapport d’erreur aux administrateurs du site afin de corriger le lien rompu en utilisant le bouton ci-dessous.

En dernier lieu, de nombreux sites web disposent d’un ou plusieurs espaces réservés à leurs administrateurs ou abonnés nécessitant une connexion. Si vous y êtes autorisé, [cliquez ici pour accéder à la plateforme de connexion du site|Des identifiants vous seront demandés->@ecrire@].',

	// B
	'backtrace' => 'Backtrace PHP',

	// C
	'cfg_comment_email' => 'Utilisez les champs ci-dessous pour choisir les adresses email d’envoi et réception des rapports d’erreurs ({ces rapports sont envoyés lorsque l’internaute clique sur le bouton concerné - par défaut, le mail du webmestre est utilisé}).',
	'cfg_descr' => 'Vous pouvez ici définir certaines options du plugin "Gestion des Erreurs HTTP".',
	'cfg_label_receipt_email' => 'Adresse courriel destinataire des rapports d’erreur',
	'cfg_label_sender_email' => 'Adresse courriel d’envoi des rapports d’erreur',
	'cfg_label_titre' => 'Configuration du gestionnaire d’erreurs HTTP 400',

	// E
	'email_webmestre' => 'Email webmestre',
	'email_webmestre_ttl' => 'Insertion automatique de l’email du webmestre',

	// H
	'http_headers' => 'En-têtes HTTP',

	// R
	'referer' => 'Referer',
	'report_a_bug' => 'Rapport d’incident',
	'report_a_bug_comment' => 'Vous pouvez soumettre un rapport d’incident sur l’erreur que vous rencontrez au webmestre du site en cliquant sur le bouton ci-dessous.',
	'report_a_bug_envoyer' => 'Envoyer le rapport',
	'report_a_bug_message_envoye' => 'OK - Un rapport de bug a été transmis. Merci.',
	'report_a_bug_texte_mail' => 'La page "@url@" a renvoyée un code erreur HTTP @code@ au @date@.',
	'report_a_bug_titre_mail' => '[@sitename@] Rapport d’erreur HTTP @code@',
	'report_an_authorized_bug_comment' => 'Si vous pensez qu’il s’agit d’une erreur ou d’une mauvaise évaluation de vos droits, vous pouvez soumettre un rapport d’incident au webmestre du site en cliquant sur le bouton ci-dessous. Les informations sont transmises automatiquement (<i>page demandée et vos identifiants</i>).',
	'request_auth_message_envoye' => 'OK - Votre demande a été transmise. Merci.',
	'request_auth_texte_mail' => 'L’utilisateur "@user@" a sollicité d’être autorisé à accéder à la page "@url@" au @date@.',

	// S
	'session' => 'Session utilisateur',
	'session_only_notempty_values' => '(seules les valeurs non-vides sont inscrites)',
	'spip_400' => 'SPIP 400',

	// U
	'url_complete' => 'URL complète',
	'utilisateur_concerne' => 'Utilisateur concerné : '
);

?>
