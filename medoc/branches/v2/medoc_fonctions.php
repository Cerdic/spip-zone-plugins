<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_BOUTON_MODE_IMAGE', true);

function filtre_medias_raccourcis_doc($id_document,$titre,$descriptif,$inclus,$largeur,$hauteur,$mode,$vu,$media=null){
	$raccourci = '';
	$doc = 'doc';
	include_spip('inc/documents'); // pour la fonction affiche_raccourci_doc
	
	if ($mode=='image' AND (strlen($descriptif.$titre) == 0))
		$doc = 'img';

	// Affichage du raccourci <doc...> correspondant
	$raccourci = 
		  affiche_raccourci_doc($doc, $id_document, 'left')
		. affiche_raccourci_doc($doc, $id_document, 'center')
		. affiche_raccourci_doc($doc, $id_document, 'right');
	if ($mode=='document'
		AND ($inclus == "embed" OR $inclus == "image")
		AND (($largeur > 0 AND $hauteur > 0) 
		OR in_array($media,array('video','audio')))) {
		$raccourci =
		  "<span>"._T('medias:info_inclusion_vignette')."</span>"
		. $raccourci
		. "<span>"._T('medias:info_inclusion_directe')."</span>"
		. affiche_raccourci_doc('doc', $id_document.'|emb', 'left')
		. affiche_raccourci_doc('doc', $id_document.'|emb', 'center')
		. affiche_raccourci_doc('doc', $id_document.'|emb', 'right');
	}
	return "<div class='raccourcis'>".$raccourci."</div>";
}

?>