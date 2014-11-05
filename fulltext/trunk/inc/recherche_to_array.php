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


if (!defined('_ECRIRE_INC_VERSION')) return;


// methodes sql
function inc_recherche_to_array_dist($recherche, $options = array()) {

	// options par defaut
	$options = array_merge(
		array(
		'score' => true,
		'champs' => false,
		'toutvoir' => false,
		'matches' => false,
		'jointures' => false
		),
		$options
	);

	include_spip('inc/rechercher');
	include_spip('inc/autoriser');

	$requete = array(
		"SELECT"=>array(),
		"FROM"=>array(),
		"WHERE"=>array(),
		"GROUPBY"=>array(),
		"ORDERBY"=>array(),
		"LIMIT"=>"",
		"HAVING"=>array()
	);

	$table = sinon($options['table'], 'article');
	if ($options['champs'])
		$champs = $options['champs'];
	else {
		$l = liste_des_champs();
		$champs = $l['article'];
	}
	$serveur = $options['serveur'];


	// s'il n'y a qu'un mot mais <= 3 lettres, il faut le chercher avec une *
	// ex: RFC => RFC* ; car mysql fulltext n'indexe pas ces mots
	if (preg_match('/^\w{1,3}$/', $recherche))
		$recherche .= '*';

	list($methode, $q, $preg) = expression_recherche($recherche, $options);

	$jointures = $options['jointures']
		? liste_des_jointures()
		: array();

	$_id_table = id_table_objet($table);

	// c'est un pis-aller : ca a peu de chance de marcher, mais mieux quand meme que en conservant la ','
	// (aka ca marche au moins dans certains cas comme avec spip_formulaires_reponses_champs)
	if (strpos($_id_table,",")!==false){
		$_id_table = explode(',',$_id_table);
		$_id_table = reset($_id_table);
	}
	
	$table_origine = table_objet_sql($table);
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
	$requete['FROM'][] = $table_origine.' AS t';

	/**
	 * FULLTEXT
	 * Partie spécifique à l'indexation du plugin
	 */
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
		$full_text_where = array();
		foreach ($keys as $name => $key) {
			$val = "MATCH($key) AGAINST ($p)";
			$val_where = $val;
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
			if ($boolean = preg_match(', [+-><~]|\* |".*?",', " $r ")) {
				$val = "MATCH($key) AGAINST ($p IN BOOLEAN MODE) * $mult";
				$val_where = "MATCH($key) AGAINST ($p IN BOOLEAN MODE)";
			}
			$full_text_where[] = $val_where;
			$score[] = $val;
		}
		$full_text_where = array("(".implode(") OR (",$full_text_where).")");

		// On ajoute la premiere cle FULLTEXT de chaque jointure
		$from = array_pop($requete['FROM']);

		if (isset($jointures[$table])
			
			) {
			include_spip('action/editer_liens');
			$trouver_table = charger_fonction('trouver_table','base');
			$cle_depart = id_table_objet($table);
			$table_depart = table_objet($table,$serveur);
			$desc_depart = $trouver_table($table_depart,$serveur);
			$depart_associable = objet_associable($table);
			$i = 0;

			foreach ($jointures[$table] as $table_liee => $champs) {
			//foreach(array_keys($jointures[$table]) as $jtable) {
				$i++;
				spip_log($pe,'recherche');
				if ($mkeys = fulltext_keys($table_liee, 'obj'.$i, $serveur)) {
					$_id_join = id_table_objet($table_liee);
					$table_join = table_objet($table_liee);
					//$lesliens = recherche_tables_liens();

					$subscore = "MATCH(".implode($mkeys,',').") AGAINST ($p".($boolean ? ' IN BOOLEAN MODE':'').")";
					// on peut definir une fonction de recherche jointe pour regler les cas particuliers
						$cle_arrivee =  id_table_objet($table_liee);
						$table_arrivee = table_objet($table_liee,$serveur);
						$desc_arrivee = $trouver_table($table_arrivee,$serveur);
						// cas simple : $cle_depart dans la table_liee
						if (isset($desc_arrivee['field'][$cle_depart])){
							//$s = sql_select("$cle_depart, $cle_arrivee", $desc_arrivee['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '','','','',$serveur);
						}
						// cas simple : $cle_arrivee dans la table
						elseif (isset($desc_depart['field'][$cle_arrivee])){
							//$s = sql_select("$cle_depart, $cle_arrivee", $desc_depart['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '','','','',$serveur);
						}
						// sinon cherchons une table de liaison
						// cas recherche principale article, objet lie document : passer par spip_documents_liens
						elseif ($l = objet_associable($table_liee)){
							list($primary, $table_liens) = $l;
							$join = "
							LEFT JOIN (
							SELECT lien$i.id_objet,$subscore AS score
							FROM $table_liens as lien$i
							JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$_id_join=lien$i.$_id_join
							AND lien$i.objet='$table'
							WHERE $subscore > 0
							ORDER BY score DESC LIMIT 100
							) AS o$i ON o$i.id_objet=t.$_id_table
							";
							$score[] = "IF(SUM(o".$i.".score) IS NULL,0,SUM(o".$i.".score))";
							$from .= $join;
							//$s = sql_select("id_objet as $cle_depart, $primary as $cle_arrivee", $table_liens, array("objet='$table'",sql_in($primary, array_keys($ids_trouves))), '','','','',$serveur);
						}
						// cas recherche principale auteur, objet lie article: passer par spip_auteurs_liens
						elseif ($l = $depart_associable){
							list($primary, $table_liens) = $l;
							$join = "
							LEFT JOIN (
							SELECT lien$i.id_objet,$subscore AS score
							FROM $table_liens as lien$i
							JOIN ".$desc_arrivee['table_sql']." as obj$i ON obj$i.$_id_join=lien$i.$_id_join
									AND lien$i.objet='$table'
									WHERE $subscore > 0
									ORDER BY score DESC LIMIT 100
							) AS o$i ON o$i.id_objet=t.$_id_table";
							$score[] = "IF(SUM(o".$i.".score) IS NULL,0,SUM(o".$i.".score))";
							$from .= $join;
						}
					}
				}
		}
		
		$requete['FROM'][] = $from;
		$score = join(' + ', $score).' AS score';
		spip_log($score, 'recherche');

		// si on define(_FULLTEXT_WHERE_$table,'date>"2000")
		// cette contrainte est ajoutee ici:)
		$requete['WHERE'] = $full_text_where;
		$requete['WHERE'] = array();
		if (defined('_FULLTEXT_WHERE_'.$table))
			$requete['WHERE'][] = constant('_FULLTEXT_WHERE_'.$table);
		else
			if (!test_espace_prive()
			AND !defined('_RECHERCHE_FULLTEXT_COMPLETE')
			AND in_array($table, array('article', 'rubrique', 'breve', 'forum', 'syndic_article')))
				$requete['WHERE'][] = "t.statut='publie'";

		// nombre max de resultats renvoyes par l'API
		define('_FULLTEXT_MAX_RESULTS', 500);

		// preparer la requete
		$requete['SELECT'][] = $score;

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
		#exit;
	}

	$r = array();

	$s = sql_select(
		$requete['SELECT'], $requete['FROM'], $requete['WHERE'],
		implode(" ",$requete['GROUPBY']),
		$requete['ORDERBY'], $requete['LIMIT'],
		$requete['HAVING'], $serveur
	);

	if (!$s) spip_log(mysql_errno().' '.mysql_error()."\n".$recherche, 'recherche');

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

		}
		// fin FULLTEXT


		if (!$fulltext OR (defined('_FULLTEXT_FIELD_SCORE') AND _FULLTEXT_FIELD_SCORE)) {
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
							// on pondere le nombre d'occurence par une fonction inverse de la longueur du contenu
							// 1 = 1 occurence pour 200 mots de 8 lettres = 1600 signes
							$score += $n * $poids * sqrt(sqrt(1600/strlen($t[$champ])));
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
						$r[$id]['score'] = ($fulltext?$r[$id]['score']:0)+$score;
					if ($matches)
						$r[$id]['matches'] = $matches;
				}
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
		include_spip('action/editer_liens');
		$trouver_table = charger_fonction('trouver_table','base');
		$cle_depart = id_table_objet($table);
		$table_depart = table_objet($table,$serveur);
		$desc_depart = $trouver_table($table_depart,$serveur);
		$depart_associable = objet_associable($table);
		foreach ($joints as $table_liee => $ids_trouves) {
			// on peut definir une fonction de recherche jointe pour regler les cas particuliers
			if (
					!(
							$rechercher_joints = charger_fonction("rechercher_joints_${table}_${table_liee}","inc",true)
							or $rechercher_joints = charger_fonction("rechercher_joints_objet_${table_liee}","inc",true)
							or $rechercher_joints = charger_fonction("rechercher_joints_${table}_objet_lie","inc",true)
			)
			){
				$cle_arrivee =  id_table_objet($table_liee);
				$table_arrivee = table_objet($table_liee,$serveur);
				$desc_arrivee = $trouver_table($table_arrivee,$serveur);
				// cas simple : $cle_depart dans la table_liee
				if (isset($desc_arrivee['field'][$cle_depart])){
					$s = sql_select("$cle_depart, $cle_arrivee", $desc_arrivee['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '','','','',$serveur);
				}
				// cas simple : $cle_arrivee dans la table
				elseif (isset($desc_depart['field'][$cle_arrivee])){
					$s = sql_select("$cle_depart, $cle_arrivee", $desc_depart['table_sql'], sql_in($cle_arrivee, array_keys($ids_trouves)), '','','','',$serveur);
				}
				// sinon cherchons une table de liaison
				// cas recherche principale article, objet lie document : passer par spip_documents_liens
				elseif ($l = objet_associable($table_liee)){
					list($primary, $table_liens) = $l;
					$s = sql_select("id_objet as $cle_depart, $primary as $cle_arrivee", $table_liens, array("objet='$table'",sql_in($primary, array_keys($ids_trouves))), '','','','',$serveur);
				}
				// cas recherche principale auteur, objet lie article: passer par spip_auteurs_liens
				elseif ($l = $depart_associable){
					list($primary, $table_liens) = $l;
					$s = sql_select("$primary as $cle_depart, id_objet as $cle_arrivee", $table_liens, array("objet='$table_liee'",sql_in('id_objet', array_keys($ids_trouves))), '','','','',$serveur);
				}
				// cas table de liaison generique spip_xxx_yyy
				elseif($t=$trouver_table($table_arrivee."_".$table_depart,$serveur)
						OR $t=$trouver_table($table_depart."_".$table_arrivee,$serveur)){
					$s = sql_select("$cle_depart,$cle_arrivee", $t["table_sql"], sql_in($cle_arrivee, array_keys($ids_trouves)), '','','','',$serveur);
				}
			}
			else
				list($cle_depart,$cle_arrivee,$s) = $rechercher_joints($table,$table_liee,array_keys($ids_trouves), $serveur);
	
			while ($t = is_array($s)?array_shift($s):sql_fetch($s)) {
				$id = $t[$cle_depart];
				$joint = $ids_trouves[$t[$cle_arrivee]];
				if (!isset($results))
					$results = array();
				if (!isset($results[$id]))
					$results[$id] = array();
				if (isset($joint['score']) and $joint['score']) {
					$results[$id]['score'] += $joint['score'];
				}
				if (isset($joint['champs']) and $joint['champs']) {
					foreach($joint['champs'] as $c => $val) {
						$results[$id]['champs'][$table_liee.'.'.$c] = $val;
					}
				}
				if (isset($joint['matches']) and $joint['matches']) {
					foreach($joint['matches'] as $c => $val) {
						$results[$id]['matches'][$table_liee.'.'.$c] = $val;
					}
				}
			}
		}
	}

	return $r;
}


?>