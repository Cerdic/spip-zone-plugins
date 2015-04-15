<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	//A
	'api_errorcode' => 'Infos de retour de MailChimp. Codes retournés:',
	'api_error' => 'Une erreur est survenue, votre inscription n\'a pas été prise en compte',

	//C
	'config_erreur' => 'Erreur Les paramêtres d\'accès à Mailchimp ne sont pas renseignés, l\'administrateur du site peut le faire à cette adresse : <a href="ecrire/?exec=configurer_mailchimp">configuration MailChimp</a>',
	'configurer_erreur_api' => 'Erreur, vos données n\'ont pas été sauvegardées',
	'configurer_legend' => 'Paramétrages des identifiants MailChimpss',
	'configurer_listid' => 'Identifiant unique de la liste',
	'configurer_listid_explication' => 'Chaque liste de personne possède un identifiant unique. Visualisez la liste qui vous intéresse sur cette page : <a href ="https://admin.mailchimp.com/lists/">Mailchimp list</a>. Cliquez sur "Settings/list Settings and unique id" puis en bas de la page vous trouvez l\'identifiant: "unique id for list"',
	'configurer_apikey' => 'Identifiant unique du compte mailchimp',
	'configurer_apikey_explication' => 'API Key que l\'on trouve dans l\'administration : <a href="http://admin.mailchimp.com/account/api">MailChimp API</a>',

//A
	'demande_inscription_envoyee1' => 'Un mail vous a été envoyé a l\'adresse "@email@".',
	'demande_inscription_envoyee2' => 'Pour valider votre abonnement vous devez cliquer sur le lien dans l\'email que vous avez reçu.',
	'demande_inscription_envoyee3' => 'Vérifiez éventuellement dans votre dossier SPAM ou dans votre corbeille si jamais vous ne trouvez pas cet email.',
	'demande_desincription_ok' => 'Votre adresse "@email@" a également été retirée de Mailchimp. Au revoir.',


	'enregistrer_et_tester' => 'Enregistrer et Tester la connexion',


	'retour_test_api' => 'Connexion à MailChimp réussie, vos paramètres ont bien été sauvegardés.Ci-après les 5 (ou moins) abonnés à la liste:',
	


	//fin parceque bon penser a mettre une virgule ou pas c'est penible
	'zzzz'=>'zzzz'

);

?>