<?php

/**
 * 
 * \file tinymce.php
 * \brief Fonctions appelées par les pipeline SPIP
 * \author Brice Favre bfavre@alternancesoft.com
 * \date 12.09.2006
 *
 * Modifications :
 * Auteur       Date    Commentaire
 */

function TinyMCE_header_prive($flux) {
	$exec = _request('exec');
	if ($exec == "articles_edit") {
		$flux .= '<script language="javascript" type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/jscripts/tiny_mce/tiny_mce.js"></script>'."\n";
		$flux .= '<script language="javascript" type="text/javascript">'."\n";
		$flux .= 'tinyMCE.init({
			language: "fr",
			mode: "exact",
			elements: "text_area",
			theme_advanced_toolbar_location: "top",
			theme_advanced_buttons1: "bold,italic,underline,strikethrough,separator,justifyleft,justifyright,justifycenter,justifyfull,separator,formatselect",
			theme_advanced_buttons2: "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,image,code",
			theme_advanced_buttons3: ""
			
		});
		</script>';
	}
	
	return $flux;
}
?>
