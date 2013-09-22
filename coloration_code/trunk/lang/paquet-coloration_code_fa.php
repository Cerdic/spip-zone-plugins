<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=fa
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'كافي است بين دو برچسب گذاشته شوند
_ {{<code class="langage">...</code>}}
_ يا با يك كادر
_ {{<cadre class="langage">...</cadre>}}.

زبان‌هاي مورد حمايت آن‌هايي هستند كه توسط  [->http://sourceforge.net/projects/geshi/] و بايك كلاس تكميلي تدارك شوند  : "spip".

به صورت پيش‌گزيده، اگر كد برجسته‌ شده بيش از يك خط باشد، مانند يك متن در حافظه‌ي نزديك گذاشته خواهد شد تا بارگذاري شود. اين خصوصيت مي‌تواند به صورت جهاني توسط يك ثابت PLUGIN_COLORATION_CODE_TELECHARGE كتنرل شود.

 Il peut être forcé localement en rajoutant la classe "sans_telechargement" dans un sens ou "chargement" dans l’autre comme
_ {{<code class="php sans_telechargement">}}

Vous pouvez aussi utiliser le filtre {coloration_code_color} dans un squelette comme
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : colore #TEXTE avec le language spip en format code (sans cadre), voir exemple lecode.html. L’url depuis l’article serait <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'كد برجسته سازي ',
	'coloration_code_slogan' => 'سينتاكس برجسته‌ سازي منبع كد در داخل مقاله‌ها'
);

?>
