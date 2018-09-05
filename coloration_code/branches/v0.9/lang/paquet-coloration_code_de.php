<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Es genügt, den Code zwischen folgende Tags zu setzen,
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ bzw. diese um einen Kasten zu erzeugen
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Die verfügbaren Sprachen werden bereitgestellt vom [->http://sourceforge.net/projects/geshi/] ergänzt um die Klasse "spip".


In der Grundeinstellung wird derart hervorgehobener Code, der länger als eine Zeile ist, in Textform zwischengespeichert und kann heruntergeladen werden. Dieses Verhalten wird von der globalen Variable PLUGIN_COLORATION_CODE_TELECHARGE gesteuert (Grundeinstellung true). Durch die Klasse "sans_telechargement" kann die Downloadmöglichkeit einzeln ab- bzw. mit der Klasse "chargement" angeschaltet werden. Beispiel:
_ {{&lt;code class="php sans_telechargement"&gt;}}

Sie können den Filter {coloration_code_color} mit der folgenden Syntax in einem Skelett verwenden
_ <code>#TEXTE**|coloration_code_color{spip,code}</code> : koloriert #TEXTE mit der SPIP-Sprache als Code (ohne Rahmen); siehe Beispieldatei lecode.html. Der URL im Artikel wäre hier <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Kolorierter Code',
	'coloration_code_slogan' => 'Koloriert syntaktische Elemente im Code von Artikeln'
);
