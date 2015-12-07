<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_autologin_label' => 'Activer l\'autologin',
	'cfg_autologin_explication' => 'Si un certain cookie est présent, cela indique que
		l\'utilisateur est déjà connecté au SSO, quelque part sur le même domaine, mais pas forcément chez nous.
		Si l\'on est pas actuellement authentifié on peut alors se loger automatiquement au SSO, 
		sans avoir besoin de cliquer un lien «Se connecter», et sans avoir de formulaire d\'identification à remplir.',
	'cfg_cookie_nom_label' => 'Nom du cookie pour l\'autologin',
	'cfg_cookie_nom_explication' => 'Nom du cookie utilisé globalement sur tout le domaine pour l\'autologin',
	'cfg_cookie_valeur_label' => 'Valeur du cookie',
	'cfg_cookie_valeur_explication' => 'Valeur du cookie qui considère qu\'on est identifié',

	'cfg_titre_parametrages' => 'Paramétrages',

	// S
	'simplesaml_titre' => 'Authentification SAML',

	// T
	'titre_page_configurer_simplesaml' => 'Configurer Simple SAML',
);
