<?php
/*
 * multilang_mot_cles
 *
 * Copyright (c) 2006 Renato Formato (renatoformato@virgilio.it)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 */

function multilang_mot_cles_header_prive($flux) {
	if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui") {
		$active_langs = "'".str_replace(",","','",$GLOBALS['meta']['langues_multilingue'])."'";
		$flux .= "<script type='text/javascript' src='".find_in_path("multilang_mots.js")."'></script>\n".
		"<script type='text/javascript'>\n".
		"var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n".
		"$(function(){multilang_init_lang('exec=mots_edit','.cadre-formulaire','','','input[@name=\'titre\'],textarea')});\n".
		"</script>\n";
	}
	return $flux;
}

?>
