<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

define('_BOUTON_MODE_IMAGE', true);

function media_raccourcis_doc($id_document){
	$raccourci = '';

	$raccourci = 
		  "<span>"._T('media:info_inclusion_icone')."</span>"
		. affiche_raccourci_media('media', $id_document, 'icone', 'left')
		. affiche_raccourci_media('media', $id_document,'icone',  'center')
		. affiche_raccourci_media('media', $id_document,'icone',  'right');
	$raccourci .= 
		  "<span>"._T('medias:info_inclusion_vignette')."</span>"
		. affiche_raccourci_media('media', $id_document,'vignette',  'left')
		. affiche_raccourci_media('media', $id_document,'vignette',  'center')
		. affiche_raccourci_media('media', $id_document,'vignette',  'right');
	$raccourci .= 
		  "<span>"._T('medias:info_inclusion_directe')."</span>"
		. affiche_raccourci_media('media', $id_document,'insert',  'left')
		. affiche_raccourci_media('media', $id_document,'insert',  'center')
		. affiche_raccourci_media('media', $id_document,'insert',  'right');
	return "<div class='raccourcis'>".$raccourci."</div>";
}

function affiche_raccourci_media($doc, $id, $variante, $align) {
	static $num = 0;

	if ($align) {
		$pipe = "|$align";
		$onclick = "\nondblclick=\"barre_inserer('\\x3C$doc$id|$variante$pipe&gt;', $('textarea[name=texte]')[0]);\"\ntitle=\"". str_replace('&amp;', '&', entites_html(_T('medias:double_clic_inserer_doc')))."\"";
	} else {
		$align='center';
	}

	return "\n<div style='text-align: $align'$onclick>&lt;$doc$id|$variante$pipe&gt;</div>\n";
}

?>