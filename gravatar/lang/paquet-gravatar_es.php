<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-gravatar?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// G
	'gravatar_description' => 'Permite utilizar una caché para almacenar los gravatars.
_ A utilizar en un bucle de esta manera: <code>#GRAVATAR{correo electrónico, tamaño, imagen url por defecto}</code>
_ Ejemplo: <code>#GRAVATAR{#EMAIL,80,#URL_SITE_SPIP/defaut-gravatar.gif}</code>

Extiende la etiqueta #LOGO_AUTEUR de modo para tomar en cuenta el gravatar de un autor si existe, y comprendido en foros y peticiones.
_ Permite configurar una imagen por defecto, y el tamaño de las imágenes.

Proporciona el filtro <code>|gravatar</code>, a utilizar por ejemplo como
<code>[(#EMAIL|gravatar|image_reduire{80})]</code>.',
	'gravatar_slogan' => 'Mostrar el Gravatar de un autor o de un colaborador de foro'
);

?>
