<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function opensearch_insert_head($flux){
	$nom_site = textebrut(typo($GLOBALS['meta']['nom_site']));
	$url_opensearch = url_absolue(generer_url_public('opensearch.xml'));
	$flux .= '<link rel="search" type="application/opensearchdescription+xml" title="'.$nom_site.'" href="'.$url_opensearch.'" />';
	return $flux;
}

?>