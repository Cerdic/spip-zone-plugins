<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/coloration_code/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Il suffit de mettre le code entre
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ ou avec un cadre
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Les langages supportés sont ceux fournis par [->http://sourceforge.net/projects/geshi/] avec une classe supplementaire : "spip".


Par défaut, si le code mis en surbrillance fait plus d’une ligne, il est mis en cache sous forme textuelle et proposé au téléchargement. Ce fonctionnement est controlé globalement par une constante PLUGIN_COLORATION_CODE_TELECHARGE défaut true. Il peut être forcé localement en rajoutant la classe "sans_telechargement" dans un sens ou "chargement" dans l’autre comme
_ {{&lt;code class="php sans_telechargement"&gt;}}

Vous pouvez aussi utiliser le filtre {coloration_code_color} dans un squelette comme
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : colore #TEXTE avec le language spip en format code (sans cadre), voir exemple lecode.html. L’url depuis l’article serait <code>#URL_SITE_SPIP/spip.php ?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Coloration Code',
	'coloration_code_slogan' => 'Coloration syntaxique du code source dans les articles'
);

?>
