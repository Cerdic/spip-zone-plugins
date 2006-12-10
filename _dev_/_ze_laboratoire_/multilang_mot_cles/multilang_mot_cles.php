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
		"$(function(){\n".
		"multilang_init_lang({'page':'exec=mots_edit','root':'.cadre-formulaire','fields':'input[@name=\'titre\'],textarea'});\n".
		"multilang_init_lang({'page':'exec=mots_type','root':'#page','form_menu':'div.cadre-formulaire:eq(0)','fields':'input[@name=\'change_type\'],textarea'});\n".
		"multilang_init_lang({'page':'exec=articles_edit','root':'#page','forms':'#liste_images form,#liste_documents form','fields':'input,textarea'});\n".
		"multilang_init_lang({'page':'exec=articles','root':'#portfolio,#documents','fields':'input,textarea'});\n".
		"onAjaxLoad(function(){forms_init_multi({'target':this})});\n".
		"});\n".
		"</script>\n";
	}
	return $flux;
}

?>
