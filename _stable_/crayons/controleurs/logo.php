<?php

// un controleur php
// NE FONCTIONNE PAS VRAIMENT A CAUSE D'UN MELANGE PRIVE/PUBLIC
function controleurs_logo_dist($regs) {
    list(,$crayon,$type,$champ,$id) = $regs;

	include_spip('inc/minipres'); # pour aide()
	include_spip('inc/presentation'); # pour debut_cadre()
	include_spip('inc/layer'); # pour le js des fleches

	$iconifier = charger_fonction('iconifier', 'inc');
	$html =
		http_script("\nvar ajax_image_searching = \n'<div style=\"float: ".$GLOBALS['spip_lang_right'].";\"><img src=\"".url_absolue(_DIR_IMG_PACK."searching.gif")."\" alt=\"\" /></div>';")
		. http_script('', generer_url_public('jquery.js'))
		. http_script('', _DIR_JAVASCRIPT . 'layer.js','')
		. $iconifier('id_'.$type, $id, 'bizarre');

    $status = NULL;

	return array($html, $status);
}




?>
