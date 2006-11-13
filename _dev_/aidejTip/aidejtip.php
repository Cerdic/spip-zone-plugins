<?php

function aidejtip_header_prive($texte) {
	$exec = _request('exec');
	if ($exec == "articles_edit") {
	$texte.= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_AIDEJTIP.'/css/global.css" />' . "\n";
	$texte .= '<script language="javascript" type="text/javascript" src="'._DIR_PLUGIN_AIDEJTIP.'/js/jtip.js"></script>'."\n";
	}
	return $texte;
}


?>