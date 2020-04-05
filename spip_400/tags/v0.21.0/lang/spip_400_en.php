<?php
// RAPPELS
// Les textes de cette page peuvent etre rediges avec les raccourcis typo de SPIP
// !! - Les accents doivent etre code en HTML : é => &eacute;
// !! - Les apostrophes doivents etre echappees : ' => \'

$GLOBALS[$GLOBALS['idx_lang']] = array(

// chiffres //
// Textes des pages d'erreur HTML

	// 401 //
	'401_error' => 'You do not have sufficient permissions to access the requested page or document ...',
	'401_error_comment_notconnected' => '{{Please login below to access ...}}

Access to this page or this document requires to be authorized and identified. If you have permission, please sign in via the form below.',
	'401_error_comment_connected' => '{{Please contact the webmaster for access ...}}

Access to this page or this document requires to be authorized and identified. It appears that your access rights are not sufficient ...',

	// 404 //
	'404_error' => 'The page or document you requested is not found on the site ...',
	'404_error_comment' => '{{We apologize for this time-cons ...}}

Some web pages are not permanent or regularly changing URL ({address access entry in the browser bar}).

To facilitate your browsing, we recommend the following actions:
- check the URL you typed in the address bar of your browser and make sure it is complete,
- access to the [site map|Exhaustive list of site pages->@plan@] to find the desired page,
- perform a search in this page search box by entering keywords of the page you want,
- return to the [homepage|Back to site homepage->@sommaire@] to restart from the root of the hierarchy,
- send an error report to the site administrators to correct the broken link using the button below.

Finally, many websites have one or several spaces reserved for their directors or subscribers require login. If you have permission, [click here to access the platform connecting the site|IDs will be required->@ecrire@].',

// B //
	'backtrace' => 'PHP Backtrace',

// C //
	// Page de CFG
	'cfg_descr_titre' => 'HTTP 400 Error Management',
	'cfg_label_titre' => 'Setup of HTTP 400 Error Management',
	'cfg_descr' => 'Here you can set some options of the "HTTP 400 Error Management" plugin.',
	'cfg_label_sender_email' => 'Email address of sender reports error',
	'cfg_label_receipt_email' => 'Email address of recipient reports error',
	'cfg_comment_email' => 'Use the form below to select the email addresses of sender and receiver for error reports ({these reports are sent when the user clicks the button in question - by default, webmaster\'s email is used}).',

// E //
	'email_webmestre' => 'Webmaster\'s email',
	'email_webmestre_ttl' => 'Webmaster\'s email auto-insertion',

// H //
	'http_headers' => 'HTTP Headers',

// R //
	// Bug rapport
	'report_a_bug' => 'Incident report',
	'report_a_bug_comment' => 'You can submit an incident report about the error you are encountering to the webmaster of the site by clicking the button below.',
	'report_an_authorized_bug_comment' => 'If you think that it is an error or a bad review of your rights, you can submit an incident report to the webmaster of the site by clicking the button below. Information is transmitted automatically (<i>requested page and your username</i>).',
	'report_a_bug_envoyer' => 'Send report',
	'report_a_bug_message_envoye' => 'OK - A bug report has been submitted. Thank you.',
	'report_a_bug_titre_mail' => 'Error report @code@',
	'report_a_bug_texte_mail' => 'The page "@url@" has returned an error code @code@ at @date@.',
	'request_auth_texte_mail' => 'User "@user@" asked to be allowed to access the page "@url@" at @date@.',
	'request_auth_message_envoye' => 'OK - Your request has been forwarded. Thank you.',
	'referer' => 'Referer',

// S //
	'spip_400' => 'SPIP 400',
	'session' => 'User session',
	'session_only_notempty_values' => '(only non-empty values ​​are listed)',

// U //
	'url_complete' => 'Complete URL',
	'utilisateur_concerne' => 'User concerned : ',

);
?>