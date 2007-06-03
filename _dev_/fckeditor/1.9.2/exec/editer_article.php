<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

// http://doc.spip.org/@editer_article_texte
function editer_article_texte($texte, $config, $aider)
{
	$att_text = " class='formo' "
	. $GLOBALS['browser_caret']
	. " rows='"
	. ($config['lignes'] +15)
	. "' cols='40'";

	if ($config['afficher_barre']) {
		include_spip('inc/barre');
		$afficher_barre = '<div>' 
		.  afficher_barre('document.formulaire.texte')
		. '</div>';
	} else $afficher_barre = '';

	$texte = entites_html($texte);
	 // texte > 32 ko -> decouper en morceaux
//	if (strlen($texte)>29*1024) {
//	  list($texte, $sup) = editer_article_recolle($texte, $att_text);
//	} else $sup='';

	return	"\n<p><b>" ._T('info_texte') ."</b>"
	. $aider ("arttexte") . "<br />\n" 
	. _T('texte_enrichir_mise_a_jour')
	. $aider("raccourcis")
	. "</p>"
	. $sup
	. "<br />"
	. $afficher_barre
	.  "<textarea id='text_area' name='texte'$att_text>"
	.  $texte
	. "</textarea>\n"
	. (_DIR_RESTREINT ? '' : "<script type='text/javascript'><!--\njQuery(hauteurTextarea);\n//--></script>\n");
}


?>
