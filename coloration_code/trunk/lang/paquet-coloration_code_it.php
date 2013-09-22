<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=it
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Dovete solo utilizzare il segnaposto:
_ {{&lt;code class="linguaggio"&gt;...&lt;/code&gt;}}
_ o con riquadro
_ {{&lt;cadre class="linguaggio"&gt;...&lt;/cadre&gt;}}

Sono supportati tutti i linguaggi di [->http://sourceforge.net/projects/geshi/] ed in aggiunta la classe "spip".

In maniera predefinita, Se il codice da colorare ha più di una linea, sarà salvato in cache sotto forma di testo semplice e disponibile per lo scaricamento. Questa impostazione può essere modificata globalmente grazie alla costante PLUGIN_COLORATION_CODE_TELECHARGE, che normalmente è impostata a true. Quest’ultima può anche essere modificata localmente aggiugendo la classe "sans_telechargement" o "chargement", es:
_ {{&lt;code class="php sans_telechargement"&gt;}}

_ Potete utilizzare {coloration_code_color} anche come un filtro in scheletro in questo modo: 
_ <code>#TEXTE**|coloration_code_color{spip,code}</code>: colora #TEXTE con le regole del linguaggio "spip" in formato code (senza il riquadro), vedi l’esempio lecode.html. L’url per vedere il codice dell’articolo dalla sua stessa pagina sará: <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Colorazione del codice',
	'coloration_code_slogan' => 'Colorazione sintattica del codice utilizzato nel testo'
);

?>
