<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/imap?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_email' => 'Email',
	'cfg_email_explication' => 'Dirección mail o login',
	'cfg_email_pwd' => 'Contraseña',
	'cfg_hote_imap' => 'Dirección del servidor IMAP',
	'cfg_hote_imap_explication' => 'por ej. imap.gmail.com',
	'cfg_hote_options' => 'Opciones de conexión',
	'cfg_hote_options_explication' => 'Ver <a href="http://php.net/manual/es/function.imap-open.php">los "flags" posibles</a> (por ejemplo: <code>/imap/tls/novalidate-cert</code>)',
	'cfg_hote_port' => 'Puerto',
	'cfg_hote_port_explication' => '143, 993 (SSL) o 993/imap/ssl (gmail) - <a href="http://php.net/manual/es/function.imap-open.php">Infos</a>',
	'cfg_inbox' => 'Buzón',
	'cfg_titre_parametrages' => 'Parámetros de conexión',

	// I
	'imap_titre' => 'IMAP',

	// T
	'test_connexion' => 'Test de conexión al servidor IMAP',
	'test_connexion_notok' => 'Error: no se puede conectar a<br /><i>@connexion@</i>',
	'test_connexion_ok' => '¡Autenticación exitosa!',
	'test_librairie_installee_notok' => 'Error: la librería PHP "imap" no está instalada - Ver <a href="http://www.php.net/manual/es/imap.setup.php">la documentación</a>.',
	'test_parametres_remplis_notok' => 'Llenar los parámetros del servidor antes de probar la conexión IMAP.',
	'titre_page_configurer_imap' => 'Conexión IMAP'
);

?>
