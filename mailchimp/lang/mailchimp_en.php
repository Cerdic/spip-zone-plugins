<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	//A
	'api_errorcode' => 'Information returned from MailChimp. Returned codes:',
	'api_error' => 'An error occured, your subscription couldn\'t be completed',

	//C
	'config_erreur' => 'Error: the Mailchimp settings are empty, the site administrator can change it there: <a href="ecrire/?exec=configurer_mailchimp">MailChimp configuration</a>',
	'configurer_erreur_api' => 'Error: your data could not be saved',
	'configurer_legend' => 'Your Mailchimp identification',
	'configurer_listid' => 'Unique identifier for the list',
	'configurer_listid_explication' => 'Every list has a unique identifier. Find the list you want in that page: <a href ="https://admin.mailchimp.com/lists/">Mailchimp list</a>. Click on “Settings/list Settings and unique id”, then at the bottom of the page you will find the identifier: “unique id for list”',
	'configurer_apikey' => 'Unique identifier for the Mailchimp account',
	'configurer_apikey_explication' => 'API Key that you\'ll find in the administration: <a href="http://admin.mailchimp.com/account/api">MailChimp API</a>',

//A
	'demande_inscription_envoyee1' => 'An email was sent to the address “@email@”.',
	'demande_inscription_envoyee2' => 'To confirm your subscription, you must click on the link in the email you have been sent.',
	'demande_inscription_envoyee3' => 'Please check in your SPAM folder or in your trash if you cannot find the email.',
	'demande_desincription_ok' => 'Your address “@email@” was also deleted from Mailchimp. Goodbye.',


	'enregistrer_et_tester' => 'Save and test the connection',


	'retour_test_api' => 'Connection to Mailchimp OK, your settings have been saved. Below are 5 (or less) subscribers to the list:',



	//fin parceque bon penser a mettre une virgule ou pas c'est penible
	'zzzz'=>'snorr :)'

);

?>