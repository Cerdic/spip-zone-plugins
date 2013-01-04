<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Basta con poner el código entre
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ o con un marco
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Los idiomas tolerados son aquellos proporcionados por
[->http://sourceforge.net/projects/geshi/] con una clase suplementaria: "SPIP".


Por defecto, si el código puesto de relieve ocupa más de una línea, éste se almacena en caché en forma de texto y disponible para su descarga. Toda esta operación se controla por una constante PLUGIN_COLORATION_CODE_TELECHARGE por defecto "true". Puede forzarse a nivel local añadiendo la clase "sans_telechargement" en un sentido o "chargement" en el otro como
_ {{&lt;code class="php sans_telechargement"&gt;}}

También puede utilizar el filtro {coloration_code_color} en un esqueleto como
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : colorea #TEXTE con el lenguaje SPIP en formato código (sin marco), ver ejemplo lecode.html. La url para el artículo sería <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Código de coloración',
	'coloration_code_slogan' => 'Coloración de sintaxis del código fuente de los artículos'
);

?>
