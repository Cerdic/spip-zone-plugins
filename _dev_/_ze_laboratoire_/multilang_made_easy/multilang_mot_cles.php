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
	$page = _request("exec");
  $active_langs = "'".str_replace(",","','",$GLOBALS['meta']['langues_multilingue'])."'"; 

	if($page=="articles") {
    if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui" || $GLOBALS['meta']['multi_secteurs']=="oui") { 
              $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
              "<script type='text/javascript'>\n". 
              "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
              "$(function(){\n". 
              "multilang_init_lang({'root':'#portfolio,#documents','forms':'form:not(.form_upload)','fields':'input,textarea'});\n".
              "onAjaxLoad(function(){forms_init_multi({'target':this})});\n". 
              "});\n". 
              "</script>\n"; 
      }                	
	}
	if($page=="articles_edit") {
		if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui" || $GLOBALS['meta']['multi_secteurs']=="oui") { 
            $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
            "<script type='text/javascript'>\n". 
            "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
            "$(function(){\n". 
            "multilang_init_lang({'root':'#page','forms':'#liste_images form,#liste_documents form','fields':'input,textarea'});\n". 
            "onAjaxLoad(function(){forms_init_multi({'target':this})});\n". 
            "});\n". 
            "</script>\n"; 
    } 	
	}
	if($page=="mots_edit") {
		if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui" || $GLOBALS['meta']['multi_secteurs']=="oui") { 
		      $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
		      "<script type='text/javascript'>\n". 
		      "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
		      "$(function(){\n". 
		      "multilang_init_lang({'root':'.cadre-formulaire','fields':'input[@name=\'titre\'],textarea'});\n". 
		      "});\n". 
		      "</script>\n"; 
		} 
	}
	if($page=="mots_type") {
		if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui" || $GLOBALS['meta']['multi_secteurs']=="oui") { 
	        $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
	        "<script type='text/javascript'>\n". 
	        "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
	        "$(function(){\n". 
	        "multilang_init_lang({'root':'#page','form_menu':'div.cadre-formulaire:eq(0)','fields':'input[@name=\'change_type\'],textarea'});\n". 
	        "});\n". 
	        "</script>\n"; 
	  } 	
	}
	if($page=="naviguer") {
    if($GLOBALS['meta']['multi_rubriques']!="oui" && $GLOBALS['meta']['multi_secteurs']!="oui") { 
          $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
          "<script type='text/javascript'>\n". 
          "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
          "$(function(){\n". 
          "multilang_init_lang({'root':'#portfolio,#documents','forms':'form:not(.form_upload)','fields':'input,textarea'});\n".
          "onAjaxLoad(function(){forms_init_multi({'target':this})});\n". 
          "});\n". 
          "</script>\n"; 
    }       	
	}
	if($page=="rubriques_edit") {
	  if($GLOBALS['meta']['multi_rubriques']!="oui" && $GLOBALS['meta']['multi_secteurs']!="oui") { 
            $flux .= "<script type='text/javascript' src='"._DIR_JAVASCRIPT."multilang.js'></script>\n". 
            "<script type='text/javascript'>\n". 
            "var multilang_def_lang='".$GLOBALS["spip_lang"]."';var multilang_avail_langs=[$active_langs];\n". 
            "$(function(){\n". 
            "multilang_init_lang({'root':'#page','forms':'#liste_images form,#liste_documents form:not(.form_upload) , div.cadre-formulaire form','fields':'input,textarea'});\n". 
            "onAjaxLoad(function(){forms_init_multi({'target':this})});\n". 
            "});\n". 
            "</script>\n"; 
	  } 
	}
	
	return $flux;
}

?>
