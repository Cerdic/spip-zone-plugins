<?php

function aeres_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='configurer_zotspip' && autoriser('webmestre'))
		$flux['data'] .= recuperer_fond('prive/inclure/configurer_aeres');
	return $flux;
}

function aeres_affiche_droite($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='ticketskiss' || $exec=='ticket_afficher')
		$flux['data'] .= recuperer_fond('prive/inclure/maj_zotspip').'<h3>Correspondances<br />Zotero / AERES</h3>'.recuperer_fond('inclure/correspondances_zotero_aeres');
	return $flux;
}

function aeres_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('aeres.css').'" type="text/css" />';
	return $flux;
}


?>
