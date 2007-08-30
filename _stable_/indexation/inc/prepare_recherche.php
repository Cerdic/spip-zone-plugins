<?php

// Preparer les listes id_article IN (...) pour les parties WHERE
// et points =  des requetes du moteur de recherche
function inc_prepare_recherche($recherche, $primary = 'id_article', $id_table='articles',$nom_table='spip_articles', $cond=false) {
	static $cache = array();
	static $fcache = array();

	// si recherche n'est pas dans le contexte, on va prendre en globals
	// ca permet de faire des inclure simple.
	if (!isset($recherche) AND isset($GLOBALS['recherche']))
		$recherche = $GLOBALS['recherche'];

	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// Premier passage : chercher eventuel un cache des donnees sur le disque
	if (!isset($cache[$recherche])) {
		$dircache = sous_repertoire(_DIR_CACHE,'rech3');
		$fcache[$recherche] =
			$dircache . substr(md5($recherche),0,10).'.txt';
		if (false && lire_fichier($fcache[$recherche], $contenu))
			$cache[$recherche] = @unserialize($contenu);
	}

	// si on n'a pas encore traite les donnees dans une boucle precedente
	if (!is_array($cache[$recherche])) {
		$cache[$recherche] = array();

		spip_timer('fulltext');

		$points = array();

		$s = spip_query($q = "SELECT id,type, MATCH (texte) AGAINST ("._q($recherche).") + 10*MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE) AS points FROM spip_indexation WHERE MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE)");

		while ($t = sql_fetch($s)) {
			$points[$t['type']][$t['id']] = ceil(10*$t['points']);
		}

		# calculer le {id_article IN()} et le {... as points}
		include_spip('inc/indexation');
		$liste_index_tables = liste_index_tables();
		foreach ($points as $type => $scores) {
		if ($table = $liste_index_tables[$type]) {
			$primary = id_table_objet(preg_replace(',^spip_|s$,', '', $table)); // eurk

			if (!count($scores)) {
				$cache[$recherche][$type] = array("''", '0');
			} else {
				$listes_ids = array();
				$select = '0';
				foreach ($scores as $id => $score)
					$listes_ids[$score] .= ','.$id;
				foreach ($listes_ids as $p => $liste_ids)
					$select .= "+$p*(".
					calcul_mysql_in($primary, substr($liste_ids, 1))
					.") ";

				$cache[$recherche][$type] = array($select,
					'('.calcul_mysql_in($primary, array_keys($scores)).')'
					);
			}
		}}

		spip_log("recherche fulltext ($recherche) ".spip_timer("fulltext"));

		// ecrire le cache de la recherche sur le disque
		ecrire_fichier($fcache[$recherche], serialize($cache[$recherche]));
		// purger le petit cache
		nettoyer_petit_cache('rech3', 300);
	}

	$type = id_index_table($nom_table);
	return $cache[$recherche][$type];
}


?>
