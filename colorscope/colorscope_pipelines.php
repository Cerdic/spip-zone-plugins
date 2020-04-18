<?php
/**
 * Plugin Colorscope pour SPIP 2.0
 * Licence GPL (c) 2009 - 2011
 * Auteur Cyril MARION - Ateliers CYM
 * Merci a Arno* pour les filtres couleur :-)
 *
 */
include_spip('inc/filtres_images_mini');
include_spip('inc/filtres_images');

//pour conserver compatibilité avant SPIP 3.1
if (!function_exists('_couleur_hex_to_dec')) {
function	_couleur_hex_to_dec($couleur) {
	return couleur_hex_to_dec($couleur);
}
}

function colorscope_post_typo($texte) {
	$motif = '`(#[a-fA-F0-9]{6})`';
	$texte = preg_replace_callback($motif, 'bloc_colorscope', $texte);
	return $texte;
}

function bloc_colorscope($couleurs) {
	$coul = $couleurs[0];
	if ( luminance($coul) < 127 ) {
		$fond = "white";
	} else {
		$fond = "black";
	}
	// $fond = '#'.couleur_inverser(couleur_extreme($coul));

	$bloc = '<code class="spip_color" style="background-color:'.format_decimal($coul).';color:'.$fond.'">'.$coul.'</code>';
	return $bloc;
}

function luminance($couleur) {
	$couleurs = _couleur_hex_to_dec($couleur); // modifi SPIP 3.1
	$valeur = 0.2126*$couleurs["red"] + 0.7152*$couleurs["green"] + 0.0722*$couleurs["blue"];
	return $valeur;
}

function format_decimal($couleur) {
	$couleurs = _couleur_hex_to_dec($couleur); // modifi SPIP 3.1
	$rouge = $couleurs['red'];
	$vert = $couleurs['green'];	
	$bleu = $couleurs['blue'];

	$couleur_dec = "rgb(".$rouge.",".$vert.",".$bleu.")";
	return $couleur_dec;
}

?>