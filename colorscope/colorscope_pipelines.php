<?php
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 * Merci a Arno* pour les filtres couleur :-)
 *
 */
include_spip('inc/filtres_images_mini');
include_spip('inc/filtres_images');

function colorscope_post_typo($texte) {
	$motif = '`(#[a-fA-F0-9]{6})`';
	$texte = preg_replace_callback($motif, 'bloc_colorscope', $texte);
	return $texte;
}

function bloc_colorscope($couleur) {
	$fond = '#'.couleur_inverser(couleur_extreme($couleur[0]));
	$bloc = '<span style="display:inline-block;text-align:center;width:90px;height:20px;background-color:'.$couleur[0].';color:'.$fond.'">'.$couleur[0].'</span>';
	return $bloc;
}


?>