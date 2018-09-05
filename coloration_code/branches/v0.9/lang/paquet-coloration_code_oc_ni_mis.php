<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=oc_ni_mis
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Basta de metre lou code tra
_ {{<code class="langage">...</code>}}
_ o embé un cadre
_ {{<cadre class="langage">...</cadre>}}.

Lu lengage supourtat soun aquelu que soun fournit da [->http://sourceforge.net/projects/geshi/] embé una classa suplementari : "spip".


En mancança, se lou code mes en subrilança fa mai d’una ligna, es mes en l’amagadou souta la forma testuala e proupausat au telecargamen. Aqueu founciounamen es countroulat da una coustanta PLUGIN_COLORATION_CODE_TELECHARGE predefinida a true. Pòu estre fourçat loucalemen en ajustant la classa "sans_telechargement" o "chargement" couma _ {{<code class="php sans_telechargement">}}

Poudès finda utilisà lou filtre {coloration_code_color} en un esquelètrou couma
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : colore #TEXTE emb’au lengage spip en format code (sensa cadre), vèire isemple lecode.html. L’url despì l’article siguèsse <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Coulouramen Code',
	'coloration_code_slogan' => 'Coulouramen sintàssicou dóu code sourgent en lu article'
);
