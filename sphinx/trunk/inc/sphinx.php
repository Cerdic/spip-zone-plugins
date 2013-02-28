<?php

function recherchesphinx_array_dist($recherche, $options) {
	$options = array_merge(
		array('table' => 'article',
		),
		(array)$options
	);
	$table = $options['table'];
	$serveur = $options['serveur'];

	# router les recherches article vers la base sphinx
	if ($table == 'article') {
		# array ( id_article => score, id_article => score ... );
		$u = serialize(array('recherche' => $recherche));
		$a = array();
		foreach(inc_sphinx_to_array_dist($u, $selection='ecrire') as $r) {
			if ($id_article = $r['attrs']['id_objet'])
				$a[$id_article] = $r['weight'];
		}
		return $a;
	}

	# recherche d'un autre type
	include_spip('inc/recherche_to_array');
	return inc_recherche_to_array_dist($recherche, $options);
}


/**
 * sphinx -> tableau
 * @param string $u "serialize(env)"
 * @param string $selection : indiquer quelle sélection on demande
 * @return array|bool
 */
function inc_sphinx_to_array_dist($u, $selection=null) {
	static $mem;

	if (!$env = @unserialize($u))
		return false;

	spip_timer('total');


	// les trucs demandés
	$recherche = trim($env['recherche']);  # recherche fulltext

	// appel par une page mot :
	//   (ou limiter aux mots-clés)
	// ou par l'url &mots=italie ; &mots[]=italie&mots[]=economie
	// sprintf('%u') necessaire car le crc32 en 32 bits n'est pas le même qu'en 64
	if (is_string($env['mots'])) {
		$env['mots'] = array($env['mots']);
	}
	if (is_array($env['mots'])) {
		foreach ($env['mots'] as $mot) {
			if (strlen($mot))
			$cmot[] = sprintf('%u',crc32(trim(mb_strtolower($mot, 'UTF-8'))));
		}
	}
	else if (isset($env['id_mot'])
	AND $mot = sql_fetsel('titre', 'spip_mots', 'id_mot='.intval($env['id_mot']))) {
		$mot = $mot['titre'];
		$cmot = array(sprintf('%u',crc32(trim(mb_strtolower($mot, 'UTF-8')))));
	}

	# demande d'un id_auteur
	if (isset($env['id_auteur'])) {
		$id_auteur = intval($env['id_auteur']);
	}

	# validité de la requête ?
	if (!strlen($recherche)
	AND !isset($cmot)
	AND !$id_auteur
	)
		return false;

	# charger sphinx
	include_spip('lib/sphinxapi');
	$cl = new SphinxClient ();

	if (defined('_SPHINX_SERVER'))
		$cl->SetServer(_SPHINX_SERVER, defined('_SPHINX_PORT') ? _SPHINX_PORT : 9312);

	# visible = publie ou prop dans certains cas (voir sphinx.conf)
	$cl->SetFilter ( "visible", array(1) );

	# limites
	$max_pagination = 10;

	$debut = intval($env['debut_'.$selection]);
	$cl->SetLimits($debut, $max_pagination);


	# mots-clés sans fulltext
	if (isset($cmot)) {

		# on veut un ET logique
		foreach ($cmot as $m) {
			$cl->SetFilter ( "cmot", array($m), $exclude=false );
		}
		# ca, ca fait un OU logique
		# $cl->SetFilter ( "cmot", $cmot, $exclude=false );

	}

	# id_auteur
	if ($id_auteur)
		$cl->SetFilter ( "id_auteurs", array($id_auteur), $exclude=false );

	# lang
	if ($env['lang']) {
		$crc = sprintf('%u',CRC32($env['lang']));
		$cl->SetFilter ( "lang", array($crc) , $exclude=false );
	}

	# matching mode
	if (strlen($recherche)) {
		if (preg_match(',[&|],', $recherche)) {
			$cl->SetMatchMode ( SPH_MATCH_BOOLEAN );
		} else {
			$cl->SetMatchMode ( SPH_MATCH_EXTENDED2 );
		}
	} else {
		$cl->SetMatchMode ( SPH_MATCH_FULLSCAN );
	}

	# que veut-on recuperer comme elements
	$cl->SetSelect ( "*" );

	# filtrage
	#$cl->SetFilter ( "autotags", $tags );

	# debut et fin sous la forme d'une date 1999-01
	if (isset($env['debut'])
	OR isset($env['fin'])
	) {
		$min = strtotime($env['debut']);
		$max = strtotime($env['fin']);
		if (!$max) $max = strtotime('2999-01');
		$cl->SetFilterRange ( "dateu", $min, $max, $exclude=false );
	}

	# booster le titre et l'auteur
	$cl->SetFieldWeights(array(
		'auteurs' => 10,
		'titre' => 7,
		'surtitre' => 4,
		'petit' => 3,
	));

	switch ($env['tri']) {
		case 'relevance':
			$cl->SetSortMode ( SPH_SORT_RELEVANCE );
			break;
		# tri par "time segments" :
		#    1 heure, 1 jour, 1 semaine, 1 mois, 3 mois, et le reste
		#    dans chaque segement, tri par pertinence
		case 'tseg':
			$cl->SetSortMode ( SPH_SORT_TIME_SEGMENTS, 'date' );
			break;
		case 'date':
			$cl->SetSortMode ( SPH_SORT_ATTR_DESC, 'dateu' );
			break;
#		case 'expr2':
#			$cl->SetSelect("*, sum(lcs*user_weight) as w");
#			$cl->SetSortMode ( SPH_SORT_EXPR, "sum(lcs*user_weight)" );
#			break;
		case 'points':  /* c'est le choix proposé aux visiteurs ! */
		case 'expr1':
		default:
			$cl->SetSortMode ( SPH_SORT_EXPR,
				"@weight / (100+SQRT(SQRT(".(time()+3600*24*365)."-dateu)))"
			);
			break;
	}

	// si aucune source n'est definie, on meurt
	if (!defined('_SPHINX_SOURCE')) {
		die ('Please define _SPHINX_SOURCE');
	} else {
		$sources = _SPHINX_SOURCE;
	}

	# agir enfonction de la selection demandee :
	if ($selection == 'ecrire') {
		# espace prive
		if (defined('_SPHINX_ECRIRE_SOURCE')) {
			$cl->SetFilter ( "source", array(_SPHINX_ECRIRE_SOURCE) );
		}

		$cl->SetLimits(0,500);
	}
	else if (function_exists($f = 'sphinx_selection_'.$selection)) {
		// passage par parametre de $cl et $sources
		// pour modification eventuelle par une fonction maison
		$f($cl,$sources,$query,$env);
	}
	else if (function_exists($f = 'sphinx_selection_default')) {
		// passage par parametre de $cl et $sources
		// pour modification eventuelle par une fonction maison
		$f($cl,$sources,$query,$env);
	}

	# recuperer les données
	$cl->SetArrayResult ( true );

	# analyser la query
	$query = $recherche;

	# lancer la query dans sphinx
	$res = $cl->Query ( $query, $sources );

	# loger une eventuelle erreur
	if (strlen($cl->_error))
		spip_log($cl->_error, 'sphinx');

	# si ca ne donne rien au premier tour, on essaiera en relax
	# sauf si la query est deja booleenne/complexe (l'utilisateur sait ce qu'il fait)
	if (!isset($res['matches'])
	AND !$selection
	AND !preg_match('/[&|"~@]/', $query)
	) {
		$GLOBALS['_SPHINX_RELAX'] = count(explode(" ",$query));
		if (is_array($res['words'])) {
			foreach($res['words'] as $mot => $m) {
				$mot = mb_strtolower($mot, 'UTF-8');
				if (!$m['docs'])
					$GLOBALS['_SPHINX_SUBQUERY'][] = '<del>'.$mot.'</del>';
				else
					$GLOBALS['_SPHINX_SUBQUERY'][] = $mot;
			}
		}
	}
	if ($GLOBALS['_SPHINX_RELAX']>1) {
		# utiliser la syntaxe "mot1 mot2 mot3"/2
		$query2 = '"'.$query.'"/'.($GLOBALS['_SPHINX_RELAX']-1);
		$cl->SetLimits(0, 20);
		$res = $cl->Query ( $query2, $sources );
		if (!isset($res['matches']))
			$GLOBALS['_SPHINX_RELAX'] = 1;
	}
	if ($GLOBALS['_SPHINX_RELAX'] == 1) {
		$cl->SetMatchMode( SPH_MATCH_ANY );
		$cl->SetLimits(0, 20);
		$res = $cl->Query ( $query, $sources );
	}

	if (isset($res['matches'])) {
		$r = &$res['matches'];

		# environ 3ms pour filtrer
		if ($selection != ''
		AND function_exists($f = 'sphinx_filtrer_resultats')) {
			$n = count($r);
			$f($r);
			$n -= count($r);
		} else {
			$n = 0;
		}

		# creer les extraits avec surlignement des mots demandés
		if ($selection != '') {
			sphinx_excerpts($r, $cl, $res['words'], $query, $sources);
		}

		if ($GLOBALS['_SPHINX_RELAX']) {
			foreach($r as &$match) {
				$match['relax'] = true;
				$match['mots'] = join(', ', (array) $GLOBALS['_SPHINX_SUBQUERY']);
			}
		}

		# remplir avant debut, avec du vide
		for ($i=0; $i< $debut; $i++) {
			array_unshift($r, 0);
		}

		# remplir apres fin, avec du vide
		$grand_total = min(1000, intval($res['total_found']));
		for ($i=count($r); $i < $grand_total; $i++) {
			array_push($r, 0);
		}

		#echo '<li>total='.spip_timer('total').'</li>';


		return $r;
	}

	#var_dump($res, $cl->_error);

	return false;
}


/*
 * ajouter les extraits (c'est couteux car il faut indexer 'full')
 * @param $sources : au moins une des sources, n'importe laquelle
 */
function sphinx_excerpts(&$r, &$cl, $words=null, $query=null, $sources=null) {

	// ne pas surligner si la query demande un champ (ex: @auteurs toto)
	if (preg_match('/@/', $query))
		return;

	// ne pas surligner les mots de deux lettres sauf s'ils sont seuls ("UE")
	if ($wds = $words) {
		foreach ($wds as $wd=>$att) {
			if (strlen($wd)<=2        # mot trop court
			OR $att['hits'] > 100000  # stopword
			)
				unset($wds[$wd]);
		}
		if ($wds) $words = $wds;
		$words = join(' ',array_keys($words));
	}
	if (!$words) return;

	$textes = array();
	foreach ($r as $k=>&$w) {
		if (isset($w['attrs']['full'])) {
			$index[] = $k;
			$textes[] = $w['attrs']['full'];
		}
	}
	if (!$textes) return;

	$limit = defined('_SPHINX_COUPER_INTRO')
		? _SPHINX_COUPER_INTRO : 400;

	$options = array(
		'before_match'      => '<span class="spip_surligne">',
		'after_match'       => '</span>',
		'chunk_separator'   => ' (...) ',
		'limit'             => $limit,
		'around'            => 20,
		'html_strip_mode'   => 'strip',
	);

	# BuildExcerpts demande une source unique
	$source = array_pop(explode(',', $sources));

	# construire les extraits
	if ( $x = $cl->BuildExcerpts($textes, $source, $words, $options) ) {
		foreach ($index as $n=>$i) {
			if (strpos($x[$n], $options['before_match']) !== false) {
				$r[$i]['attrs']['intro'] = '<intro>'
					.typo(preg_replace('/[\[\]\{\}]|->.*\]/', ' ', $x[$n]))
					.'</intro>';
			}
		}
	}
}


?>
