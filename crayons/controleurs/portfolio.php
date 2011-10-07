<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// un controleur php (TODO -- NE FONCTIONNE PAS DU TOUT)

function controleurs_portfolio_dist($regs) {
    list(,$crayon,$type,$champ,$id) = $regs;

	include_spip('inc/minipres'); # pour aide()
	include_spip('inc/presentation'); # pour debut_cadre()
	include_spip('inc/layer'); # pour le js des fleches
	include_spip('inc/documents'); # pour aide()

	$html =
		http_script("\nvar ajax_image_searching = \n'<div style=\"float: ".$GLOBALS['spip_lang_right'].";\"><img src=\"".url_absolue(_DIR_IMG_PACK."searching.gif")."\" alt=\"\" /></div>';")
		. http_script('', generer_url_public('jquery.js'))
		. http_script('', _DIR_JAVASCRIPT . 'layer.js','')
		. afficher_documents_colonne($id, $type, 'portfolio');

    $status = NULL;

	return array($html, $status);
}




?>
