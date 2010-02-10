<?php
/*
 * securizator (sortez couvert !)
 * licence GPL
 * auteur denisb
 */

function securizator_insert_head_prive($flux) {
	if (autoriser('webmestre') &&
		in_array(_request('exec'), array('accueil', NULL)) &&
		!defined('_ALERTE_SECURIZATOR')) {
			$flux .= "<script type=\"text/javascript\"><!--\n"
				. "$(document).ready( function() {\n"
				. "  rep_interdits = new Array('".url_absolue(_DIR_TMP)."', '".url_absolue(_DIR_ETC)."');\n"
				. "  $.each(rep_interdits, function (i, rep) {\n"
				. "    $.get(rep, function(data) {\n"
				. "      if (data.length) alert(\""._T('securizator:htacces_invalide_1')."\" + rep + \""._T('securizator:htacces_invalide_2')."\");"
				. "    });\n"
				. "  });\n"
				. "});\n"
				. "// --></script>\n";
	}
	return $flux;
}

?>