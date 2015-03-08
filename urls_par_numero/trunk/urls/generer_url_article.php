<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$cas_propre = array('propres','libres','arbo','propres_qs','propres2');
if (!(
	$GLOBALS['type_urls']=='page'
	or (
		$GLOBALS['type_urls']!='page'
		and in_array($GLOBALS['meta']['type_urls'],$cas_propre)
	))
)
{	
	// Pour les fois où on utilise pas des urls propres
	function urls_generer_url_article_dist($id_article, $args='', $ancre='') {
		return _DIR_RACINE . $id_article . ($args ? "?$args" : '') .($ancre ? "#$ancre" : '');
	};
}
