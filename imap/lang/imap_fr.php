<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_email' => 'Email', 
	'cfg_email_explication' => 'Email ou identifiant de la boite email',   
	'cfg_email_pwd' => 'Mot de passe',
	'cfg_hote_imap' => 'Adresse du serveur IMAP',
	'cfg_hote_imap_explication' => 'ex. imap.gmail.com',   
	'cfg_hote_options' => 'Options de connexion',
	'cfg_hote_options_explication' => 'Voir <a href="http://php.net/manual/fr/function.imap-open.php">les "flags" possibles</a> (par exemple : <code>/imap/tls/novalidate-cert</code>)',
	'cfg_hote_port' => 'Port',
	'cfg_hote_port_explication' => '143, 993 (SSL) ou 993/imap/ssl (gmail) - <a href="http://php.net/manual/fr/function.imap-open.php">Infos</a>',
	'cfg_inbox' => 'Dossier distant',
	'cfg_titre_parametrages' => 'Paramètres de connexion',

	// I
	'imap_titre' => 'IMAP',

	// T
	'test_connexion' => 'Test de connexion au serveur IMAP',
	'test_connexion_ok' => 'Authentification réussie !',
	'test_connexion_notok' => 'Erreur : Impossible de se connecter à<br /><i>@connexion@</i>',
	'test_librairie_installee_notok' => 'Erreur : la librairie PHP "imap" n\'est pas installée - Voir <a href="http://www.php.net/manual/en/imap.setup.php">la documentation</a>.',
	'test_parametres_remplis_notok' => 'Remplir les paramètres du serveur avant de tester la connexion IMAP.',
	'titre_page_configurer_imap' => 'Connexion IMAP',

);

?>
