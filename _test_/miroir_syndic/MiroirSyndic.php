<?php

// Syndication miroir : ce plugin permet de recopier les articles
// de la table spip_syndic_articles vers spip_articles ; on identifie
// un article par son url seulement :
//
//    `spip_articles`.url_site = `spip_syndic_articles`.url
//
// (c) Fil 2006-2007 - Licence GNU/GPL


# TODO:
# ALTER TABLE spip_articles ADD INDEX url_site (url_site);
# ALTER TABLE spip_syndic_articles ADD INDEX url (url);

// Ajoute notre fonction dans un cron
function MiroirSyndic_ajouter_cron($taches) {
	$taches['miroir_syndic'] = 60;
	return $taches;
}

function genie_miroir_syndic($t) {
#spip_log('miroir de syndication = '.$t, 'syndic');
	include_spip('inc/vieilles_defs');
	$nombre = MiroirSyndic_miroir();
#spip_log('miroir de syndication : '.$nombre, 'syndic');
	return $nombre;
}


// un nouvel article : le creer
function MiroirSyndic_creer_article($t) {
	lang_select(trim(preg_replace(',[-_].*,', '', $t['lang'])));
	$lang = $GLOBALS['spip_lang'];
	lang_dselect();

#spip_log('insert', 'syndic');

	$id_article = sql_insert('spip_articles',
		'(id_rubrique, id_secteur, statut, url_site, lang)',
		"(".$t['id_rubrique'].", ".$t['id_secteur'].",
		'publie', '".addslashes($t['url'])."', '$lang')"
	);
#spip_log($id_article, 'syndic');


	return $id_article;
}

// indique la rubrique d'un (nouvel) article en fonction de ses tags
//
function MiroirSyndic_regler_rubrique($t) {
	$nom_rub = '';

	if (_MODE_RUBRIQUE_MIROIR != '') {
		$annee = substr(trim($t['date']), 0, strlen('2006'));
		$mois = substr(trim($t['date']), 0, strlen('2006-03'));
		$nom_rub = "$annee/$mois";
	}
	if (_MODE_RUBRIQUE_MIROIR == 'tag'
	AND $tag = afficher_tags($t['tags'], 'directory')) {
		$nom_rub = supprimer_tags($tag);
	}

	if ($nom_rub) {
		#spip_log("rubrique '$nom_rub'", 'syndic');
		$r = creer_rubrique_nommee($nom_rub, $t['id_rubrique']);
		spip_query("UPDATE spip_articles SET
		id_rubrique=$r WHERE id_article=".$t['id_article']);
	}
}

// Cette fonction regarde les spip_syndic_articles modifies recemment
// et les reporte dans spip_articles ; a appeler avec cron() ou autre...
function MiroirSyndic_miroir() {
	include_spip('inc/lang');
	include_spip('inc/filtres');
	include_spip('base/abstract_sql');

	// S'il y a un tag de rubrique, deplacer l'article
	// dans une sous-rubrique nommee de la meme maniere
	// (si la rubrique est nommee Truc/Chose/Machin ca cree l'arbo)
	// ou alors organiser les choses par date
	// -- le mode par defaut est 'tag' (qui prend le mois s'il n'y a pas de tag)
	// -- define('_MODE_RUBRIQUE_MIROIR', '') pour ne pas ranger
	// -- define('_MODE_RUBRIQUE_MIROIR', 'mois') : par mois exclusivement
	define('_MODE_RUBRIQUE_MIROIR', 'tag');

	$q = "
	SELECT s.*, a.id_article AS id_article, src.nom_site as nom_site,
		src.id_rubrique AS id_rubrique, src.id_secteur AS id_secteur
	FROM spip_syndic_articles AS s
		LEFT JOIN spip_articles AS a
		ON s.url = a.url_site
		LEFT JOIN spip_syndic AS src
		ON s.id_syndic = src.id_syndic
	WHERE
		src.statut='publie'
		AND s.statut='publie'
		AND (a.id_article IS NULL OR s.maj > a.maj)
	ORDER BY maj DESC LIMIT 200";

#spip_log($q, 'syndic');

	$s = spip_query($q);
#spip_log($s, 'syndic');

	while ($t = sql_fetch($s)) {
		$nombre ++;
#spip_log($t, 'syndic');
		if (
			!isset($GLOBALS['mode_rubrique_miroir_disallow'][$t['id_secteur']])
			AND (
			  !isset($GLOBALS['mode_rubrique_miroir_allow']) 
			  OR isset($GLOBALS['mode_rubrique_miroir_allow'][$t['id_secteur']])
			)
		){

			// Si l'article n'existe pas, on le cree ; a priori sa rubrique
			// est la meme que la rubrique du site syndique (idem pour le secteur)
			if (!$t['id_article']) {
				$t['id_article'] = MiroirSyndic_creer_article($t);
	
				MiroirSyndic_regler_rubrique($t);
			}
	
			spip_query("UPDATE spip_articles SET
				titre = '".addslashes($t['titre'])."',
				date = '".$t['date']."',
				surtitre = '".addslashes($t['lesauteurs'])."',
				chapo = '".addslashes($t['descriptif'])."',
				soustitre = '".addslashes($t['tags'])."',
				maj=NOW()
				WHERE id_article=".$t['id_article']);
		}

#spip_log($q, 'syndic');
	}

	spip_log('miroir de '.intval($nombre).' articles syndiques', 'syndic');
	return $nombre;
}




// creer_rubrique_nommee('/truc/machin/chose') a partir de id_rubrique
// et avec la langue $lang
include_spip('inc/rubriques');
if(!function_exists('creer_rubrique_nommee')) {
function creer_rubrique_nommee($titre, $id_parent=0) {

	// eclater l'arborescence demandee
	$arbo = explode('/', preg_replace(',^/,', '', $titre));

	foreach ($arbo as $titre) {
		$s = spip_query("SELECT id_rubrique, id_secteur FROM spip_rubriques
		WHERE titre = "._q($titre)."
		AND id_parent=".intval($id_parent));
		if (!$t = spip_fetch_array($s)) {
			include_spip('base/abstract_sql');
			$id_rubrique = spip_abstract_insert('spip_rubriques',
				'(titre, id_parent, statut)',
				'('._q($titre).", $id_parent, 'prive')"
			);
			if ($id_parent > 0) {
				$data = spip_fetch_array(spip_query(
					"SELECT id_secteur,lang FROM spip_rubriques
					WHERE id_rubrique=$id_parent"));
				$id_secteur = $data['id_secteur'];
				$lang = $data['lang'];
			} else {
				$id_secteur = $id_rubrique;
				$lang = $GLOBALS['meta']['langue_site'];
			}

			spip_query("UPDATE spip_rubriques SET id_secteur=$id_secteur, lang="._q($lang)."
			WHERE id_rubrique=$id_rubrique");
		} else {
			$id_rubrique = $t['id_rubrique'];
		}

		// pour la recursion
		$id_parent = $id_rubrique;
	}

	return intval($id_rubrique);
}
}

?>
