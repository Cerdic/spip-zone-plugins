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

// Preparer les listes id_article IN (...) pour les parties WHERE
// et points =  des requetes du moteur de recherche
function inc_prepare_recherche($recherche, $table='articles', $cond=false) {
	static $cache = array();
	static $fcache = array();

	// si recherche n'est pas dans le contexte, on va prendre en globals
	// ca permet de faire des inclure simple.
	if (!isset($recherche) AND isset($GLOBALS['recherche']))
		$recherche = $GLOBALS['recherche'];

	// traiter le cas {recherche?}
	if (!strlen($recherche))
		return $cond
			? array("''" /* as points */, /* where */ '1')
			: array("''" /* as points */, /* where */ '0');

	// Premier passage : chercher eventuel un cache des donnees sur le disque
	if (!isset($cache[$recherche])) {
		$dircache = sous_repertoire(_DIR_CACHE,'rech3');
		$fcache[$recherche] =
			$dircache . substr(md5($recherche),0,10).'.txt';
		if (lire_fichier($fcache[$recherche], $contenu))
			$cache[$recherche] = @unserialize($contenu);
	}

	include_spip('inc/indexation');

	// si on n'a pas encore traite les donnees dans une boucle precedente
	if (!is_array($cache[$recherche])) {
		$cache[$recherche] = array();

		spip_timer('fulltext');

		$points = Indexation_recherche_sql($recherche);

		# calculer le {id_article IN()} et le {... as points}
		$liste_index_tables = liste_index_tables();
		foreach ($points as $type => $scores) {
		if ($ttable = $liste_index_tables[$type]) {
			$primary = id_table_objet($ttable);
			if (!count($scores)) {
				$cache[$recherche][$ttable] = array("''", '0');
			} else {
				$listes_ids = array();
				$select = '0';
				$table_abreg = preg_replace("{^spip_}","",$ttable);
				foreach ($scores as $id => $score)
					$listes_ids[$score] .= ','.$id;
				foreach ($listes_ids as $p => $liste_ids)
					$select .= "+$p*(".
					calcul_mysql_in("$table_abreg.$primary", substr($liste_ids, 1))
					.") ";

				$cache[$recherche][$ttable] = array($select,
					'('.calcul_mysql_in("$table_abreg.$primary", array_keys($scores)).')'
					);
			}
		}}

		spip_log("recherche fulltext ($recherche) ".spip_timer("fulltext"));

		// ecrire le cache de la recherche sur le disque
		ecrire_fichier($fcache[$recherche], serialize($cache[$recherche]));
		// purger le petit cache
		nettoyer_petit_cache('rech3', 300);
	}
	return $cache[$recherche]['spip_'.$table];
}


?>
