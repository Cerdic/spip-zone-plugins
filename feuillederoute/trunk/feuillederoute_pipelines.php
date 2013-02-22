<?php

// inclure le bouton d'admin pour afficher/masquer la feuille de route dans l'espace public
function feuillederoute_formulaire_admin($flux) {
	$btn = recuperer_fond('prive/bouton/feuillederoute');
	$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);
	return $flux;
}

function feuillederoute_body_prive($flux){
	$flux .= recuperer_fond('prive/bouton/feuillederoute');
	return $flux;
}

?>