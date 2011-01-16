<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;


// Donne la liste des champs/tables ou l'on sait chercher/remplacer
// avec un poids pour le score
// http://doc.spip.org/@liste_des_champs
function liste_des_champs() {
	return
	pipeline('rechercher_liste_des_champs',
		array(
			'article' => array(
				'surtitre' => 5, 'titre' => 8, 'soustitre' => 5, 'chapo' => 3,
				'texte' => 1, 'ps' => 1, 'nom_site' => 1, 'url_site' => 1,
				'descriptif' => 4
			),
			'breve' => array(
				'titre' => 8, 'texte' => 2, 'lien_titre' => 1, 'lien_url' => 1
			),
			'rubrique' => array(
				'titre' => 8, 'descriptif' => 5, 'texte' => 1
			),
			'site' => array(
				'nom_site' => 5, 'url_site' => 1, 'descriptif' => 3
			),
			'mot' => array(
				'titre' => 8, 'texte' => 1, 'descriptif' => 5
			),
			'auteur' => array(
				'nom' => 5, 'bio' => 1, 'email' => 1, 'nom_site' => 1, 'url_site' => 1, 'login' => 1
			),
			'forum' => array(
				'titre' => 3, 'texte' => 1, 'auteur' => 2, 'email_auteur' => 2, 'nom_site' => 1, 'url_site' => 1
			),
			'document' => array(
				'titre' => 3, 'descriptif' => 1, 'contenu' => 1, 'fichier' => 1
			),
			'syndic_article' => array(
				'titre' => 5, 'descriptif' => 1
			),
			'signature' => array(
				'nom_email' => 2, 'ad_email' => 4,
				'nom_site' => 2, 'url_site' => 4,
				'message' => 1
			)
		)
	);
}


// Recherche des auteurs et mots-cles associes
// en ne regardant que le titre ou le nom
// http://doc.spip.org/@liste_des_jointures
function liste_des_jointures() {
	return
	pipeline('rechercher_liste_des_jointures',
			array(
			'article' => array(
				'auteur' => array('nom' => 10),
				'mot' => array('titre' => 3),
				'document' => array('titre' => 2, 'descriptif' => 1, 'contenu' => 1)
			),
			'breve' => array(
				'mot' => array('titre' => 3),
				'document' => array('titre' => 2, 'descriptif' => 1, 'contenu' => 1)
			),
			'rubrique' => array(
				'mot' => array('titre' => 3),
				'document' => array('titre' => 2, 'descriptif' => 1, 'contenu' => 1)
			),
			'document' => array(
				'mot' => array('titre' => 3)
			)
		)
	);
}


function fulltext_keys($table, $prefix=null, $serveur=null) {
	if ($s = sql_query("SHOW CREATE TABLE ".table_objet_sql($table), $serveur)
	AND $t = sql_fetch($s)
	AND $create = array_pop($t)
	AND preg_match_all('/,\s*FULLTEXT\sKEY.*`(.*)`\s+[(](.*)[)]/i', $create, $keys, PREG_SET_ORDER)) {
		$cles = array();
		foreach ($keys as $key) {
			$cle = $key[2];
			if ($prefix)
				$cle = preg_replace(',`.*`,U', $prefix.'.$0', $cle);
			$cles[$key[1]] = $cle;
		}
		spip_log("fulltext $table: ".join(', ',array_keys($cles)),'recherche');
		return $cles;
	}
}


function expression_recherche($recherche, $options) {
	$u = $GLOBALS['meta']['pcre_u'];
	include_spip('inc/charsets');
	$recherche = trim(translitteration($recherche));

	// s'il y a plusieurs mots il faut les chercher tous : oblige REGEXP
	$recherche = preg_replace(',\s+,'.$u, '|', $recherche);

	$preg = '/'.str_replace('/', '\\/', $recherche).'/' . $options['preg_flags'];
	// Si la chaine est inactive, on va utiliser LIKE pour aller plus vite
	// ou si l'expression reguliere est invalide
	if (preg_quote($recherche, '/') == $recherche
	OR (@preg_match($preg,'')===FALSE) ) {
		$methode = 'LIKE';
		$u = $GLOBALS['meta']['pcre_u'];
		// eviter les parentheses qui interferent avec pcre par la suite (dans le preg_match_all) s'il y a des reponses
		$recherche = str_replace(
			array('(',')','?','[', ']'),
			array('\(','\)','[?]', '\[', '\]'),
			$recherche);
		$recherche_mod = $recherche;
		
		// echapper les % et _
		$q = str_replace(array('%','_'), array('\%', '\_'), trim($recherche));
		// les expressions entre " " sont un mot a chercher tel quel
		// -> on remplace les espaces par un _ et on enleve les guillemets
		if (preg_match(',["][^"]+["],Uims',$q,$matches)){
			foreach($matches as $match){
				// corriger le like dans le $q
				$word = preg_replace(",\s+,Uims","_",$match);
				$word = trim($word,'"');
				$q = str_replace($match,$word,$q);
				// corriger la regexp
				$word = preg_replace(",\s+,Uims","[\s]",$match);
				$word = trim($word,'"');
				$recherche_mod = str_replace($match,$word,$recherche_mod);
			}
		}
		$q = sql_quote(
			"%"
			. preg_replace(",\s+,".$u, "%", $q)
			. "%"
		);

		$preg = '/'.preg_replace(",\s+,".$u, ".+", trim($recherche_mod)).'/' . $options['preg_flags'];

	} else {
		$methode = 'REGEXP';
		$q = sql_quote($recherche);
	}

	return array($methode, $q, $preg);
}

// methodes sql
function inc_recherche_to_array_dist($recherche, $table, $options) {

	$requete = array(
	"SELECT"=>array(),
	"FROM"=>array(),
	"WHERE"=>array(),
	"GROUPBY"=>array(),
	"ORDERBY"=>array(),
	"LIMIT"=>"",
	"HAVING"=>array()
	);

	$serveur = $options['serveur'];

	list($methode, $q, $preg) = expression_recherche($recherche, $options);

	$l = liste_des_champs();
	$champs = $l[$table];

	$jointures = $options['jointures']
		? liste_des_jointures()
		: array();

	$_id_table = id_table_objet($table);
	$requete['SELECT'][] = "t.".$_id_table;
	$a = array();
	// Recherche fulltext
	foreach ($champs as $champ => $poids) {
		if (is_array($champ)){
		  spip_log("requetes imbriquees interdites");
		} else {
			if (strpos($champ,".")===FALSE)
				$champ = "t.$champ";
			$requete['SELECT'][] = $champ;
			$a[] = $champ.' '.$methode.' '.$q;
		}
	}
	if ($a) $requete['WHERE'][] = join(" OR ", $a);
	$requete['FROM'][] = table_objet_sql($table).' AS t';

	// FULLTEXT
	$fulltext = false; # cette table est-elle fulltext?
	if ($keys = fulltext_keys($table, 't', $serveur)) {
		$fulltext = true;

		$r = trim(preg_replace(',\s+,', ' ', $recherche));

		// si espace, ajouter la meme chaine avec des guillemets pour ameliorer la pertinence
		$pe = (strpos($r, ' ') AND strpos($r,'"')===false)
			? sql_quote(trim("\"$r\""), $serveur) : '';

		// On utilise la translitteration pour contourner le pb des bases
		// declarees en iso-latin mais remplies d'utf8
		if (($r2 = translitteration($r)) != $r)
			$r .= ' '.$r2;

		$p = sql_quote(trim("$r"), $serveur);

		// On va additionner toutes les cles FULLTEXT
		// de la table
		$score = array();
		foreach ($keys as $name => $key) {
			$val = "MATCH($key) AGAINST ($p)";
			// Une chaine exacte rapporte plein de points
			if ($pe)
				$val .= "+ 2 * MATCH($key) AGAINST ($pe)";

			// Appliquer les ponderations donnees
			// quels sont les champs presents ?
			// par defaut le poids d'une cle est fonction decroissante
			// de son nombre d'elements
			// ainsi un FULLTEXT sur `titre` vaudra plus que `titre`,`chapo`
			$compteur = preg_match_all(',`.*`,U', $key, $ignore);
			$mult = intval(sqrt(1000/$compteur))/10;

			// (Compat ascendante) si un FULLTEXT porte sur un seul champ,
			// ET est nomme de la meme facon : `titre` (`titre`)
			// sa ponderation est eventuellement donnee par la table $liste
			if ($key == "t.`${name}`"
			AND $ponderation = $liste[$table][$name])
				$mult = $ponderation;

			// Appliquer le coefficient multiplicatif
			if ($mult != 1)
				$val = "($val) * $mult";

			// si symboles booleens les prendre en compte
			if ($boolean = preg_match(', [+-><~]|\* |".*?",', " $r "))
				$val = "MATCH($key) AGAINST ($p IN BOOLEAN MODE) * $mult";
			$score[] = $val;
		}

		// On ajoute la premiere cle FULLTEXT de chaque jointure
		$from = array_pop($requete['FROM']);

		if (is_array($jointures[$table]))
		foreach(array_keys($jointures[$table]) as $jtable) {
			$i++;
			if ($mkeys = fulltext_keys($jtable, 'obj'.$i, $serveur)) {
				$score[] = "SUM(MATCH(".array_shift($mkeys).") AGAINST ($p".($boolean ?' IN BOOLEAN MODE':'')."))";
				$_id_join = id_table_objet($jtable);
				$table_join = table_objet($jtable);

				if ($jtable == 'document')
					$from .= "
					LEFT JOIN spip_documents_liens AS lien$i ON (lien$i.id_objet=t.$_id_table AND lien$i.objet='$table')
					LEFT JOIN spip_${table_join} AS obj$i ON lien$i.$_id_join=obj$i.$_id_join
					";
				else
					$from .= "
					LEFT JOIN spip_${jtable}s_${table}s as lien$i ON lien$i.$_id_table=t.$_id_table
					LEFT JOIN spip_${table_join} AS obj$i ON lien$i.$_id_join=obj$i.$_id_join
					";
			}
		}
		$requete['FROM'][] = $from;
		$score = join(' + ', $score).' AS score';
		spip_log($score, 'recherche');

		// si on define(_FULLTEXT_WHERE_$table,'date>"2000")
		// cette contrainte est ajoutee ici:)
		$requete['WHERE'] = array();
		if (defined('_FULLTEXT_WHERE_'.$table))
			$requete['WHERE'][] = constant('_FULLTEXT_WHERE_'.$table);
		else
			if (!test_espace_prive()
			AND in_array($table, array('article', 'rubrique', 'breve', 'forum', 'syndic_article')))
				$requete['WHERE'][] = "t.statut='publie'";

		// nombre max de resultats renvoyes par l'API
		define('_FULLTEXT_MAX_RESULTS', 500);

		// preparer la requete
		$requete['SELECT'] = array(
			"t.$_id_table"
			,$score
		);

		// popularite ?
		if (true # config : "prendre en compte la popularite
		AND $table == 'article')
			$requete['SELECT'][] = "t.popularite";

		# "t.date"
		# "t.note"

		#array_unshift($requete['FROM'], table_objet_sql($table)." AS t");
		$requete['GROUPBY'] = array("t.$_id_table");
		$requete['ORDERBY'] = "score DESC";
		$requete['LIMIT'] = "0,"._FULLTEXT_MAX_RESULTS;
		$requete['HAVING'] = '';

		#var_dump($requete);
		#spip_log($requete,'recherche');
		if (!$s) spip_log(mysql_errno().' '.mysql_error()."\n".$query, 'recherche');
#			exit;
	}

	$r = array();

	$s = sql_select(
		$requete['SELECT'], $requete['FROM'], $requete['WHERE'],
		implode(" ",$requete['GROUPBY']),
		$requete['ORDERBY'], $requete['LIMIT'],
		$requete['HAVING'], $serveur
	);

	while ($t = sql_fetch($s,$serveur)
	AND (!isset($t['score']) OR $t['score']>0)) {
		$id = intval($t[$_id_table]);

		// FULLTEXT
		if ($fulltext) {
			$pts = $t['score'];

			if (isset($t['popularite'])
			AND $mpop = $GLOBALS['meta']['popularite_max'])
				$pts *= (1+$t['popularite']/$mpop);

			$r[$id]['score'] = $pts;

		} ELSE
		// fin FULLTEXT

		if ($options['toutvoir']
		OR autoriser('voir', $table, $id)) {
			// indiquer les champs concernes
			$champs_vus = array();
			$score = 0;
			$matches = array();

			$vu = false;
			foreach ($champs as $champ => $poids) {
				$champ = explode('.',$champ);
				$champ = end($champ);
				if ($n = 
					($options['score'] || $options['matches'])
					? preg_match_all($preg, translitteration_rapide($t[$champ]), $regs, PREG_SET_ORDER)
					: preg_match($preg, translitteration_rapide($t[$champ]))
				) {
					$vu = true;

					if ($options['champs'])
						$champs_vus[$champ] = $t[$champ];
					if ($options['score'])
						$score += $n * $poids;
					if ($options['matches'])
						$matches[$champ] = $regs;

					if (!$options['champs']
					AND !$options['score']
					AND !$options['matches'])
						break;
				}
			}

			if ($vu) {
				$r[$id] = array();
				if ($champs_vus)
					$r[$id]['champs'] = $champs_vus;
				if ($score)
					$r[$id]['score'] = $score;
				if ($matches)
					$r[$id]['matches'] = $matches;
			}
		}
	}


	// Gerer les donnees associees
	if (!$fulltext
	AND isset($jointures[$table])
	AND $joints = recherche_en_base(
			$recherche,
			$jointures[$table],
			array_merge($options, array('jointures' => false))
		)
	) {
		foreach ($joints as $jtable => $jj) {
			$it = id_table_objet($table);
			$ij =  id_table_objet($jtable);
			if ($jtable == 'document')
				$s = sql_select("id_objet as $it, $ij", "spip_documents_liens", array("objet='$table'",sql_in('id_'.${jtable}, array_keys($jj))), '','','','',$serveur);
			else
				$s = sql_select("$it,$ij", "spip_${jtable}s_${table}s", sql_in('id_'.${jtable}, array_keys($jj)), '','','','',$serveur);
			while ($t = sql_fetch($s)) {
				$id = $t[$it];
				$joint = $jj[$t[$ij]];
				if (!isset($r))
					$r = array();
				if (!isset($r[$id]))
					$r[$id] = array();
				if ($joint['score'])
					$r[$id]['score'] += $joint['score'];
				if ($joint['champs'])
				foreach($joint['champs'] as $c => $val)
					$r[$id]['champs'][$jtable.'.'.$c] = $val;
				if ($joint['matches'])
				foreach($joint['matches'] as $c => $val)
					$r[$id]['matches'][$jtable.'.'.$c] = $val;
			}
		}
	}

	return $r;
}


// Effectue une recherche sur toutes les tables de la base de donnees
// options :
// - toutvoir pour eviter autoriser(voir)
// - flags pour eviter les flags regexp par defaut (UimsS)
// - champs pour retourner les champs concernes
// - score pour retourner un score
// On peut passer les tables, ou une chaine listant les tables souhaitees
// http://doc.spip.org/@recherche_en_base
function recherche_en_base($recherche='', $tables=NULL, $options=array(), $serveur='') {
	include_spip('base/abstract_sql');

	if (!is_array($tables)) {
		$liste = liste_des_champs();

		if (is_string($tables)
		AND $tables != '') {
			$toutes = array();
			foreach(explode(',', $tables) as $t)
				if (isset($liste[$t]))
					$toutes[$t] = $liste[$t];
			$tables = $toutes;
			unset($toutes);
		} else
			$tables = $liste;
	}

	if (!strlen($recherche) OR !count($tables))
		return array();

	include_spip('inc/autoriser');

	// options par defaut
	$options = array_merge(array(
		'preg_flags' => 'UimsS',
		'toutvoir' => false,
		'champs' => false,
		'score' => false,
		'matches' => false,
		'jointures' => false,
		'serveur' => $serveur
		),
		$options
	);

	$results = array();

	foreach ($tables as $table => $champs) {
		# lock via memoization, si dispo
		include_spip('inc/memoization');
		if (function_exists('cache_lock')) {
			$lock = 'fulltext '.$table.' '.$recherche;
			cache_lock($lock);
		}

		spip_timer('rech');

		include_spip('inc/recherche_to_array');
		$to_array = charger_fonction('recherche_to_array', 'inc');
		$results[$table] = $to_array($recherche, $table, $options);

		// resultat au format { 
		//      id1 = { 'score' => x, attrs => { } },
		//      id2 = { 'score' => x, attrs => { } },
		//      id3 = { 'score' => x, attrs => { } },
		// }
		##var_dump($results[$table]);


		spip_log("recherche $table ($recherche) : ".count($results[$table])." resultats ".spip_timer('rech'),'recherche');

		if (isset($lock)) cache_unlock($lock);
	}

	return $results;
}


// Effectue une recherche sur toutes les tables de la base de donnees
// http://doc.spip.org/@remplace_en_base
function remplace_en_base($recherche='', $remplace=NULL, $tables=NULL, $options=array()) {
	include_spip('inc/modifier');

	// options par defaut
	$options = array_merge(array(
		'preg_flags' => 'UimsS',
		'toutmodifier' => false
		),
		$options
	);
	$options['champs'] = true;


	if (!is_array($tables))
		$tables = liste_des_champs();

	$results = recherche_en_base($recherche, $tables, $options);

	$preg = '/'.str_replace('/', '\\/', $recherche).'/' . $options['preg_flags'];

	foreach ($results as $table => $r) {
		$_id_table = id_table_objet($table);
		foreach ($r as $id => $x) {
			if ($options['toutmodifier']
			OR autoriser('modifier', $table, $id)) {
				$modifs = array();
				foreach ($x['champs'] as $key => $val) {
					if ($key == $_id_table) next;
					$repl = preg_replace($preg, $remplace, $val);
					if ($repl <> $val)
						$modifs[$key] = $repl;
				}
				if ($modifs)
					modifier_contenu($table, $id,
						array(
							'champs' => array_keys($modifs),
						),
						$modifs);
			}
		}
	}
}

?>
