<?php

//
// Les crayons ont leur propre copie du pack de Dean Edwards
// pour compatibilite avec SPIP < [9717]
//

function pack_cQuery($chemin) {
	$flux = spip_file_get_contents($chemin);
	$flux = str_replace('jQuery', 'cQuery', $flux);
	$flux = str_replace('var $ = cQuery','// dont set dollar', $flux);
	$flux = str_replace('window.$ = cQuery','// dont set dollar', $flux);

	if (!strlen($flux)
	OR _request('debug_crayons'))
		return $flux;

	include_spip('inc/class.JavaScriptPacker');
	$packer = new JavaScriptPacker($flux, 0, true, false);

	// en cas d'echec (?) renvoyer l'original
	if (strlen($t = $packer->pack()))
		return $t;

	// erreur
	spip_log('erreur de pack_js');
	return $flux;
}

?>
