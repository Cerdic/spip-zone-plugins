<?php

function mll_insert_head_css($flux){
	// Insertion de la feuille de styles du menu de langues
	$css_mll = generer_url_public('mll_styles.css');
	$flux .='<link rel="stylesheet" type="text/css" media="screen" href="'.$css_mll.'" />';

	return $flux;
}

?>