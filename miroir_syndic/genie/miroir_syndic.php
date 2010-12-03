<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2010 Fil, Cedric
 * Distribue sous licence GPL
 *
 *
 * Syndication miroir : ce plugin permet de recopier les articles
 * de la table spip_syndic_articles vers spip_articles ; on identifie
 * un article par son url seulement :
 * `spip_articles`.url_site = `spip_syndic_articles`.url
 *
 *
 * Options et reglages :
 * define('_MODE_RUBRIQUE_MIROIR', '') pour ne pas ranger les articles dans des rubriques crees automatiquement
 * define('_MODE_RUBRIQUE_MIROIR', 'mois') : pour ranger par mois exclusivement
 * define('_MODE_RUBRIQUE_MIROIR', 'tag') : pour ranger par tag si possible, par mois sinon
 *
 * defined('_MIROIR_ID_SYNDIC','1,2,3') : pour lister explicitement les site a dupliquer
 * $GLOBALS['mode_rubrique_miroir_disallow'][2] : pour interdire le miroir dans le secteur 2 (autorise partout ailleurs)
 * $GLOBALS['mode_rubrique_miroir_allow'][3] : pour autoriser le miroir dans le secteur 3 (interdit partout ailleurs)
 *
 */


// Ajoute notre fonction dans un cron
function miroirsyndic_ajouter_cron($taches) {
	$taches['miroir_syndic'] = 60;
	return $taches;
}

function genie_miroir_syndic($t) {
	spip_log('miroir de syndication = '.$t, 'miroirsyndic');
	$nombre = miroirsyndic_miroir();
	spip_log('miroir de syndication : '.$nombre, 'miroirsyndic');
	return $nombre;
}


// un nouvel article : le creer
function miroirsyndic_creer_article($t) {
	lang_select(trim(preg_replace(',[-_].*,', '', $t['lang'])));
	$lang = $GLOBALS['spip_lang'];
	lang_select();

	spip_log('insert', 'miroirsyndic');

	include_spip('action/editer_article');
	include_spip('inc/autoriser');
	autoriser_exception('publierdans','rubrique',$t['id_rubrique']); // se donner temporairement le droit
	if ($id_article = insert_article($t['id_rubrique'])) {
		autoriser_exception('modifier','article',$id_article); // se donner temporairement le droit
		articles_set($id_article,array(
				'titre'=>$t['titre'],
				'nom_site'=>$t['titre'],
				'url_site'=>$t['url'],
				'statut'=>'prop',
				'date'=>$t['date'],
				'lang' => $lang,
		));
		autoriser_exception('modifier','article',$id_article,false); // revenir a la normale
	}
	autoriser_exception('publierdans','rubrique',$t['id_rubrique'], false);

	spip_log($id_article, 'miroirsyndic');
	return $id_article;
}

// indique la rubrique d'un (nouvel) article en fonction de ses tags
//
function miroirsyndic_regler_rubrique($t) {
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
		spip_log("rubrique '$nom_rub'", 'miroirsyndic');
		include_spip('inc/rubriques');
		$r = creer_rubrique_nommee($nom_rub, $t['id_rubrique']);
		include_spip('action/editer_article');
		include_spip('inc/autoriser');
		autoriser_exception('publierdans','rubrique',$r); // se donner temporairement le droit
		articles_set($id_article,array('id_parent'=>$r));
		autoriser_exception('publierdans','rubrique',$r,false);
	}

}

// Cette fonction regarde les spip_syndic_articles modifies recemment
// et les reporte dans spip_articles ; a appeler avec cron() ou autre...
function miroirsyndic_miroir($force_refresh = false) {
	include_spip('inc/lang');
	include_spip('inc/filtres');
	include_spip('base/abstract_sql');
	include_spip('action/editer_article');
	include_spip('inc/rubriques');
	include_spip('inc/autoriser');

	// S'il y a un tag de rubrique, deplacer l'article
	// dans une sous-rubrique nommee de la meme maniere
	// (si la rubrique est nommee Truc/Chose/Machin ca cree l'arbo)
	// ou alors organiser les choses par date
	// -- le mode par defaut est 'tag' (qui prend le mois s'il n'y a pas de tag)
	// -- define('_MODE_RUBRIQUE_MIROIR', '') pour ne pas ranger
	// -- define('_MODE_RUBRIQUE_MIROIR', 'mois') : par mois exclusivement
	define('_MODE_RUBRIQUE_MIROIR', 'tag');

	define('_MIROIR_CHAMP_LESAUTEURS','surtitre');
	define('_MIROIR_CHAMP_DESCRIPTIF','chapo');
	define('_MIROIR_CHAMP_TAGS','soustitre');

	$s = sql_select(
		"s.*, a.id_article AS id_article,a.id_rubrique AS rubrique_article, src.nom_site as nom_site,
		src.id_rubrique AS id_rubrique, src.id_secteur AS id_secteur",
		// FROM
		"spip_syndic_articles AS s
		LEFT JOIN spip_articles AS a
		ON s.url = a.url_site
		LEFT JOIN spip_syndic AS src
		ON s.id_syndic = src.id_syndic",
		// WHERE
		"src.statut='publie'
		AND s.statut='publie'"
		. ($force_refresh?'':' AND (a.id_article IS NULL OR s.maj > a.maj)')
		. (defined('_MIROIR_ID_SYNDIC')?" AND ".sql_in('src.id_syndic',explode(',',_MIROIR_ID_SYNDIC)):''),
		'',
		// ORDER BY
		's.maj DESC LIMIT 200'
		);

	$peupler_article = charger_fonction('peupler_article','miroir');
	spip_log('miroir:'.sql_count($s)." articles syndiques a mettre a jour (fonction $peupler_article)", 'miroirsyndic');
	
	while ($t = sql_fetch($s)) {
		$nombre ++;
		spip_log('miroir:'.var_export($t,1), 'miroirsyndic');
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
				$t['id_article'] = miroirsyndic_creer_article($t);
				miroirsyndic_regler_rubrique($t);
				$creation = true;
			}

			$id_rubrique = sql_getfetsel("id_rubrique", "spip_articles", "id_article=".$t['id_article']);
			autoriser_exception('publierdans','rubrique',$id_rubrique); // se donner temporairement le droit
			autoriser_exception('modifier','article',$t['id_article']); // se donner temporairement le droit
			$peupler_article($t['id_article'],$t);
			autoriser_exception('modifier','article',$t['id_article'],false); // revenir a la normale
			autoriser_exception('publierdans','rubrique',$id_rubrique,false);
		}

		spip_log($q, 'miroirsyndic');
	}

	if ($creation) {
		if (function_exists('calculer_rubriques'))
			calculer_rubriques();
		if (function_exists('calculer_langues_rubriques'))
			calculer_langues_rubriques();
		if (function_exists('propager_les_secteurs'))
			propager_les_secteurs();
	}

	spip_log('miroir de '.intval($nombre).' articles syndiques', 'miroirsyndic');
	return $nombre;
}


?>