<?php

define('_BOUTON_MODE_IMAGE', true);

function media_raccourcis_media($id_document){
	$raccourci = '';

	$raccourci = 
		  "<span>"._T('media:info_inclusion_icone')."</span>"
		. affiche_raccourci_media($id_document,'icone',  'left')
		. affiche_raccourci_media($id_document,'icone',  'center')
		. affiche_raccourci_media($id_document,'icone',  'right');
	$raccourci .= 
		  "<span>"._T('info_inclusion_vignette')."</span>"
		. affiche_raccourci_media($id_document,'vignette',  'left')
		. affiche_raccourci_media($id_document,'vignette',  'center')
		. affiche_raccourci_media($id_document,'vignette',  'right');
	$raccourci .= 
		  "<span>"._T('info_inclusion_directe')."</span>"
		. affiche_raccourci_media($id_document,'embed',  'left')
		. affiche_raccourci_media($id_document,'embed',  'center')
		. affiche_raccourci_media($id_document,'embed',  'right');
	return "<div class='raccourcis'>".$raccourci."</div>";
}

function affiche_raccourci_media($id, $variante, $align) {
	static $num = 0;

	$variante = ($variante) ? "|$variante" : "";
	if ($align) {
		$pipe = "|$align";
		if ($GLOBALS['browser_barre'])
			$onclick = "\nondblclick=\"barre_inserer('\\x3Cmedia$id$variante$pipe&gt;', $('textarea[name=texte]')[0]);\"\ntitle=\"". str_replace('&amp;', '&', entites_html(_T('double_clic_inserer_doc')))."\"";
	} else {
		$align='center';
	}

	return
	  ((++$num > 1) ? "" : http_script('',  "spip_barre.js"))
		. "\n<div style='text-align: $align'$onclick>&lt;media$id$variante$pipe&gt;</div>\n";
}

?>