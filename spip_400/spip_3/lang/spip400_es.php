<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/spip400?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 4
	'401_error' => 'No dispone de autorización suficiente para acceder a la página o al documento solicitado...',
	'401_error_comment_connected' => '{{Contacte por favor con el administrador del sitio para acceder...}}

El acceso a esta página o a este documento precisa estar debidamente autorizado e identificado. Parece que sus derechos de acceso son insuficientes...',
	'401_error_comment_notconnected' => '{{Identifíquese por favor a continuación para acceder...}}

El acceso a esta página o a este documento precisa estar debidamente autorizado e identificado. Si usted está autorizado, conéctese mediante el siguiente formulario.',
	'404_error' => 'La página o el documento que solicita no se encuentra en el sitio...',
	'404_error_comment' => '{{Discúlpenos por favor por este contratiempo}}

Algunas páginas webs no son permanentes o cambian de URL regularmente ({dirección de acceso ubicada en la barra de navegación}). 

Para facilitar su navegación, le aconsejamos las siguientes acciones:
-* verifique la URL que tiene en la barra de dirección de su navegador y asegúrese que esta completa,
-* accceda [al plan del sitio|Lista exhaustiva de las páginas del sitio->@plan@] para buscar la página deseada,
-* efectúe una búsqueda en el área de búsqueda de la página introduciendo palabras clave de la página deseada,
-* regrese al [inicio del sitio|Regreso a la página de inicio->@sommaire@] para recomenzar desde la raíz de la jerarquía,
-* transmita un informe de error a los administradores del sitio para corregir el enlace roto utilizando el siguiente botón.

Por último, numerosos sitios web disponen de uno o varios espacios  reservados a sus administradores o abonados que precisan de una conexión. Si está autorizado, [haga click aquí para acceder a la plataforma de conexión del sitio|Le serán demandados identificadores->@ecrire@].',

	// B
	'backtrace' => 'Backtrace PHP',

	// C
	'cfg_comment_email' => 'Utilice los siguientes campos para elegir las direcciones de correo electrónico de envío y recepción de los informes de errores ({estos informes se envían cuando el internauta hace click sobre el botón en cuestión - por defecto, se emplea el correo electrónico del administrador}).',
	'cfg_descr' => 'Aquí puede definir algunas opciones del plugin " Gestión de Errores HTTP".',
	'cfg_label_receipt_email' => 'Dirección de correo destinatario de los informes de error',
	'cfg_label_sender_email' => 'Dirección de correo de envío de los informes de errores',
	'cfg_label_titre' => 'Configuración del gestor de errores HTTP 400',

	// E
	'email_webmestre' => 'Correo electrónico administrador',
	'email_webmestre_ttl' => 'Inserción automática del correo electrónico del administrador',

	// H
	'http_headers' => 'Membretes HTTP',

	// R
	'referer' => 'Referente',
	'report_a_bug' => 'Informe de incidente',
	'report_a_bug_comment' => 'Puede remitir un informe de incidente sobre el error que encuentre al administrador del sitio haciendo click sobre el siguiente botón.',
	'report_a_bug_envoyer' => 'Enviar el informe',
	'report_a_bug_message_envoye' => 'OK - Se ha transmitido un informe de error de software (bug). Gracias.',
	'report_a_bug_texte_mail' => 'La página "@url@" ha reenviado un código error HTTP @code@ el @date@.',
	'report_a_bug_titre_mail' => '[@sitename@] Informe de error HTTP @code@',
	'report_an_authorized_bug_comment' => 'Si cree que se trata de un error o de una mala evaluación de sus derechosm puede remitir un informe de incidente al administrador del sitio haciendo click en el siguiente botón. La información se transmite automáticamente (<i>página demandada y sus identificadores</i>).',
	'request_auth_message_envoye' => 'OK - Su solicitud se ha transmitido. Gracias.',
	'request_auth_texte_mail' => 'El usuario "@user@" ha solicitado autorización para acceder a la página "@url@" el @date@.',

	// S
	'session' => 'Sesión usuario',
	'session_only_notempty_values' => '(sólo los valores no vacíos son inscritos)',
	'spip_400' => 'SPIP 400',

	// U
	'url_complete' => 'URL completa',
	'utilisateur_concerne' => 'Usuario en cuestión: '
);

?>
