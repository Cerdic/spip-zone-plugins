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
	$xml = recuperer_fond(
		'selecteurs/'._request('quoi'),
		calculer_contexte()
	);

	header('Content-Type: text/xml; charset='.$GLOBALS['meta']['charset']);
	echo $xml;
}


// critere {contenu_auteur_select} , cf. sedna
function critere_contenu_auteur_select($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];

	if (isset($crit->param[0][0])
	AND $crit->param[0][0]->texte == 'strict')
		$debut = '';
	else
		$debut = '%';

	// un peu trop rapide, ca... le compilateur exige mieux (??)
	$boucle->hash = '
	// RECHERCHE
	if ($r = _request("value")) {
		$r = _q("'.$debut.'$r%");
		$s = "(
			auteurs.nom LIKE $r
			OR auteurs.email LIKE $r'

	// on ne cherche pas dans la bio etc
	// si on peut trouver direct dans le nom ou l'email
	. (!$debut
		? ''
		: '
			OR auteurs.bio LIKE $r
			OR auteurs.nom_site LIKE $r
			OR auteurs.url_site LIKE $r
		')
	.'
		)";
	} else {
		$s = 1;
	}
	';
	$boucle->where[] = '$s';
}

// Un filtre pour afficher le bonhomme_statut
function icone_statut_auteur($statut) {
	include_spip('inc/presentation');
	return bonhomme_statut(array('statut'=>$statut));
}

?>
