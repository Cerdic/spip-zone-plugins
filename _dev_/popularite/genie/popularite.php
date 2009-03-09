<?php

// Cron
function genie_popularite_dist($t) {

	spip_log('popularite: cron');

	$tables = array(
		array('id' => 'id_auteur', 'lien' => 'spip_auteurs_articles', 'table' => 'spip_auteurs'),
		array('id' => 'id_mot', 'lien' => 'spip_mots_articles', 'table' => 'spip_mots'),
		array('id' => 'id_rubrique', 'lien' => 'spip_rubriques', 'table' => 'spip_rubriques')
	);


	foreach($tables as $desc) {
		$f = "select
			sum(articles.popularite) as popularite,
			lien.$desc[id] AS id
			from
				spip_articles as articles
			right join
				$desc[lien] as lien
					ON articles.id_article = lien.id_article
			group by id";
		if ($desc['table'] == 'spip_rubriques')
		$f =  "select
			sum(articles.popularite) as popularite,
			articles.id_rubrique AS id
			from
				spip_articles as articles
			group by id";

		if ($s = spip_query($f)
		) {
			while($t = sql_fetch($s)) {
				sql_update($desc['table'],
					array('popularite'=>$t['popularite']),
					$desc['id'].'='.$t['id']
				);
			}
		}
	}

	return true;
}

