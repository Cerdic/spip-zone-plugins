<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/rubriques');
include_spip('inc/logos');
include_spip('inc/mots');
include_spip('inc/date');
include_spip('inc/documents');
include_spip('inc/forum');
include_spip('base/abstract_sql');
include_spip('inc/sale');

function exec_test_sale(){
	$id_article=intval(_request('id_article'));
	
	$query = "SELECT texte FROM spip_articles WHERE id_article=$id_article";
	

	$row = spip_fetch_array(spip_query($query));
	$texte = $row['texte'];
	$texte_propre = propre($texte);
	$texte_bis = sale($texte_propre);
	
	echo "<textarea style='width:30%;height:100%;float:left'>";
	echo entites_html($texte);
	echo "</textarea>";
	echo "<textarea style='width:30%;height:100%;float:left'>";
	echo entites_html($texte_propre);
	echo "</textarea>";
	echo "<textarea style='width:30%;height:100%;float:left'>";
	echo entites_html($texte_bis);
	echo "</textarea>";
	
}