<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_TEXTES_PROPRIETAIRE($p) {
   return calculer_balise_dynamique($p,TEXTES_PROPRIETAIRE,array());
}

function balise_TEXTES_PROPRIETAIRE_dyn($chaine='', $args=array(), $fct='propre') {
	include_spip('inc/texte');
	$ok = textes_proprietaire(true);

	$div = spip_proprio_proprietaire_texte($chaine, $args);
	if(strlen($fct) AND function_exists($fct))
		$div = $fct( $div );

	echo $div;
}

?>