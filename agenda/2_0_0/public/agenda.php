<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 *
 */


/**
 * <BOUCLE(EVENEMENTS)>
 *
 * @param <type> $id_boucle
 * @param <type> $boucles
 * @return <type>
 */
function boucle_EVENEMENTS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	if (!isset($boucle->modificateur['criteres']['statut'])) {
		// Restreindre aux elements publies
		// uniquement les evenements d'un article publie
		if (!$GLOBALS['var_preview'])
			if (!isset($boucle->modificateur['lien']) AND !isset($boucle->modificateur['tout'])
			AND (!isset($boucle->lien) OR !$boucle->lien) AND (!isset($boucle->tout) OR !$boucle->tout)) {
				$boucle->from["articles"] =  "spip_articles";
				$boucle->where[]= array("'='", "'articles.id_article'", "'$id_table.id_article'");
				$boucle->where[]= array("'='", "'articles.statut'", "'\"publie\"'");
			}
	}
	return calculer_boucle($id_boucle, $boucles);
}


/**
 * {agendafull ..} variante etendue du crietre agenda du core
 * qui accepte une date de debut et une date de fin
 *
 * {agendafull date_debut, date_fin, jour, #ENV{annee}, #ENV{mois}, #ENV{jour}}
 * {agendafull date_debut, date_fin, semaine, #ENV{annee}, #ENV{mois}, #ENV{jour}}
 * {agendafull date_debut, date_fin, mois, #ENV{annee}, #ENV{mois}}
 * {agendafull date_debut, date_fin, periode, #ENV{annee}, #ENV{mois}, #ENV{jour},
 *                                            #ENV{annee_fin}, #ENV{mois_fin}, #ENV{jour_fin}}
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_agendafull_dist($idb, &$boucles, $crit)
{
	$params = $crit->param;

	if (count($params) < 1)
	      erreur_squelette(_T('zbug_info_erreur_squelette'),
			       "{agenda ?} BOUCLE$idb");

	$parent = $boucles[$idb]->id_parent;

	// les valeurs $date et $type doivent etre connus a la compilation
	// autrement dit ne pas etre des champs

	$date_deb = array_shift($params);
	$date_deb = $date_deb[0]->texte;

	$date_fin = array_shift($params);
	$date_fin = $date_fin[0]->texte;

	$type = array_shift($params);
	$type = $type[0]->texte;

	$annee = $params ? array_shift($params) : "";
	$annee = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois =  $params ? array_shift($params) : "";
	$mois = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour =  $params ? array_shift($params) : "";
	$jour = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($jour, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$annee2 = $params ? array_shift($params) : "";
	$annee2 = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee2, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois2 =  $params ? array_shift($params) : "";
	$mois2 = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois2, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour2 =  $params ? array_shift($params) : "";
	$jour2 = "\n" .  'sprintf("%02d", ($x = ' .
		calculer_liste($jour2, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$boucle = &$boucles[$idb];
	$date = $boucle->id_table . ".$date";

	if ($type == 'jour')
		$boucle->where[]= array("'AND'", 
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",("$annee . $mois . $jour")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",("$annee . $mois . $jour")));
	elseif ($type == 'mois')
		$boucle->where[]= array("'AND'", 
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m\')'",("$annee . $mois")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m\')'",("$annee . $mois")));
	elseif ($type == 'semaine')
		$boucle->where[]= array("'AND'", 
					array("'>='",
					     "'DATE_FORMAT($date_fin, \'%Y%m%d\')'", 
					      ("date_debut_semaine($annee, $mois, $jour)")),
					array("'<='",
					      "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",
					      ("date_fin_semaine($annee, $mois, $jour)")));
	elseif (count($crit->param) > 3)
		$boucle->where[]= array("'AND'",
					array("'>='",
					      "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",
					      ("$annee . $mois . $jour")),
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'", ("$annee2 . $mois2 . $jour2")));
	// sinon on prend tout
}



/**
 * fonction sous jacente pour les 3 criteres
 * fusion_par_jour, fusion_par_mois, fusion_par_annee
 * 
 * @param string $format
 * @param strinf $as
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function agenda_critere_fusion_par_xx($format, $as, $idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$_date = isset($crit->param[0]) ? calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent)
	  : "'".(isset($GLOBALS['table_date'][$type])?$GLOBALS['table_date'][$type]:"date")."'";

	$date = $boucle->id_table. '.' .substr($_date,1,-1);

	// annuler une eventuelle fusion sur cle primaire !
	foreach($boucles[$idb]->group as $k=>$g)
		if ($g==$boucle->id_table.'.'.$boucle->primary)
			unset($boucles[$idb]->group[$k]);
	$boucles[$idb]->group[]  = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format')";
	$boucles[$idb]->select[] = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format') AS $as";
}

/**
 * {fusion_par_jour date_debut}
 * {fusion_par_jour date_fin}
 * 
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_fusion_par_jour_dist($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y-%m-%d','jour',$idb, $boucles, $crit);
}

/**
 * {fusion_par_mois date_debut}
 * {fusion_par_mois date_fin}
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_fusion_par_mois_dist($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y-%m','mois',$idb, $boucles, $crit);
}

/**
 * {fusion_par_annee date_debut}
 * {fusion_par_annee date_fin}
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_fusion_par_annee_dist($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y','annee',$idb, $boucles, $crit);
}

/**
 * {evenement_a_venir}
 * {evenement_a_venir #ENV{date}}
 * 
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_evenement_a_venir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	if (isset($crit->param[0]))
		$_dateref = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$_dateref = "date('Y-m-d H:i:00')";

	$_date = "$id_table.date_debut";
	$op = $crit->not ? "<=":">";
	$boucle->where[] = array("'$op'","'$_date'","sql_quote($_dateref)");
}


/**
 * {evenement_passe}
 * {evenement_passe #ENV{date}}
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_evenement_passe_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	if (isset($crit->param[0]))
		$_dateref = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$_dateref = "date('Y-m-d H:i:00')";

	$_date = "$id_table.date_fin";
	$op = $crit->not ? ">=":"<";
	$boucle->where[] = array("'$op'","'$_date'","sql_quote($_dateref)");
}

/**
 * {evenement_en_cours}
 * {evenement_en_cours #ENV{date}}
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_evenement_en_cours_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	if (isset($crit->param[0]))
		$_dateref = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$_dateref = "date('Y-m-d H:i:00')";

	$where = array("'<='","'date_debut'","sql_quote($_dateref)");
	$where = array("'AND'",$where,array("'>='","'date_fin'","sql_quote($_dateref)"));
	if ($crit->not)
		$where = array("'NOT'",$where);
	$boucle->where[] = $where;
}


?>