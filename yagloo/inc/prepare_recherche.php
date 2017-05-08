<?php


@define('_DELAI_CACHE_RECHERCHES',600);

// Preparer les listes id_article IN (...) pour les parties WHERE
// et points =  des requetes du moteur de recherche
function inc_prepare_recherche($recherche, $table='articles', $cond=false, $serveur='') {
	static $cache = array();

	// Liste des services de recherche connus
	$services = array('boss', 'google');

	// Si on n'est pas configure, revenir a la recherche normale
	if (!$config = lire_config('yagloo')
	OR !in_array($config['service'], $services)) {
		include_once _DIR_RESTREINT.'inc/prepare_recherche.php';
		return inc_prepare_recherche_dist($recherche, $table, $cond, $serveur);
	}

	// si recherche n'est pas dans le contexte, on va prendre en globals
	// ca permet de faire des inclure simple.
	$recherche = trim($recherche);
	if (!strlen($recherche) AND isset($GLOBALS['recherche']))
		$recherche = trim($GLOBALS['recherche']);

	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// si on n'a pas encore traite les donnees
	if (!isset($cache[$recherche])) {

		spip_timer('recherche '.$config['service']);

		// Ici il faut indiquer toutes les tables potentiellement {recherche}
		$liste_index_tables = array(
			'articles' => 'id_article',
			'auteurs' => 'id_auteur',
			'breves' => 'id_breve',
			'mots' => 'id_mot',
			'rubriques' => 'id_rubrique'
		);

		// tester/nettoyer le cache de cette recherche
		$hashes = array();
		foreach ($liste_index_tables as $type => $_id) {
			$table_abreg = $type;
			$hash = substr(md5($recherche . $table_abreg),0,16);
			$cache[$recherche][$table_abreg] = array("resultats.points as points","recherche='$hash'");
			$hashes[] = $hash;
		}

		if (!$row = sql_fetsel('UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(maj) AS fraicheur','spip_resultats',
		sql_in('recherche', $hashes),'','fraicheur DESC','0,1','',$serveur)
		OR $row['fraicheur'] > _DELAI_CACHE_RECHERCHES) {

			$tab_couples = array();


			$domains = preg_replace(',^https?://,', '', $GLOBALS['meta']['adresse_site']);

			$yagloo = 'Yagloo_recherche_'.$config['service'];
			$key = $config[$config['service'].'key'];
			$results = $yagloo($recherche, $domains, $key);

			$urls = array();
			$preg = ',^http://'.preg_quote($domains).'(/.+?)(\.html)?$,S';
			foreach($results as $i => $url) {
				if (preg_match($preg, $url, $regs)
				AND !isset($urls[$regs[1]])) {
					$urls[$regs[1]] = 1.8*count($results)-$i;
				}
			}

			// rechercher le contexte correspondant
			spip_connect();
			$f = generer_url_entite();

			$save = $GLOBALS['contexte'];
			$points = array();
			foreach($urls as $url => $score) {
				$GLOBALS['contexte'] = array();
				if ($f) {
					$a = $f($url, $entite);
					if (is_array($a))
						$GLOBALS['contexte'] = $a[0];
				} else {
					recuperer_parametres_url($fond, $url);
				}
				foreach ($liste_index_tables as $type => $_id) {
					if (isset($GLOBALS['contexte'][$_id])) {
						$id = $GLOBALS['contexte'][$_id];
						$points[$type][$id] = $score;
					}
				}
			}
			$GLOBALS['contexte'] = $save;

			/* modification des points pour les articles selon leur age */
			if (is_array($points['articles'])) {
				$s = sql_select('id_article,date_redac', 'spip_articles',
					sql_in('id_article', array_keys($points['articles'])));
				while ($t = sql_fetch($s)) {
					$points['articles'][$t['id_article']] *= (strtotime($t['date_redac']) - strtotime('1990-01')) / 100000000;
				}
			}


			foreach ($points as $type => $scores) {
				$ttable = 'spip_'.$type;
				$table_abreg = $type;
				$hash = substr(md5($recherche . $table_abreg),0,16);

				if (!count($scores)) {
					$cache[$recherche][$ttable] = array("''", '0');
				} else {
					foreach ($scores as $id => $score)
						$tab_couples[] = array(
							'recherche' => $hash,
							'id' => $id,
							'points' => $score
						);
				}
			}

			// Aucune reponse : le noter
			if (!count($tab_couples))
				$tab_couples[] = array(
					'recherche' => $hashes[0],
					'id' => 0,
					'points' => 0
				);


			// supprimer les anciens resultats de cette recherche
			// et les resultats trop vieux avec une marge
			sql_delete('spip_resultats',
			'(maj<DATE_SUB(NOW(), INTERVAL '.(_DELAI_CACHE_RECHERCHES+100)." SECOND)) OR ". sql_in('recherche', $hashes),
			$serveur);

			// Inserer les reponses
			sql_insertq_multi('spip_resultats', $tab_couples, array(),$serveur);

			spip_log("recherche ".$config['service']." ($recherche) ".spip_timer("recherche ".$config['service']), 'indexation');
		}
	}

	if ($val = $cache[$recherche][$table])
		return $val;
	return array("0 as points", '0=1');
}

function Yagloo_boss_decode($r) {
	if ($r = json_decode($r)
	AND $r = $r->ysearchresponse
	AND $r->totalhits > 0
	AND $r->responsecode == 200) {
		foreach($r->resultset_web AS $item) {
			$results[] = $item->url;
		};
		return array($r->totalhits, $results);
	}
	return array(0,array());
}
function Yagloo_google_decode($r) {
	if ($r = json_decode($r)
	AND $r->responseStatus == 200
	AND $r = $r->responseData
	AND $r->cursor->estimatedResultCount > 0
	) {
		foreach($r->results AS $item) {
			$results[] = $item->url;
		};
		return array($r->cursor->estimatedResultCount, $results);
	}
	return array(0,array());
}

function Yagloo_recherche_api($urlapi, $page, $maxpages, $json_decode) {
	list($total, $results) = $json_decode(recuperer_page($urlapi));

	if ($total > count($results)) {
		$ping = min($maxpages, ceil(($total - count($results)) / $page));
		$urls = array();
		for ($i=1; $i<= $ping; $i++)
			$urls[] = $urlapi . '&start=' . ($i * $page);

		foreach(Yagloo_recuperer_pages($urls) as $r) {
			list(,$z) = $json_decode($r);
			$results = array_merge($results, $z);
		}
	}

	return $results;
}

function Yagloo_recherche_boss($recherche, $domains='', $key='') {
	$api = 'http://boss.yahooapis.com/ysearch/web/v1/';
	$page = 50;
	$urlapi = $api . urlencode($recherche) . '?appid=' . $key
		. '&format=json&sites=' . urlencode($domains) . '&count=' . $page;

	return Yagloo_recherche_api($urlapi, $page, 14, 'Yagloo_boss_decode');
}

function Yagloo_recherche_google($recherche, $domains='', $key='') {
	$api = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large';
	$urlapi = $api . '&q=site:' . urlencode($domains) . '+' . urlencode($recherche);
	if ($key)
		$urlapi .= '&key='.urlencode($key);
	if (strlen($GLOBALS['spip_lang']))
		$urlapi .= '&hl=' . $GLOBALS['spip_lang'];

	return Yagloo_recherche_api($urlapi, 8, 8, 'Yagloo_google_decode');
}

// Charge plusieurs URLs en parallele
function Yagloo_recuperer_pages($urls = array()) {
	$ch = $res = array();
	$mh = curl_multi_init();

	foreach($urls as $url) {
		$ch[$url] = curl_init();
		curl_setopt($ch[$url], CURLOPT_URL, $url);
		curl_setopt($ch[$url], CURLOPT_HEADER, 0);
		curl_setopt($ch[$url], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch[$url], CURLOPT_REFERER,
		$GLOBALS['meta']['adresse_site'].'/');
		curl_setopt($ch[$url], CURLOPT_USERAGENT,
		"SPIP-".$GLOBALS['spip_version_affichee']." (https://www.spip.net/)");
		curl_multi_add_handle($mh, $ch[$url]);
		spip_log('Recuperer '.$url);
	}
 
	$running=null;
	do {
		curl_multi_exec($mh,$running);
	} while ($running > 0);

	foreach($ch as $url => $c) {
		$res[$url] = curl_multi_getcontent($c);
		curl_multi_remove_handle($mh, $c);
	}
	curl_multi_close($mh);

	return $res;
}

