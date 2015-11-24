<?php

function exec_indexer_bidules() {

	include_spip('inc/indexer');

	// recuperer les stats de ce que contient l'index sphinx, trie
	// par source, par objet, et par "recent ou non"
	$stats = indexer_statistiques_indexes_depuis();
	$source = lire_meta('adresse_site');

	echo "<p>", date('Y-m-d H:i:s'),"</p>\n";

	foreach(indexer_lister_blocs_indexation(10000000) as $alias => $ids) {
		foreach ($ids as $id) {

			if (is_array($stats[$source])) {
				$u = $stats[$source][$alias];
			} else $u = [];

			// todo : ajouter statut='publie' pour les objets pertinents
			$total = sql_fetsel('COUNT(*) AS c', 'spip_'.table_objet($alias));

			if (is_array($total) AND $count = intval($total['c'])) {
				echo "<p>Nombre total d'$alias dans la base: ".$count."; indexés: ".($u[0]+$u[1])." ; ".(0.1*floor($u[1]/($u[0]+$u[1])*1000))."% à jour.</p>";
			} else {
				echo "<p>Nombre d'$alias indexés dans sphinx: ".($u[0]+$u[1])." dont ".(0.1*floor($u[1]/($u[0]+$u[1])*1000))."% à jour.</p>";
			}
		}
	}



	echo "<a href='./?action=indexer_tout_reindexer_async'><button>Lancer la réindexation</button></a>\n";


	echo "<img width=0 height=0 src='../?action=cron' />";

}
