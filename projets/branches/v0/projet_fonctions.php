<?php
function puce_statut_projet($id, $statut, $rubrique, $type='projet', $ajax = false) {
	global $lang_objet;
	static $coord = array('prepa' => 0, 'publie' => 1, 'poubelle' => 2);

	$lang_dir = lang_dir($lang_objet);
	if (!$id) {
	  $ajax_node ='';
	} else	$ajax_node = " id='imgstatut$type$id'";

	$inser_puce = img_puce_statut_projet($statut, " width='9' height='9' style='margin: 1px;'$ajax_node");

	$nom = "puce_statut_";

	return 	"<span class='puce_article' id='$nom$type$id' dir='$lang_dir'>"
	. $inser_puce
	. '</span>';
}

function img_puce_statut_projet($statut, $atts='') {
	switch ($statut) {
		case 'publie':
			$img = 'puce-verte.gif';
			$alt = _T('projets:bulle_puce_publie');
			return http_img_pack($img, $alt, $atts);
		case 'prepa':
			$img = 'puce-blanche.gif';
			$alt = _T('projets:bulle_puce_preparation');
			return http_img_pack($img, $alt, $atts);
		case 'poubelle':
			$img = 'puce-poubelle.gif';
			$alt = _T('projets:bulle_puce_ferme');
			return http_img_pack($img, $alt, $atts);
	}
	return http_img_pack($img, $alt, $atts);
}

function img_puce_statut_auteur($statut, $atts='') {
	switch ($statut) {
		case 'publie':
			$img = 'puce-verte.gif';
			$alt = _T('projets:bulle_puce_publie');
			return http_img_pack($img, $alt, $atts);
		case 'prepa':
			$img = 'puce-blanche.gif';
			$alt = _T('projets:bulle_puce_preparation');
			return http_img_pack($img, $alt, $atts);
		case 'poubelle':
			$img = 'puce-poubelle.gif';
			$alt = _T('projets:bulle_puce_ferme');
			return http_img_pack($img, $alt, $atts);
	}
	return http_img_pack($img, $alt, $atts);
}

?>