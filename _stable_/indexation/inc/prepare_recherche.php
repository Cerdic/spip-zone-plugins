<?php

function Indexation_sql_like($recherche) {
	// Si la chaine est inactive, on va utiliser LIKE pour aller plus vite
	if (preg_quote($recherche, '/') == $recherche) {
		$methode = 'LIKE';
		$q = _q(
			"%"
			. str_replace(array('%','_'), array('\%', '\_'), $recherche)
			. "%"
		);
	} else {
		$methode = 'REGEXP';
		$q = _q($recherche);
	}

	return "texte $methode $q";
}

function Indexation_recherche_sql($recherche) {

	// Methode FULLTEXT si disponible
	if (Indexation_test_fulltext()) {
		$points = array();
		if ($recherche)
			$s = spip_query($q = "SELECT id,type, MATCH (texte) AGAINST ("._q($recherche).") + 10*MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE) AS points FROM spip_indexation WHERE MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE)");
		else
			$s = spip_query($q = "SELECT id,type,0 AS points FROM spip_indexation WHERE 0=1");
		while ($t = sql_fetch($s))
			$points[$t['type']][$t['id']] = ceil(10*$t['points']);
	}

	// Methode alternative LIKE / REGEXP
	// On ne peut pas utiliser inc/rechercher car l'API
	// ne comprend ni spip_indexation (elle ajoute un s)
	// ni la cle primaire sur (id,type)
	else {
		$requete['SELECT'] = array('id', 'type');
		$requete['FROM'] = array('spip_indexation');
		$requete['WHERE'] = array(Indexation_sql_like($recherche));
		
		$s = sql_select (
			$requete['SELECT'], $requete['FROM'], $requete['WHERE']
		);

		while ($t = sql_fetch($s))
			$points[$t['type']][$t['id']] ++;
	}

	return $points;
}

if ($GLOBALS['spip_version'] >= 11172)
	include_spip('inc/prepare_recherche_11172');
else
	include_spip('inc/prepare_recherche_11171');

?>
