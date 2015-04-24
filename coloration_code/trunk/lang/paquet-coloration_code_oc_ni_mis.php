<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=oc_ni_mis
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Basta de metre lou code tra
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ o embé un cadre
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Lu lengage supourtat soun aquelu que soun fournit da [->http://sourceforge.net/projects/geshi/] embé una classa suplementari : "spip".


En mancança, se lou code metut en subrilança fa mai d’una ligna, es metut en l’amagadou souta la forma testuala e proupausat au telecargamen. Aqueu founciounamen es countroulat da una coustanta PLUGIN_COLORATION_CODE_TELECHARGE predefinida a true. Pòu estre fourçat loucalemen en ajustant la classa "sans_telechargement" o "chargement" couma _ {{&lt;code class="php sans_telechargement"&gt;}}

Poudès finda utilisà lou filtre {coloration_code_color} en un esquelètrou couma
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : colore #TEXTE embé lou lengage spip en format code (sensa cadre), vèire isemple lecode.html. L’url despì l’article siguèsse <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Coulouramen Code',
	'coloration_code_slogan' => 'Coulouramen sintàssicou dóu code sourgent en lu article'
);

?>
