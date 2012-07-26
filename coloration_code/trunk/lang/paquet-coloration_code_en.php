<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'You just have to put it between the tags:
_ {{&lt;code class="language"&gt;...&lt;/code&gt;}}
_ or with a frame
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

The possible languages are the ones supported by [->http://sourceforge.net/projects/geshi/] with an extra class: "spip".


By default, when the highlighted code has more than one line, il will be put in cache as text and proposed to upload. This feature may be controlled globaly through the constant PLUGIN_COLORATION_CODE_TELECHARGE default true. It may also be forced locally by adding a class "sans_telechargement" in a way or "chargement" in the other as
_ {{&lt;code class="php sans_telechargement"&gt;}}

You may also use {coloration_code_color} as a filter in a squelette as 
_ <code>#TEXTE**|coloration_code_color{spip,code}</code>: highlights #TEXTE with langage "spip" in format code (without frame), see example lecode.html. From article url would be <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Code higlighting',
	'coloration_code_slogan' => 'Syntax higlighting of code placed in articles'
);

?>
