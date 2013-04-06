<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-spip_400?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spip_400_description' => 'Este plugin completa la distribución de SPIP proponiendo modelos de páginas de error HTTP ({códigos 401 y 404}) con un texto explicativo y la posibilidad para el internauta de transmitir un "ticket de error de software (bug)" al administrador del sitio.

Propone particularmente: 
-* un mensaje sobre las páginas públicas para que el internauta no se pierda;
-* el envío de un correo electrónico al administrador con información completa sobre el error en cuestión ({usuario SPIP, URL, REFERENTE, backtrace PHP, etc.}); 
-* la escritura de mensajes de LOG en un archivo específico;',
	'spip_400_nom' => 'SPIP 400',
	'spip_400_slogan' => 'Gestión impulsada de errores HTTP (401, 404) para SPIP'
);

?>
