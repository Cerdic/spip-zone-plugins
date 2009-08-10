<?php
function ajouter_auteur_insert_head($flux){
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	return $flux;
}
?>
