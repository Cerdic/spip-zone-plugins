<?php


// Ajoute notre fonction dans un cron
function MiroirSyndic_ajouter_cron($taches) {
	$taches['miroir_syndic'] = 60;
	return $taches;
}

function cron_miroir_syndic($t) {
	if ($nombre = MiroirSyndic_miroir()) {
		include_spip('inc/rubriques');
		calculer_rubriques();
		propager_les_secteurs();
	}

	return $nombre;
}



// Syndication miroir : ce plugin permet de recopier les articles
// de la table spip_syndic_articles vers spip_articles ; on identifie
// un article par son url seulement :
//
//    `spip_articles`.url_site = `spip_syndic_articles`.url
//

// un nouvel article : le creer, et creer au besoin la rubrique qui va bien
function MiroirSyndic_creer_article($t) {
	lang_select(trim(preg_replace(',[-_].*,', '', $t['lang'])));
	$lang = $GLOBALS['spip_lang'];
	lang_dselect();

	$id_article = spip_abstract_insert('spip_articles',
		'(id_rubrique, id_secteur, statut, url_site, lang)',
		"(".$t['id_rubrique'].", ".$t['id_secteur'].",
		'publie', '".addslashes($t['url'])."', '$lang')"
	);

	// si la rubrique n'existe pas, la creer
	spip_query("INSERT IGNORE INTO spip_rubriques
	(id_rubrique, id_secteur, titre, lang) VALUES
	(".$t['id_syndic'].", ".$t['id_syndic'].",
	'".addslashes($t['nom_site'])."',
	'$lang')");

	return $id_article;
}

// Cette fonction regarde les spip_syndic_articles modifies recemment
// et les reporte dans spip_articles ; a appeler avec cron() ou autre...
function MiroirSyndic_miroir() {
	include_spip('inc/lang');
	include_spip('base/abstract_sql');

	$q = "
	SELECT s.*, a.id_article AS id_article, src.nom_site as nom_site,
		src.id_rubrique AS id_rubrique, src.id_secteur AS id_secteur
	FROM spip_syndic AS src,
		spip_syndic_articles AS s
		LEFT JOIN spip_articles AS a
		ON s.url = a.url_site
	WHERE s.id_syndic = src.id_syndic
		AND src.statut='publie'
		AND s.statut='publie'
		AND (a.id_article IS NULL OR s.maj > a.maj)
	ORDER BY maj DESC LIMIT 200";

	$s = spip_query($q);
	$nombre = spip_num_rows($s);
	spip_log('miroir de '.$nombre.' articles syndiques');

	while ($t = spip_fetch_array($s)) {

		// Si l'article n'existe pas, on le cree
		if (!$t['id_article']) {
			$t['id_article'] = MiroirSyndic_creer_article($t);
		}

		spip_query("UPDATE spip_articles SET
			titre = '".addslashes($t['titre'])."',
			date = '".$t['date']."',
			surtitre = '".addslashes($t['lesauteurs'])."',
			chapo = '".addslashes($t['descriptif'])."',
			soustitre = '".addslashes($t['tags'])."'
			WHERE id_article=".$t['id_article']);

	}

	return $nombre;
}

?>
