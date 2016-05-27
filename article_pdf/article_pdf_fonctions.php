<?php

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ARTICLE_PDF',(_DIR_PLUGINS.end($p)));

function pdf_first_clean_prepropre($texte){
	// Cette fonction est appelé avant propre.
	$texte = preg_replace('#(<code class=(\'|")([\w]+)(\'|")>)#','<code>',$texte);	// si on a coloration code, on décolorie d'abord
	$texte = preg_replace('#(<cadre class=(\'|")([\w]+)(\'|")>)#','<cadre>',$texte);
	return propre($texte);
}

function pdf_first_clean($texte){
	// Cette focntion est appelé après la fonction propre
	// $texte = ereg_replace("<p class[^>]*>", "<P>", $texte);
	//Translation des codes iso
	// PB avec l'utilisation de <code>
	$trans = get_html_translation_table(HTML_ENTITIES);
	$texte = preg_replace(',<!-- .* -->,msU', '', $texte); // supprimer les remarques HTML (du Couteau Suisse ?)
	$trans = array_flip($trans);
	$trans["<br />\n"] = "<BR>";        // Pour éviter que le \n ne se tranforme en espace dans les <DIV class=spip_code> (TT, tag SPIP : code)
	$trans['&#176;'] = "°";
	$trans["&#339;"] = "oe";
	$trans["&#8206;"] = "";
	$trans["&#8211;"] = "-";
	$trans["&#8216;"] = "'";
	$trans["&#8217;"] = "'";
	$trans["&#8220;"] = "\"";
	$trans["&#8221;"] = "\"";
	$trans["&#8230;"] = "...";
	$trans["&#8364;"] = "Euros";
	$trans["&ucirc;"] = "û";
	$trans['->'] = '-»';
	$trans['<-'] = '«-';
	$trans['&nbsp;'] = ' ';
	// certains titles font paniquer l'analyse
	$texte = preg_replace(',title=".*",msU', 'title=""', $texte);
	
	$texte = unicode2charset(charset2unicode($texte), 'iso-8859-1'); // repasser tout dans un charset acceptable par export PDF
	$texte = strtr($texte, $trans);

	return $texte;
}

function filtre_supprimer_param_logo($texte){
	return preg_replace('`\?[0-9]*$`','',$texte);
}
