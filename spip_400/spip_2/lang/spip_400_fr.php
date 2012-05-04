<?php
// RAPPELS
// Les textes de cette page peuvent etre rediges avec les raccourcis typo de SPIP
// !! - Les accents doivent etre code en HTML : é => &eacute;
// !! - Les apostrophes doivents etre echappees : ' => \'

$GLOBALS[$GLOBALS['idx_lang']] = array(

// chiffres //
// Textes des pages d'erreur HTML

	// 401 //
	'401_error' => 'Vous n\'avez pas les autorisations suffisantes pour acc&eacute;der &agrave; la page ou au document demand&eacute; ...',
	'401_error_comment_notconnected' => '{{Veuillez vous identifier ci-dessous pour y acc&eacute;der ...}}

L\'acc&egrave;s &agrave; cette page ou ce document n&eacute;cessite d\'&ecirc;tre d&ucirc;ment autoris&eacute; et identifi&eacute;. Si vous y &ecirc;tes autoris&eacute;, connectez-vous via le formulaire ci-dessous.',
	'401_error_comment_connected' => '{{Veuillez contacter le webmestre du site pour y acc&eacute;der ...}}

L\'acc&egrave;s &agrave; cette page ou ce document n&eacute;cessite d\'&ecirc;tre d&ucirc;ment autoris&eacute; et identifi&eacute;. Il semble que vos droits d\'acc&egrave;s ne soient pas suffisants ...',

	// 404 //
	'404_error' => 'La page ou le document que vous demandez est introuvable sur le site ...',
	'404_error_comment' => '{{Veuillez nous excuser pour ce contre-temps ...}}

Certaines pages web ne sont pas permanentes ou changent d’URL r&eacute;guli&egrave;rement ({adresse d’acc&egrave;s saisie dans la barre de navigateur}). 

Afin de faciliter votre navigation, nous vous conseillons les actions suivantes :
- v&eacute;rifiez l’URL que vous avez saisie dans la barre d’adresse de votre navigateur et assurez-vous qu’elle soit compl&egrave;te,
- acc&eacute;dez [au plan du site|Liste exhaustive des pages du site->@plan@] pour rechercher la page souhait&eacute;e,
- effectuez une recherche dans la zone de recherche de la page en saisissant des mots cl&eacute;s de la page souhait&eacute;e,
- retournez à l’[accueil du site|Retour en page d’accueil->@sommaire@] pour pour repartir depuis la racine de la hi&eacute;rarchie,
- transmettez un rapport d’erreur aux administrateurs du site afin de corriger le lien rompu en utilisant le bouton ci-dessous.

En dernier lieu, de nombreux sites web disposent d’un ou plusieurs espaces r&eacute;serv&eacute;s &agrave; leurs administrateurs ou abonn&eacute;s n&eacute;cessitant une connexion. Si vous y &ecirc;tes autoris&eacute;, [cliquez ici pour acc&eacute;der &agrave; la plateforme de connexion du site|Des identifiants vous seront demand&eacute;s->@ecrire@].',

// B //
	'backtrace' => 'Backtrace PHP',

// C //
	// Page de CFG
	'cfg_descr_titre' => 'Gestionnaire d\'erreurs HTTP 400',
	'cfg_label_titre' => 'Configuration du gestionnaire d\'erreurs HTTP 400',
	'cfg_descr' => 'Vous pouvez ici d&eacute;finir certaines options du plugin "Gestion des Erreurs HTTP".',
	'cfg_label_sender_email' => 'Adresse courriel d\'envoi des rapports d\'erreur',
	'cfg_label_receipt_email' => 'Adresse courriel destinataire des rapports d\'erreur',
	'cfg_comment_email' => 'Utilisez les champs ci-dessous pour choisir les adresses email d\'envoi et r&eacute;ception des rapports d\'erreurs ({ces rapports sont envoy&eacute;s lorsque l\'internaute clique sur le bouton concern&eacute; - par d&eacute;faut, le mail du webmestre est utilis&eacute;}).',

// E //
	'email_webmestre' => 'Email webmestre',
	'email_webmestre_ttl' => 'Insertion automatique de l\'email du webmestre',

// H //
	'http_headers' => 'En-t&ecirc;tes HTTP',

// R //
	// Bug rapport
	'report_a_bug' => 'Rapport d\'incident',
	'report_a_bug_comment' => 'Vous pouvez soumettre un rapport d\'incident sur l\'erreur que vous rencontrez au webmestre du site en cliquant sur le bouton ci-dessous.',
	'report_an_authorized_bug_comment' => 'Si vous pensez qu\'il s\'agit d\'une erreur ou d\'une mauvaise &eacute;valuation de vos droits, vous pouvez soumettre un rapport d\'incident au webmestre du site en cliquant sur le bouton ci-dessous. Les informations sont transmises automatiquement (<i>page demand&eacute;e et vos identifiants</i>).',
	'report_a_bug_envoyer' => 'Envoyer le rapport',
	'report_a_bug_message_envoye' => 'OK - Un rapport de bug a &eacute;t&eacute; transmis. Merci.',
	'report_a_bug_titre_mail' => '[@sitename@] Rapport d\'erreur HTTP @code@',
	'report_a_bug_texte_mail' => 'La page "@url@" a renvoy&eacute;e un code erreur HTTP @code@ au @date@.',
	'request_auth_texte_mail' => 'L\'utilisateur "@user@" a sollicit&eacute; d\'&ecirc;tre autoris&eacute; &agrave; acc&eacute;der &agrave; la page "@url@" au @date@.',
	'request_auth_message_envoye' => 'OK - Votre demande a &eacute;t&eacute; transmise. Merci.',
	'referer' => 'Referer',

// S //
	'spip_400' => 'SPIP 400',
	'session' => 'Session utilisateur',
	'session_only_notempty_values' => '(seules les valeurs non-vides sont inscrites)',

// U //
	'url_complete' => 'URL compl&egrave;te',
	'utilisateur_concerne' => 'Utilisateur concern&eacute; : ',

);
?>