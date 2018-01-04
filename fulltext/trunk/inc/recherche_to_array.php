<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// methodes sql
function inc_recherche_to_array_dist($recherche, $options = array()) {

	// options par defaut
	$options = array_merge(
		array(
			'score' => true,
			'champs' => false,
			'toutvoir' => false,
			'matches' => false,
			'jointures' => false,
			'table' => 'article'
		),
		$options
	);

	include_spip('inc/rechercher');
	include_spip('inc/autoriser');

	$requete = array(
		'SELECT' => array(),
		'FROM' => array(),
		'WHERE' => array(),
		'GROUPBY'=> array(),
		'ORDERBY' => array(),
		'LIMIT' => '',
		'HAVING' => array()
	);

	$table = $options['table'];
	if ($options['champs']) {
		$champs = $options['champs'];
	} else {
		$l = liste_des_champs();
		$champs = $l[$table];
	}
	$serveur = $options['serveur'];

	/**
	 * Verifier l'existence d'index fulltext ou sinon on fallback sur la methode de recherche du core
	 * sans fulltext
	 */
	$keys = fulltext_keys($table, 't', $serveur);
	if (!$keys) {
		$recherche_to_array_fallback = charger_fonction('recherche_to_array_fallback', 'inc');
		return $recherche_to_array_fallback($recherche, $options);
	}

	// s'il n'y a qu'un mot mais <= 3 lettres, il faut le chercher avec une *
	// ex: RFC => RFC* ; car mysql fulltext n'indexe pas ces mots
	if (preg_match('/^\w{1,3}$/', $recherche)) {
		$recherche .= '*';
	}

	list($methode, $q, $preg) = expression_recherche($recherche, $options);

	$jointures = $options['jointures']
		? liste_des_jointures()
		: array();

	$_id_table = id_table_objet($table);

	// c'est un pis-aller : ca a peu de chance de marcher, mais mieux quand meme que en conservant la ','
	// (aka ca marche au moins dans certains cas comme avec spip_formulaires_reponses_champs)
	if (strpos($_id_table, ',') !== false) {
		$_id_table = explode(',', $_id_table);
		$_id_table = reset($_id_table);
	}

	$requete['SELECT'][] = 't.'.$_id_table;
	$a = array();
	// Recherche fulltext
	foreach ($champs as $champ => $poids) {
		if (is_array($champ)) {
			spip_log('requetes imbriquees interdites');
		} else {
			if (strpos($champ, '.') === false) {
				$champ = "t.$champ";
			}
			$requete['SELECT'][] = $champ;
			$a[] = $champ.' '.$methode.' '.$q;
		}
	}
	if ($a) {
		$requete['WHERE'][] = join(' OR ', $a);
	}
	$requete['FROM'][] = table_objet_sql($table).' AS t';

	/**
	 * FULLTEXT
	 * Partie spécifique à l'indexation du plugin
	 */
	$fulltext = false; # cette table est-elle fulltext?
	if ($keys) {
		$fulltext = true;

		$r = trim(preg_replace(',\s+,', ' ', $recherche));

		// si espace, ajouter la meme chaine avec des guillemets pour ameliorer la pertinence
		$pe = (strpos($r, ' ') and strpos($r, '"') === false) ? sql_quote(trim("\"$r\""), $serveur) : '';

		// On utilise la translitteration pour contourner le pb des bases
		// declarees en iso-latin mais remplies d'utf8
		if (($r2 = translitteration($r)) != $r) {
			$r .= ' '.$r2;
		}

		if(defined('_FULLTEXT_ASTERISQUE_PARTOUT') && _FULLTEXT_ASTERISQUE_PARTOUT) {
			$r = explode(' ', $r);
			foreach ($r as $key => $item) {
				$r[$key] = preg_match('#[\*\(\)]+#', $item) ? $item : $item.'*';
			}
			$r = join(' ', $r);
		}

		$p = sql_quote(trim("$r"), $serveur);

		// On va additionner toutes les cles FULLTEXT
		// de la table
		$score = array();
		$full_text_where = array();
		foreach ($keys as $name => $key) {
			$val = "MATCH($key) AGAINST ($p)";
			$val_where = $val;
			// Une chaine exacte rapporte plein de points
			if ($pe) {
				$val .= "+ 2 * MATCH($key) AGAINST ($pe)";
			}

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
				and isset($champs[$name])
				and $ponderation = $champs[$name]) {
				$mult = $ponderation;
			}

			// Appliquer le coefficient multiplicatif
			if ($mult != 1) {
				$val = "($val) * $mult";
			}

			// si symboles booleens les prendre en compte
			if ($boolean = preg_match(', [+-><~]|\* |".*?",', " $r ")) {
				$val = "MATCH($key) AGAINST ($p IN BOOLEAN MODE) * $mult";
				$val_where = "MATCH($key) AGAINST ($p IN BOOLEAN MODE)";
			}
			$full_text_where[] = $val_where;
			$score[] = $val;
		}

		// On ajoute la premiere cle FULLTEXT de chaque jointure
		$from = array_pop($requete['FROM']);

		if (isset($jointures[$table])) {
			// nombre max de resultats provenant des jointures
			if (!defined('_FULLTEXT_MAX_RESULTS_JOINTURES')){
				define('_FULLTEXT_MAX_RESULTS_JOINTURES', 100);
			}
			include_spip('action/editer_liens');
			$trouver_table = charger_fonction('trouver_table', 'base');
			$cle_depart = id_table_objet($table);
			$table_depart = table_objet($table, $serveur);
			$desc_depart = $trouver_table($table_depart, $serveur);
			$depart_associable = false;
			if ($table != 'article') {
				$depart_associable = objet_associable($table);
			}
			$i = 0;
			foreach ($jointures[$table] as $table_liee => $champs) {
				if ($table_liee !== $table) {
					$i++;
					spip_log($pe, 'recherche');
					if ($mkeys = fulltext_keys($table_liee, 'obj'.$i, $serveur)) {
						$_id_join = id_table_objet($table_liee);
						$table_join = table_objet($table_liee);

						$subscore = 'MATCH(' . implode($mkeys, ',').") AGAINST ($p".($boolean ? ' IN BOOLEAN MODE':'') . ')';
						// on peut definir une fonction de recherche jointe pour regler les cas particuliers
						$cle_arrivee = id_table_objet($table_liee);
						$table_arrivee = table_objet($table_liee, $serveur);
						$desc_arrivee = $trouver_table($table_arrivee, $serveur);
						/**
						 * cas simple : $cle_depart dans la table_liee
						 *
						 * Ce cas pourrait exister par exemple si on activait une jointure de recherche sur les articles
						 * avec la table spip_evenements du plugin agenda.
						 * Il suffirait d'ajouter la ligne suivante dans le pipeline "declarer_tables_objets_sql"
						 * dans le fichier base/agenda_evenements :
						 * $tables['spip_articles']['rechercher_jointures']['evenement'] = array('titre' => 8, 'descriptif' => 5, 'lieu' => 5, 'adresse' => 3);
						 *
						 */
						if (isset($desc_arrivee['field'][$cle_depart])) {
							$join = "
								LEFT JOIN (
								SELECT lien$i.$cle_depart,$subscore AS score
								FROM ".$desc_depart['table_sql']." as lien$i
								JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$cle_depart=lien$i.$cle_depart
								WHERE $subscore > 0
								ORDER BY score DESC LIMIT "._FULLTEXT_MAX_RESULTS_JOINTURES."
								) AS o$i ON o$i.$cle_depart=t.$cle_depart";
							$score[] = 'IF(SUM(o' . $i . '.score) IS NULL,0,SUM(o' . $i . '.score))';
							$from .= $join;
							$full_text_where[] = 'o' . $i . '.score IS NOT NULL';
						} elseif (isset($desc_depart['field'][$cle_arrivee])) {
							/**
							 * cas simple : $cle_arrivee dans la table
							 *
							 * Ce cas pourrait exister par exemple si on activait une jointure de recherche
							 * sur les évènements (du plugin agenda) avec la table spip_articles.
							 * Il suffirait d'ajouter la ligne suivante dans le pipeline "declarer_tables_objets_sql"
							 * dans le fichier base/agenda_evenements :
							 * $tables['spip_evenements']['rechercher_jointures']['article'] = array('titre' => 8, 'texte' => 5);
							 */
							$join = "
								LEFT JOIN (
								SELECT lien$i.$cle_depart,$subscore AS score
								FROM ".$desc_depart['table_sql']." as lien$i
								JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$_id_join=lien$i.$_id_join
								WHERE $subscore > 0
								ORDER BY score DESC LIMIT "._FULLTEXT_MAX_RESULTS_JOINTURES."
								) AS o$i ON o$i.$cle_depart=t.$cle_depart";
							$score[] = 'IF(SUM(o' . $i . '.score) IS NULL,0,SUM(o' . $i . '.score))';
							$from .= $join;
							$full_text_where[] = 'o' . $i . '.score IS NOT NULL';
						} elseif ($l = objet_associable($table_liee)) {
							// sinon cherchons une table de liaison
							// cas recherche principale article, objet lie document : passer par spip_documents_liens
							list($primary, $table_liens) = $l;
							$join = "
							LEFT JOIN (
								SELECT lien$i.id_objet,$subscore AS score
								FROM $table_liens as lien$i
								JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$_id_join=lien$i.$_id_join
								AND lien$i.objet='$table'
								WHERE $subscore > 0
								ORDER BY score DESC LIMIT "._FULLTEXT_MAX_RESULTS_JOINTURES."
								) AS o$i ON o$i.id_objet=t.$_id_table";
							$score[] = 'IF(SUM(o' . $i . '.score) IS NULL,0,SUM(o' . $i . '.score))';
							$from .= $join;
							$full_text_where[] = 'o' . $i . '.score IS NOT NULL';
						} elseif ($l = $depart_associable) {
							// cas recherche principale auteur, objet lie article: passer par spip_auteurs_liens
							list($primary, $table_liens) = $l;
							$join = "
								LEFT JOIN (
								SELECT lien$i.id_objet,$subscore AS score
								FROM $table_liens as lien$i
								JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$_id_join=lien$i.$_id_join
										AND lien$i.objet='$table'
										WHERE $subscore > 0
										ORDER BY score DESC LIMIT "._FULLTEXT_MAX_RESULTS_JOINTURES."
								) AS o$i ON o$i.id_objet=t.$_id_table";
							$score[] = 'IF(SUM(o' . $i . '.score) IS NULL,0,SUM(o' . $i . '.score))';
							$from .= $join;
							$full_text_where[] = 'o' . $i . '.score IS NOT NULL';
						}
					}
				}
			}
		}

		$full_text_where = array('(('.implode(') OR (', $full_text_where).'))');

		$requete['FROM'][] = $from;
		$score = join(' + ', $score).' AS score';
		spip_log($score, 'recherche');

		// si on define(_FULLTEXT_WHERE_$table,'date>"2000")
		// cette contrainte est ajoutee ici:)
		$requete['WHERE'] = $full_text_where;

		if (defined('_FULLTEXT_WHERE_'.$table)) {
			$requete['WHERE'][] = constant('_FULLTEXT_WHERE_'.$table);
		} elseif (!test_espace_prive()
			and !defined('_RECHERCHE_FULLTEXT_COMPLETE')
			and in_array($table, array('article', 'rubrique', 'breve', 'forum', 'syndic_article'))) {
				$requete['WHERE'][] = "t.statut='publie'";
		}

		// nombre max de resultats renvoyes par l'API
		if (!defined('_FULLTEXT_MAX_RESULTS')) {
			define('_FULLTEXT_MAX_RESULTS', 500);
		}

		// preparer la requete
		$requete['SELECT'][] = $score;

		// popularite ?
		if (true # config : "prendre en compte la popularite
			and $table == 'article') {
			$requete['SELECT'][] = 't.popularite';
		}

		# "t.date"
		# "t.note"

		#array_unshift($requete['FROM'], table_objet_sql($table)." AS t");
		$requete['GROUPBY'] = array("t.$_id_table");
		$requete['ORDERBY'] = 'score DESC';
		$requete['LIMIT'] = '0,'._FULLTEXT_MAX_RESULTS;
		$requete['HAVING'] = '';

		#var_dump($requete);
		#spip_log($requete,'recherche');
		#exit;
	}

	$r = array();

	$s = sql_select(
		$requete['SELECT'],
		$requete['FROM'],
		$requete['WHERE'],
		implode(' ', $requete['GROUPBY']),
		$requete['ORDERBY'],
		$requete['LIMIT'],
		$requete['HAVING'],
		$serveur
	);

	if (!$s) {
		spip_log(sql_errno() . ' ' . sql_error() . "\n" . $recherche, 'recherche');
	}

	while ($t = sql_fetch($s, $serveur)
		and (!isset($t['score']) or $t['score']>0)) {
		$id = intval($t[$_id_table]);

		// FULLTEXT
		if ($fulltext) {
			$pts = $t['score'];

			if (isset($t['popularite'])
				and $mpop = $GLOBALS['meta']['popularite_max']) {
				$pts *= (1+$t['popularite']/$mpop);
			}

			$r[$id]['score'] = $pts;
		}
		// fin FULLTEXT

		if (!$fulltext or (defined('_FULLTEXT_FIELD_SCORE') and _FULLTEXT_FIELD_SCORE)) {
			if ($options['toutvoir']
				or autoriser('voir', $table, $id)) {
				// indiquer les champs concernes
				$champs_vus = array();
				$score = 0;
				$matches = array();

				$vu = false;
				foreach ($champs as $champ => $poids) {
					$champ = explode('.', $champ);
					$champ = end($champ);
					// éviter des divisions par zéro sur le calcul du score :
					// tester seulement les champs avec du contenu !
					if ($len = strlen($t[$champ])) {
						// translitteration_rapide uniquement si on est deja en utf-8
						$value = ($GLOBALS['meta']['charset']=='utf-8' ? translitteration_rapide($t[$champ]) : translitteration($t[$champ]));
						if ($n =
							($options['score'] || $options['matches'])
							? preg_match_all($preg, $value, $regs, PREG_SET_ORDER)
							: preg_match($preg, $value)
						) {
							$vu = true;

							if ($options['champs']) {
								$champs_vus[$champ] = $t[$champ];
							}
							if ($options['score']) {
								// on pondere le nombre d'occurence par une fonction inverse de la longueur du contenu
								// 1 = 1 occurence pour 200 mots de 8 lettres = 1600 signes
								$score += $n * $poids * sqrt(sqrt(1600/$len));
							}
							if ($options['matches']) {
								$matches[$champ] = $regs;
							}
							if (!$options['champs']
								and !$options['score']
								and !$options['matches']) {
								break;
							}
						}
					}
				}

				if ($vu) {
					$r[$id] = array();
					if ($champs_vus) {
						$r[$id]['champs'] = $champs_vus;
					}
					if ($score) {
						$r[$id]['score'] = ($fulltext?$r[$id]['score']:0)+$score;
					}
					if ($matches) {
						$r[$id]['matches'] = $matches;
					}
				}
			}
		}
	}

	// Gerer les donnees associees
	if (!$fulltext
		and isset($jointures[$table])
		and $joints = recherche_en_base(
			$recherche,
			$jointures[$table],
			array_merge($options, array('jointures' => false))
		)
	) {
		include_spip('action/editer_liens');
		$trouver_table = charger_fonction('trouver_table', 'base');
		$cle_depart = id_table_objet($table);
		$table_depart = table_objet($table, $serveur);
		$desc_depart = $trouver_table($table_depart, $serveur);
		$depart_associable = objet_associable($table);
		foreach ($joints as $table_liee => $ids_trouves) {
			// on peut definir une fonction de recherche jointe pour regler les cas particuliers
			if (!(
				$rechercher_joints = charger_fonction("rechercher_joints_${table}_${table_liee}", 'inc', true)
				or $rechercher_joints = charger_fonction("rechercher_joints_objet_${table_liee}", 'inc', true)
				or $rechercher_joints = charger_fonction("rechercher_joints_${table}_objet_lie", 'inc', true)
			)) {
				$cle_arrivee =  id_table_objet($table_liee);
				$table_arrivee = table_objet($table_liee, $serveur);
				$desc_arrivee = $trouver_table($table_arrivee, $serveur);
				// cas simple : $cle_depart dans la table_liee
				if (isset($desc_arrivee['field'][$cle_depart])) {
					$s = sql_select("$cle_depart, $cle_arrivee", $desc_arrivee['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '', '', '', '', $serveur);
				} elseif (isset($desc_depart['field'][$cle_arrivee])) {
					// cas simple : $cle_arrivee dans la table
					$s = sql_select("$cle_depart, $cle_arrivee", $desc_depart['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '', '', '', '', $serveur);
				} elseif ($l = objet_associable($table_liee)) {
					// sinon cherchons une table de liaison
					// cas recherche principale article, objet lie document : passer par spip_documents_liens
					list($primary, $table_liens) = $l;
					$s = sql_select("id_objet as $cle_depart, $primary as $cle_arrivee", $table_liens, array("objet='$table'",sql_in($primary, array_keys($ids_trouves))), '', '', '', '', $serveur);
				} elseif ($l = $depart_associable) {
					// cas recherche principale auteur, objet lie article: passer par spip_auteurs_liens
					list($primary, $table_liens) = $l;
					$s = sql_select("$primary as $cle_depart, id_objet as $cle_arrivee", $table_liens, array("objet='$table_liee'", sql_in('id_objet', array_keys($ids_trouves))), '', '', '', '', $serveur);
				} elseif ($t = $trouver_table($table_arrivee . '_' . $table_depart, $serveur)
					or $t=$trouver_table($table_depart . '_' . $table_arrivee, $serveur)) {
					// cas table de liaison generique spip_xxx_yyy
					$s = sql_select("$cle_depart,$cle_arrivee", $t['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '', '', '', '', $serveur);
				}
			} else {
				list($cle_depart,$cle_arrivee,$s) = $rechercher_joints($table,$table_liee,array_keys($ids_trouves), $serveur);
			}
			while ($t = is_array($s) ? array_shift($s) : sql_fetch($s)) {
				$id = $t[$cle_depart];
				$joint = $ids_trouves[$t[$cle_arrivee]];
				if (!isset($results)) {
					$results = array();
				}
				if (!isset($results[$id])) {
					$results[$id] = array();
				}
				if (isset($joint['score']) and $joint['score']) {
					if (!isset($results[$id]['score'])) {
						$results[$id]['score'] = 0;
					}
					$results[$id]['score'] += $joint['score'];
				}
				if (isset($joint['champs']) and $joint['champs']) {
					foreach ($joint['champs'] as $c => $val) {
						$results[$id]['champs'][$table_liee.'.'.$c] = $val;
					}
				}
				if (isset($joint['matches']) and $joint['matches']) {
					foreach ($joint['matches'] as $c => $val) {
						$results[$id]['matches'][$table_liee.'.'.$c] = $val;
					}
				}
			}
		}
	}

	return $r;
}
