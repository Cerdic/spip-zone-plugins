<?php


function exec_selecteur_generique_dist() {

#	$field = _request('field');
#	$value = _request('value');
#	$quoi = _request('quoi');

	// tester la securite
	include_spip('inc/autoriser');
	# TODO....
	# pour les rubriques penser aussi au fait qu'on n'a pas forcement le droit
	# de deplacer vers n'importe quelle rubrique destination !

	include_spip('public/assembler');
	include_spip('public/parametrer');
	$text = recuperer_fond(
		'selecteurs/'._request('quoi'),
		calculer_contexte()
	);

	header('Content-Type: text/plain; charset='.$GLOBALS['meta']['charset']);
	echo $text;
}

?>
