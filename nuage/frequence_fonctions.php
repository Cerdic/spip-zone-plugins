<?php

function boucle_MOTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_mots";

	return $boucle->modificateur['frequence'] ?
		calculer_boucle_avec_frequence($id_boucle, $boucles) :
		calculer_boucle($id_boucle, $boucles);
}

function calculer_boucle_avec_frequence($id_boucle, &$boucles) {
	//compatibilite 1.9.2 et SPIP SVN
	$spip_abstract_fetch = function_exists('spip_abstract_fetch') ? 'spip_abstract_fetch' : 'sql_fetch';
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_mots";

	//retenir max_frequence
	$max = '$max_frequence'.$boucle->id_boucle;
	$frequence = '$Pile[$SP][\'frequence'.$boucle->id_boucle.'\']';
	$code_avant = "\n\t" . $max .' = 0;
	$PileTemp = array();';
	$code = "\n\t".'while ($Pile[$SP] = @'.$spip_abstract_fetch.'($result,"")) {' . "\n\t\t" .
		$max.' = max('.$max.', '.$frequence.');' . "\n\t\t" .
		'$PileTemp[] = $Pile[$SP];
	}

	while (list(,$Pile[$SP]) = each($PileTemp)) {';
	$calcul = calculer_boucle($id_boucle, $boucles);
	$calcul = preg_replace(',(\t\/\/ RESULTATS),', '$1'.$code_avant, $calcul);
	$calcul = preg_replace(',(while([^{]*){),', $code, $calcul);
	return $calcul;
}

function critere_frequence_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$not = $crit->not;
	$boucle = &$boucles[$idb];

	if ($not)
		erreur_squelette(_T('zbug_info_erreur_squelette'), $crit->op);

	if (empty($boucle->jointures)) {
		erreur_squelette(_T('zbug_info_erreur_squelette'), _L('frequence sur table sans jointures'));
		return;
	}

	//analyser chaque criteres de frequence
	$nom = $table_des_tables[$boucle->type_requete];
	$parent = $boucles[$idb]->id_parent;
	$criteres = array();
	//Pour l'instant, un seul parametre
	while(list(,$p) = each($crit->param)) {
 		$param = calculer_liste($p, array(), $boucles, $parent);
 		$type = preg_match(',^\(?\'(\w+)(\s*)?([!=<>]+)?(\s*)?,', $param, $regs) ? $regs[1] : $boucle->jointures[0];
		$op = $regs[3] ? $regs[3] : '>=';
		if($val = $regs[0] ? preg_replace(',' . preg_quote($regs[0]) . ',', '', $param) : 0) {
			$val = preg_replace(',\'$,', '', $val);
			$val = preg_replace(',^\'\s\.\s(.*)\)$,Um', '$1', $val);
			$val = $val ? $val : 0;
		}
		//Trouver une jointure n:n (cad table spip_mots_articles)
		if(in_array($_type = $nom.'_'.$type, $boucle->jointures))
			$criteres[] = array($op, $_type, $val);
		else {
			erreur_squelette(_T('zbug_info_erreur_squelette'), _L('frequence '.$type.': jointure inconnue'));
		}
	}
	if(empty($criteres)) $criteres[0] = array('>=', $boucle->jointures[0], 0);
	
	//composer la requete pour la jointure
	$primary = $boucle->primary;
	$id_table = $boucle->id_table . '.' . $primary;
	foreach($criteres as $critere) {
		$frequence = $boucle->modificateur['frequence'] = "frequence".$idb;
		list($op, $type, $val) = $critere;
		//compatibilite SVN et 1.9.2
		if(function_exists('trouver_def_table')) {
			$nom = $table_des_tables[$type];
			list($table, $desc) = trouver_def_table($nom ? $nom : $type, $boucle);
		}
		else {
      $trouver_table = charger_fonction('trouver_table','base');
      $desc=$trouver_table($type, $boucle->sql_serveur);
      $table = $desc['table'];
    }
		/*Ajouter ici un test et produire une erreur si table non trouvee*/
		$ids = $desc['key']['PRIMARY KEY'];
		foreach(split(',', $ids) as $_id)
			if(trim($_id) != $primary) $id = $_id;
		$boucle->select[]= 'COUNT('.$frequence.'.'.$id.') AS '.$frequence;
		$boucle->from[$frequence] = $table;
		$boucle->where[] = array("'='", "'$id_table'", "'$frequence.$primary'");
		$boucle->group[] = $id_table;
		$boucle->having[] = array("'$op'", "'$frequence'", $val);		
	}
}

function balise_FREQUENCE_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	// s'il n'y a pas de nom de boucle, on ne peut pas frequencer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#FREQUENCE')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}
	// s'il n'y a pas de modificateur de frequence, c'est qu'on
	// a oublie le critere {frequence}
	if (!$p->boucles[$b]->modificateur['frequence']) {
		erreur_squelette(
			_L('#FREQUENCE sans crit&egrave;re {frequence}'), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	//Un seul critere pour l'instant
	$p->code = '$Pile[$SP][\''.$p->boucles[$b]->modificateur['frequence'].'\']';
	return $p;
}

function balise_MAX_FREQUENCE_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	// s'il n'y a pas de nom de boucle, on ne peut pas frequencer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#MAX_FREQUENCE')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	$p->code = '$max_frequence' . $p->id_boucle;
	$p->interdire_scripts = false;
	return $p;
}

?>