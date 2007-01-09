<?php

// {recherche}
// http://www.spip.net/@recherche
// http://doc.spip.org/@critere_recherche_dist
function critere_recherche_ext($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	$t = $boucle->id_table;
	if (in_array($t,$table_des_tables))
		$t = "spip_$t";

	// Ne pas executer la requete en cas de hash vide
	$boucle->hash = '
	// RECHERCHE
	list($rech_select, $rech_where) = prepare_recherche_ext($GLOBALS["recherche"], "'.$boucle->primary.'", "'.$boucle->id_table.'", "'.$t.'", "'.$crit->cond.'");
	';

	// Sauf si le critere est conditionnel {recherche ?}
	if (!$crit->cond)
		$boucle->hash .= '
	if ($rech_where) ';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici
	$boucle->select[]= '$rech_select as points';

	// et la recherche trouve
	$boucle->where[]= '$rech_where';
}

function prepare_recherche_ext($recherche, $primary = 'id_article', $id_table='articles',$nom_table='spip_articles', $cond=false) {
	static $cache = array();
	static $fcache = array();
	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// Premier passage : chercher eventuel un cache des donnees sur le disque
	if (false && !$cache[$recherche]['hash']) {
		$dircache = sous_repertoire(_DIR_CACHE,'rech');
		$fcache[$recherche] =
			$dircache . substr(md5($recherche),0,10).'.txt';
		if (lire_fichier($fcache[$recherche], $contenu))
			$cache[$recherche] = @unserialize($contenu);
	}

	// si on n'a pas encore traite les donnees dans une boucle precedente
	if (!$cache[$recherche][$primary]) {
		if (!$cache[$recherche]['hash'])
			$cache[$recherche]['hash'] = requete_hash_ext($recherche);
		list($hash_recherche, $hash_recherche_strict, $hash_recherche_not, $hash_recherche_and, $hash_recherche_strict_and)
			= $cache[$recherche]['hash'];

		$strict = array();
		if ($hash_recherche_strict)
			foreach (split(',',$hash_recherche_strict) as $h)
				$strict[$h] = 99;
		
		if ($hash_recherche_strict_and)
			foreach (split(',',$hash_recherche_strict_and) as $h)
				$strict[$h] = 99;

		$index_id_table = id_index_table($nom_table);
		$points = array();
		
		$objet_and = array();
		$object_not = array();
		if($hash_recherche_and) {
			$s = spip_query("SELECT id_objet as id FROM spip_index WHERE id_table='$index_id_table' AND hash IN ($hash_recherche_and) GROUP BY id HAVING COUNT(DISTINCT hash)=".count(split(",",$hash_recherche_and)));
			while ($r = spip_fetch_array($s)) 
				$objet_and[]=$r['id'];
		}
		if($hash_recherche_not) {
			$s = spip_query("SELECT DISTINCT id_objet as id FROM spip_index WHERE hash IN ($hash_recherche_not) AND id_table='$index_id_table'");
			while ($r = spip_fetch_array($s))
				$objet_not[]=$r['id'];														
		}
		if(count($objet_and))
			$list_and = " AND id_objet IN (".join(",",$objet_and).")";
		if(count($objet_not))
			$list_not = " AND id_objet NOT  IN (".join(",",$objet_not).")";
		if($hash_recherche) {
			$list_hash = " AND hash IN (".$hash_recherche.")";
		}
		
		if($list_hash || $list_and || $list_not) {
			$query = "SELECT hash,points,id_objet as id FROM spip_index WHERE id_table='$index_id_table'".$list_and.$list_not.$list_hash;  
			
			$s = spip_query($query);
				
			while ($r = spip_fetch_array($s))
				$points[$r['id']]
				+= (1 + $strict[$r['hash']]) * $r['points'];
			spip_free_result($s);
			arsort($points, SORT_NUMERIC);
		}

		# calculer le {id_article IN()} et le {... as points}
		if (!count($points)) {
			$cache[$recherche][$primary] = array("''", 0);
		} else {
			$ids = array();
			$select = '0';
			foreach ($points as $id => $p)
				$listes_ids[$p] .= ','.$id;
			foreach ($listes_ids as $p => $liste_ids)
				$select .= "+$p*(".
					calcul_mysql_in("$id_table.$primary", substr($liste_ids, 1))
					.") ";

			$cache[$recherche][$primary] = array($select,
				'('.calcul_mysql_in("$id_table.$primary",
					join(',',array_keys($points))).')'
				);
		}

		// ecrire le cache de la recherche sur le disque
		ecrire_fichier($fcache[$recherche], serialize($cache[$recherche]));
		// purger le petit cache
		nettoyer_petit_cache('rech', 300);
	}
	return $cache[$recherche][$primary];	
}

function requete_hash_ext($rech) {
	// recupere les mots de la recherche
	$GLOBALS['translitteration_complexe'] = true;
	$s = mots_indexation_ext($rech);
	unset($dico);
	unset($h);
	
	// cherche les mots dans le dico
	while (list(, $val) = each($s)) {
		list($rq, $rq_strict,$mode) = requete_dico_ext ($val);
		if ($rq)
			$dico[$mode][] = $rq;
		if ($rq_strict)
			$dico_strict[$mode][] = $rq_strict;
	}

	// Attention en MySQL 3.x il faut passer par HEX(hash)
	// alors qu'en MySQL 4.1 c'est interdit !
	$vers = spip_query("SELECT VERSION() AS v");
	$vers = spip_fetch_array($vers);
	if (($vers['v']{0} == 4 AND $vers['v']{2} >= 1)
		OR $vers['v']{0} > 4) {
		$hex_fmt = '';
		$select_hash = 'hash AS h';
	} else {
		$hex_fmt = '0x';
		$select_hash = 'HEX(hash) AS h';
	}

	// compose la recherche dans l'index
	$cond = "";
	if ($dico_strict["OR"]) $cond = join(" OR ", $dico_strict["OR"]);
		
	if ($cond) {	
		$result2 = spip_query("SELECT $select_hash FROM spip_index_dico WHERE ".$cond);

		while ($row2 = spip_fetch_array($result2))
			$h_strict[] = $hex_fmt.$row2['h'];
	}

	$cond = "";
	if ($dico_strict["AND"])	$cond = join(" OR ", $dico_strict["AND"]);
	if ($cond) {	
		$result2 = spip_query("SELECT $select_hash FROM spip_index_dico WHERE ".$cond);

		while ($row2 = spip_fetch_array($result2))
			$h_strict_and[] = $hex_fmt.$row2['h'];
	}

	$cond = "";
	if ($dico["OR"]) $cond = join(" OR ", $dico["OR"]);
	if ($cond) {	
		$result2 = spip_query("SELECT $select_hash FROM spip_index_dico WHERE ".$cond);

		while ($row2 = spip_fetch_array($result2))
			$h[] = $hex_fmt.$row2['h'];
	}

	$cond = "";
	if ($dico["AND"])	$cond = join(" OR ", $dico["AND"]);
	if ($cond) {	
		$result2 = spip_query("SELECT $select_hash FROM spip_index_dico WHERE ".$cond);

		while ($row2 = spip_fetch_array($result2))
			$h_and[] = $hex_fmt.$row2['h'];
	}

		
	$cond = "";
	if ($dico["NOT"]) $cond = join(" OR ", $dico["NOT"]);
		
	if ($cond) {	
		$result2 = spip_query("SELECT $select_hash FROM spip_index_dico WHERE ".$cond);

		while ($row2 = spip_fetch_array($result2))
			$h_not[] = $hex_fmt.$row2['h'];
	}
	
	if ($h_strict)
		$hash_recherche_strict = join(",", $h_strict);
	else
		$hash_recherche_strict = "0";
		
	if ($h_strict_and)
		$hash_recherche_strict_and = join(",", $h_strict_and);
	else
		$hash_recherche_strict_and = "0";

	if ($h)
		$hash_recherche = join(",", $h);
	else
		$hash_recherche = "0";

	if ($h_and) 
		$hash_recherche_and = join(",", $h_and);
	else
		$hash_recherche_and = "0";

	if ($h_not) 
		$hash_recherche_not = join(",", $h_not);
	else
		$hash_recherche_not = "0";

	return array($hash_recherche, $hash_recherche_strict, $hash_recherche_not, $hash_recherche_and, $hash_recherche_strict_and);
}

// Renvoie la liste des "mots" d'un texte (ou d'une requete adressee au moteur)
// http://doc.spip.org/@mots_indexation
function mots_indexation_ext($texte, $min_long = 3) {
	include_spip('inc/charsets');
	include_spip('inc/texte');

	// Point d'entree pour traiter le texte avant indexation
	$texte = pipeline('pre_indexation', $texte);

	// Recuperer les parametres des modeles
	$texte = traiter_modeles($texte, true);
	
	// Supprimer les tags HTML
	$texte = preg_replace(',<.*>,Ums',' ',$texte);

	// Translitterer (supprimer les accents, recuperer les &eacute; etc)
	// la translitteration complexe (vietnamien, allemand) duplique
	// le texte, en mettant bout a bout une translitteration simple +
	// une translitteration riche
	if ($GLOBALS['translitteration_complexe'])
		$texte_c = ' '.translitteration_complexe ($texte, 'AUTO', true);
	else
		$texte_c = '';
	$texte = translitteration($texte);
	if($texte!=trim($texte_c)) $texte .= $texte_c;
	# NB. tous les caracteres non translitteres sont retournes en utf-8

	// OPTIONNEL //  Gestion du tiret '-' :
	// "vice-president" => "vice"+"president"+"vicepresident"
#	$texte = preg_replace(',(\w+)-(\w+),', '\1 \2 \1\2', $texte);

	// Supprimer les caracteres de ponctuation, les guillemets...
	$e = "],:;*\"!\r\n\t\\/)}{[|@<>$%'`?\~.^(";
	$texte = strtr($texte, $e, ereg_replace('.', ' ', $e));

	//eliminare  +\- non all'inizio di una parola
	$texte = preg_replace(",(?:\S)[\-+],"," ",$texte);
	// Cas particulier : sigles d'au moins deux lettres
	$texte = preg_replace("/ ([A-Z][0-9A-Z]{1,".($min_long - 1)."}) /",
		' \\1___ ', $texte.' ');

	// Tout passer en bas de casse
	$texte = strtolower($texte);

	// Retourner sous forme de table
	return preg_split("/ +/", trim($texte));
}

// rechercher un mot dans le dico
// retourne deux methodes : lache puis strict
// http://doc.spip.org/@requete_dico
function requete_dico_ext($val) {
	$min_long = 3;
	
	preg_match(",^([+-]?)(.*),",$val,$mod);
	switch($mod[1]) {
		case '':
			$mode = "OR";
			break;
		case '+':
			$mode = "AND";
			break;
		case '-':
			$mode = "NOT";
			break;
	}
	//set logical operator between the various where parts
	$val = $mod[2];
	// cas normal
	if (strlen($val) > $min_long) {
	  return array("dico LIKE ".spip_abstract_quote($val. "%"), "dico = " . spip_abstract_quote($val),$mode);
	} else
	  return array("dico = ".spip_abstract_quote($val."___"), "dico = ".spip_abstract_quote($val."___"),$mode);
}


?>
