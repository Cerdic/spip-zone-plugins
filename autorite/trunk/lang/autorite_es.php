<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Activar la gestión por palabras claves',
	'admin_complets' => 'Los administradores completos',
	'admin_restreints' => '¿Administradores restringidos?',
	'admin_tous' => 'Todos los administradores (incluidos los restringidos)',
	'administrateur' => 'administrador',
	'admins' => 'Los administradores',
	'admins_redacs' => 'Administradores y Redactores',
	'admins_rubriques' => 'los administradores asociados a secciones tienen:',
	'attention_crayons' => '<small><strong>OjO</strong> Los parámetros a continuación sólo pueden funcionar si utilizas un plugin que propone una interfaz de edición (como por ejemplo <a href="http://www.spip-contrib.net/Les-Crayons">los Lápices</a>).</small>',
	'attention_version' => 'Cuidado, los siguientes parámetrospueden no funcionar con tu versión de SPIP:',
	'auteur_message_advitam' => 'El autor del mensaje, ad vitam',
	'auteur_message_heure' => 'El autor del mensaje, durante una hora',
	'auteur_modifie_article' => '<strong>Autor modifica artículo</strong> : cada redactor puede modificar los artículos publicados de los cuales es autor. (y, en consecuencia, moderar el foro y la recolección de firmas asociada).
	<br />
	<i>OjO: esta opción se aplica también a los visitantes registrados, si son autores y si una interfaz específica está prevista.</i>', # MODIF
	'auteur_modifie_email' => '<strong>Redactor modifica correo electrónico</strong>: cada redactor puede modificar su correo electrónico en la ficha de datos personales.',
	'auteur_modifie_forum' => '<strong>Autor modera foro</strong> : cada redactor puede moderar el foro de los articulos de los cuales él es autor.',
	'auteur_modifie_petition' => '<strong>Autor modera petición</strong> : cada redactor puede moderar la petición de los articulos de los cuales él es autor.',

	// C
	'config_auteurs' => 'Configuración de los autores',
	'config_auteurs_rubriques' => '¿ Qué tipo de autores se pueden <b>asociar a las rubricas</b> ?',
	'config_auteurs_statut' => '¿ En la creación de un autor, cuál es el <b>status por omisión</b> ?',
	'config_plugin_qui' => 'Quién puede <strong>modificar la configuración</strong> de los plugins (activación...) ?',
	'config_site' => 'Configuración del sitio',
	'config_site_qui' => '¿ Quién puede <strong>modificar la configuración</strong> del sitio ?',
	'crayons' => 'Lápices', # MODIF

	// D
	'deja_defini' => 'Las autorizaciones siguientes ya están definidas :',
	'deja_defini_suite' => 'El plugin « Autoridad» no puede modificarlos,  ciertos ajustes  podrían no funcionar.
	<br />Para corregir este problema, deberá verificar si el archivo <tt>mes_options.php</tt> (o algún otro  plugin activo) ha definido estas funciones.', # MODIF
	'descriptif_1' => 'Esta página de configuración está reservada a los webmasters del sitio :',
	'descriptif_2' => '<p>Si desea modificar esta lista, edite el archivo <tt>config/mes_options.php</tt> (o crearlo en dado caso) e indicar la lista de los nombres de usuario de los autores webmasters, de la siguiente forma :</p>
<pre><?php
  define (
  \'_ID_WEBMESTRES\',
  \'1:5:8\');
?></pre>
<p>A partir de SPIP 2.1, también es  posible dar derechos de webmestre a un administrador en la página de edición del autor.</p>
<p>Nota : los webmasters definidos de esta manera ya no tienen necesidad de proceder a la autentificación FTP para operaciones delicadas (mejorar la base de datos, por ejemplo).</p>

<a href=\'http://www.spip-contrib.net/-Autorite-\' class=\'spip_out\'>Documentación</a>
', # MODIF
	'details_option_auteur' => '<small><br />Por el momento, la opctión « autor » sólo funciona para los autores registrados (foros con suscripción, por ejemplo). Y, si está  activada, los administradores del sitio también tienen la capacidad de editar los foros.
	</small>',
	'droits_des_auteurs' => 'Derechos de los autores',
	'droits_des_redacteurs' => 'Derechos de los redactores',
	'droits_idem_admins' => 'los mismos derechos que todos los administradores',
	'droits_limites' => 'derechos limitados para estas rubricas',

	// E
	'effacer_base_option' => '<small><br />La opción recomendada es  « persona », la opción standard de SPIP es « los administradores » (pero siempre con una verificación FTP).</small>',
	'effacer_base_qui' => 'Quién puede <strong>borrar</strong> la base de datos del sitio ?',
	'espace_publieur' => 'Espacio de publicación abierta',
	'espace_publieur_detail' => 'Seleccione de abajo un sector a tratar como un espacio de publicación abierta para los redactores y / o visitantes registrados (a condición de tener una interfaz, por ejemplo los lápices y un formulario para enviar un articulo) :', # MODIF
	'espace_publieur_qui' => 'Desea abrir la publicación — más allá de los administradores :',
	'espace_wiki' => 'Espacio wiki',
	'espace_wiki_detail' => 'Seleccione un sector a tratar como un wiki, es decir, editable por todos desde el espacio público (a condición de tener una  interfaz, por ejemplo los lápices) :', # MODIF
	'espace_wiki_mots_cles' => 'Espacio wiki por palabras clave',
	'espace_wiki_mots_cles_detail' => 'Seleccione las palabras clave que activarán el modo wiki, es decir, editable por todos desde el espacio público (a condición de tener una interfaz, por elemplo los lápices) :',
	'espace_wiki_mots_cles_qui' => 'Desea abrir este wiki — más allá de los administradores :',
	'espace_wiki_qui' => 'Desea abrir este wiki — más allá de los administradores :',

	// F
	'forums_qui' => '<strong>Foros:</strong> quien puede modificar el contenido de los foros :',

	// I
	'icone_menu_config' => 'Acceso restringido',
	'infos_selection' => '(puede seleccionar varios sectores con la tecla shift)',
	'interdire_admin' => 'Marque las casillas de abajo para prohibir a los administradores de crear',

	// M
	'mots_cles_qui' => '<strong>Palabras clave :</strong> quien puede crear y editar las palabras clave :',

	// N
	'non_webmestres' => 'Este ajuste no se aplica a los webmasters.',
	'note_rubriques' => '(Nótese que sólo los administradores pueden crear rubricas, y, para los asministradores restringidos, sólo se puede hacer en sus propias rubricas.)',
	'nouvelles_rubriques' => 'nuevas rubricas en la raíz del sitio',
	'nouvelles_sous_rubriques' => 'nuevas sub-rubricas en la arborescencia.',

	// O
	'ouvrir_redacs' => 'Abrir a los redactores del sitio :',
	'ouvrir_visiteurs_enregistres' => 'Abrir a todos los visitantes registrados :',
	'ouvrir_visiteurs_tous' => 'Abrir a todos los visitantes del sitio :',

	// P
	'pas_acces_espace_prive' => '<strong>Sin acceso al espacio privado :</strong> los redactores no tienen acceso al espacio privado.',
	'personne' => 'Persona',
	'petitions_qui' => '<strong>Firmas:</strong> quien puede modificar las firmas de las peticiones :',
	'publication' => 'Publicación',
	'publication_qui' => 'Quién puede publicar en el sitio:',

	// R
	'redac_tous' => 'Tod*s l*s redactor*s',
	'redacs' => 'a los rédactores del sitio',
	'redacteur' => 'redactor/a',
	'redacteur_lire_stats' => '<strong>Redactor ve estadísticas</strong>: l*s redactor*s pueden visualizar las estadísticas.',
	'redacteur_modifie_article' => '<strong>Redactor modifica propuestos</strong> : cada redactor/a puede modificar un artículo propuesto a la publicación, incluso cuando no es autor/a de éste.',
	'refus_1' => '<p>Sólo l*s webmestres del sitio',
	'refus_2' => 'están autorizados a modificarestos parámetros.</p>
<p>Para mayor información, ver <a href="http://www.spip-contrib.net/-Autorite-">la documentación</a> (por ahora en francés).</p>',
	'reglage_autorisations' => 'Regular las autorizaciones',

	// S
	'sauvegarde_qui' => '¿Quién puede realizar <strong>respaldos</strong> ?',

	// T
	'tous' => 'Tod*s',
	'tout_deselectionner' => ' anular la selección',

	// V
	'valeur_defaut' => '(valor por omisión)',
	'visiteur' => 'visitante',
	'visiteurs_anonymes' => 'los visitantes anónimos pueden crear nuevas páginas.',
	'visiteurs_enregistres' => 'a l*s visitantes registrad*s',
	'visiteurs_tous' => 'a todos los visitantes del sitio.',

	// W
	'webmestre' => 'El o la webmestre',
	'webmestres' => 'Los webmaster'
);

?>
