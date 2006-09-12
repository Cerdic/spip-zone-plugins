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
			mode: "exact",
			elements: "text_area"
		});
		</script>';
	}
	
	return $flux;
}
?>
