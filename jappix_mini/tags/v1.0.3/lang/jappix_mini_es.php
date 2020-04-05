<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/noie/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

'jappix_mini' => 'Jappix Mini',
'introduction_configuration' => '<p>Aqu&iacute; puedes configurar los valores por defectos del mini-chat.</p><p>De todas formas, <strong>estos valores se pueden sobrecargar desde los esqueletos</strong> al momento de usar <code>#MODELE{minichat}</code>.</p>',

// Secciones de configuracion
'section_jappix' => 'Jappix',
'section_jabber' => 'Cuenta de Jabber',
'section_salons' => 'Salas',
'section_interface' => 'Interface',

// Seccion Jappix
'url_jappix' => 'URL de Jappix',
'url_jappix_explication' => 'Si instalaste Jappix en tu servidor, entra su direcci&oacute;n aqu&iacute;, o deja este campo vac&iacute;o para usar el Jappix que corre en static.jappix.com.',

// Seccion Jabber
'utilisateur_jabber' => 'Usuario',
'utilisateur_jabber_explication' => 'Nombre de usuario de la cuenta de Jabber (deja vac&iacute;o para una conecci&oacute;n an&oacute;nima).',
'pass_jabber' => 'Contrase&ntilde;a',
'pass_jabber_explication' => 'Contrase&ntilde;a de la cuenta de Jabber (deja vac&iacute;o para una conecci&oacute;on an&oacute;nima).',
'domaine_jabber' => 'Dominio',
'domaine_jabber_explication' => 'Servidor de la cuenta de Jabber. Si dejas este campo vac&iacute;o, se usar&aacute; el servidor an&oacute;nimo de jappix.com.',
'ressource' => 'Recurso',
'ressource_explication' => 'Recurso que se va a usar. Si dejas este campo vac&iacute;o, se usar&aacute; "Jappix Mini".',

// Seccion Salas
'liste_salons' => 'Lista de salas',
'liste_salons_explication' => 'Indicar aqu&iacute; la lista de salas a las que se entrar&aacute; (una por l&iacute;nea).',
'liste_pass_salons' => 'Contrase&ntilde;as',
'liste_pass_salons_explication' => 'Si las salas est&aacute;n protegidas por claves, entrarlas en este campo (una por l&iacute;nea).',
'entrer_automatiquement' => 'Entrar autom&aacute;ticamente',
'entrer_automatiquement_explication' => 'Entrar autom&aacute;ticamente a las salas al cargar la p&aacute;gina',
'pseudo_salons' => 'Apodo predefinido',
'pseudo_salons_explication' => 'Apodo que se quiere usar en las salas. Si no pones nada, los visitantes podr&aacute;n elegir su apodo al momento de entrar.',

// Seccion interface
'derouler_panneau_auto' => 'Abrir al entrar',
'derouler_panneau_auto_explication' => 'Mostrar la ventana del minichat cuando se entre a la sala',
'langue_interface' => 'Idioma',
'langue_interface_explication' => 'Idioma de la interface de mini-chat.',
'langue_interface_defaut' => 'Idioma de la p&aacute;gina',

);

?>
