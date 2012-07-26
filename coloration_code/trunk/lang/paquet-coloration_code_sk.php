<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Jednoducho dajte kód do
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ alebo do rámu
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Podporované jazyky sú tie, ktoré ponúka [->http://sourceforge.net/projects/geshi/] s doplnkovou triedou: "spip".


Ak je podľa predvolených nastavení kód zvýraznený viac ako jednou čiarou, je kešovaný vo forme textu a je k dispozícii na stiahnutie.  Celú túto operáciu globálne ovláda  konštanta PLUGIN_COLORATION_CODE_TELECHARGE v predvolených nastaveniach nastavená na hodnotu "true". Zvýraznenie môže byť lokálne vynútené pridaním triedy "sans_telechargement" (= bez stiahnutia) na jednej strane alebo pre "nahrávanie" na strane druhej
_ {{&lt;code class="php sans_telechargement"&gt;}}

Môžete tiež využiť filter {coloration_code_color} v šablóne, ako
_ <code>#TEXTE**|coloration_code_color{spip,code}</code>:  colore #TEXTE s jazykom spip vo formáte kódu (bez rámu), pozrite si príklad lecode.html. Internetová adresa pre článok by bola <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Kód zvýraznenia',
	'coloration_code_slogan' => 'Zvýraznenie syntaxe zdrojového kódu v článkoch'
);

?>
