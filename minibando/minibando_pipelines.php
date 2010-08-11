<?php

function minibando_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_MINIBANDO.'minibando.css" type="text/css" media="projection, screen, tv" />';
	return $flux;
}

// surcharger les boutons d'administration
function minibando_formulaire_admin($flux) {

	include_spip('minibando_fonctions');
	$contexte = definir_barre_contexte();
	$boutons = definir_barre_boutons($contexte, false);
	$minibando = minibando($boutons,$contexte);

	$flux['data'] = preg_replace('%(<!--minibando-->)%is', $minibando.'$1', $flux['data']);

	return $flux;
}

?>