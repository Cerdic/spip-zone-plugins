<?php
function zotspip_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/zotspip.css').'" type="text/css" />';
	$flux .= '<link rel="unapi-server" type="application/xml" title="unAPI" href="'.url_absolue(generer_url_public('zotspip_unapi','source=zotspip')).'" />';
	return $flux;
}

function zotspip_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/zotspip.css').'" type="text/css" />';
	$flux .= '<link rel="unapi-server" type="application/xml" title="unAPI" href="'.url_absolue(generer_url_public('zotspip_unapi','source=zotspip')).'" />';
	return $flux;
}
?>
