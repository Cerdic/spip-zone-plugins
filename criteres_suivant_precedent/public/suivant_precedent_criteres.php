<?php
/**
 * Plugin critere suivant precedent
 * Licence GPL - 2010 - Matthieu Marcillaud 
 * 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

// tester la presence des iterateurs...
if (version_compare($GLOBALS['spip_version_branche'], '2.3', '>=')
	or defined('_DIR_PLUGIN_ITERATEURS')) {
		define('avec_iterateur', true);
} else {
		define('avec_iterateur', false);
}


/**
 * Fournit le critère de boucle {suivant}
 * permettant de trouver l'élément suivant par rapport à la boucle parente 
 *
**/
function critere_suivant_dist($idb, &$boucles, $crit) {
	if (avec_iterateur) {
		calculer_critere_iterateur_suivant_precedent_dist($idb, $boucles, $crit, 'suivant');
	} else {
		calculer_critere_suivant_precedent_dist($idb, $boucles, $crit, 'suivant');
	}
}

/**
 * Fournit le critère de boucle {precedent}
 * permettant de trouver l'élément précédent par rapport à la boucle parente 
 *
**/
function critere_precedent_dist($idb, &$boucles, $crit) {
	if (avec_iterateur) {
		calculer_critere_iterateur_suivant_precedent_dist($idb, $boucles, $crit, 'precedent');
	} else {
		calculer_critere_suivant_precedent_dist($idb, $boucles, $crit, 'precedent');
	}
}



/**
 * Calcul des critères {suivant} et {precedent}.
 * On reprend en grande partie le fonctionnement de {pagination} avec debut_xx=@yy
 * en jouant de sql_seek pour se déplacer à la bonne position sur les résultats de
 * la boucle.
 *
 * @param string $type	type de décalage : 'suivant' ou 'precedent'
**/
function calculer_critere_iterateur_suivant_precedent_dist($idb, &$boucles, $crit, $type) {
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;
	
	$arg = kwote(calculer_argument_precedent($idb, $primary, $boucles));

	$partie =
		 "0;\n"
		."\tif (\$id_actuel = $arg) {\n"
		."\t\t".'$debut_boucle = quete_iterateur_position_primary(\''.$primary.'\', $id_actuel, "'.$type.'", $iter);'."\n"
		// pas de resultat, on cree une selection vide
		."\t\t".'if ($debut_boucle === false) {'."\n"
			//."\t\t\t".'include_spip("public/iterateur");'
			."\t\t\t".'$iter = IterFactory::create("EmptyIterator", array());' . "\n"
		."\t\t} else {\n"
			// si on a des resultats, il faut remettre le compteur a zero.
			."\t\t\t".'$iter->seek(0);' . "\n"

			// donner un bon GRAND_TOTAL pour la fonction calculer_parties()
			// NAN ça marche pas non plus... mah que passa ?
			//."\t\t\t"."\$Numrows['$idb']['total'] = (\$Numrows['$idb']['total'] >= 1 ? 2 : 0);\n"
		."\t\t}\n"
		."\t}\n"
		."\t".'$debut_boucle = intval($debut_boucle)';


	$boucle->total_parties = 1;
	calculer_parties($boucles, $idb, $partie, '++');
	
	// ajouter la cle primaire dans le select
	// sauf si pas de primaire, ou si primaire composee
	// dans ce cas, on ne sait pas gerer une pagination indirecte
	// : il faut id_xx dans le select pour la fonction quete_position_primary()
	$t = $boucle->id_table . '.' . $primary;
	if ($boucle->primary
		AND !preg_match('/[,\s]/',$primary)
		AND !in_array($t, $boucle->select)) {
	  $boucle->select[]= $t;
	}

	// forcer le compilo à ne pas prendre en static a cause du $where fluctuant
	$boucle->where[]= array("'='","'1'","sql_quote(1)");
	  
}



// $trouver : suivant / precedent / (toute autre valeur retourne la position courante de l'id demande.
function quete_iterateur_position_primary($primary, $valeur, $trouver, $iter){
	// on ne devrait pas arriver ici si la cle primaire est inexistante
	// ou composee, mais verifions
	if (!$primary OR preg_match('/[,\s]/', $primary))
		return false;

	$pos = 0;
	while ($row = $iter->fetch() AND $row[$primary]!=$valeur){
		$pos++;
	}

	// si on a pas trouve
	if ($row[$primary]!=$valeur)
		return false;

	// precedent : prendre la position moins 1
	if ($trouver == 'precedent') {
		if ($pos) {
			return ($pos - 1);
		}
		return false;
	}
	
	// suivant : tester l'existence d'un suivant
	if ($trouver == 'suivant') {
		if ($row = $iter->fetch()) {
			return ($pos + 1);
		}
		return false;
	}
	
	// sinon, retourner la position de la ligne contenant l'enregistrement demande
	return $pos;
}







/* ==================================================
 *
 * 		Ancienne ecriture (avant les iterateurs)
 * 
 */



/**
 * Calcul des critères {suivant} et {precedent} (avant l'existence d'Iterateurs).
 * On reprend en grande partie le fonctionnement de {pagination} avec debut_xx=@yy
 * en jouant de sql_seek pour se déplacer à la bonne position sur les résultats de
 * la boucle.
 *
 * @param string $type	type de décalage : 'suivant' ou 'precedent'
**/
function calculer_critere_suivant_precedent_dist($idb, &$boucles, $crit, $type) {
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;
	
	$arg = kwote(calculer_argument_precedent($idb, $primary, $boucles));

	$partie =
		 "0;\n"
		."\tif (\$id_actuel = $arg) {\n"
		."\t\t".'$debut_boucle = quete_position_primary(\''.$primary.'\', $id_actuel, "'.$type.'", $result, '._q($boucle->sql_serveur).');'."\n"
		// pas de resultat, on cree une selection vide
		."\t\t".'if ($debut_boucle === false){'."\n"
			."\t\t\t".'@sql_free($result,'._q($boucle->sql_serveur).");\n"
			."\t\t\t"."\$where[] = array('=','0','1');\n" // forcer 0 resultat
			."\t\t\t".'$result = calculer_select($select, $from, $type, $where, $join, $groupby, $orderby, $limit, $having, $table, $id, $connect);'."\n"
			//."\t\t\t"."\$Numrows['$idb']['total'] = 0;\n"
		."\t\t} else {\n"
			// si on a des resultats, il faut remettre le compteur a zero.
			."\t\t".'if (!sql_seek($result,0,'._q($boucle->sql_serveur).")){\n"
			."\t\t\t".'@sql_free($result,'._q($boucle->sql_serveur).");\n"
			."\t\t\t".'$result = calculer_select($select, $from, $type, $where, $join, $groupby, $orderby, $limit, $having, $table, $id, $connect);'."\n"
			."\t\t}\n"
			// donner un bon GRAND_TOTAL pour la fonction calculer_parties()
			// NAN ça marche pas non plus... mah que passa ?
			//."\t\t\t"."\$Numrows['$idb']['total'] = (\$Numrows['$idb']['total'] >= 1 ? 2 : 0);\n"
		."\t\t}\n"
		."\t}\n"
		."\t".'$debut_boucle = intval($debut_boucle)';


	$boucle->total_parties = 1;
	calculer_parties($boucles, $idb, $partie, '++');
	
	// ajouter la cle primaire dans le select
	// sauf si pas de primaire, ou si primaire composee
	// dans ce cas, on ne sait pas gerer une pagination indirecte
	// : il faut id_xx dans le select pour la fonction quete_position_primary()
	$t = $boucle->id_table . '.' . $primary;
	if ($boucle->primary
		AND !preg_match('/[,\s]/',$primary)
		AND !in_array($t, $boucle->select)) {
	  $boucle->select[]= $t;
	}

	// forcer le compilo à ne pas prendre en static a cause du $where fluctuant
	$boucle->where[]= array("'='","'1'","sql_quote(1)");
	  
}


// $trouver : suivant / precedent / (toute autre valeur retourne la position courante de l'id demande.
function quete_position_primary($primary, $valeur, $trouver, $res, $serveur=''){
	// on ne devrait pas arriver ici si la cle primaire est inexistante
	// ou composee, mais verifions
	if (!$primary OR preg_match('/[,\s]/', $primary))
		return false;

	$pos = 0;
	while ($row = sql_fetch($res, $serveur) AND $row[$primary]!=$valeur){
		$pos++;
	}

	// si on a pas trouve
	if ($row[$primary]!=$valeur)
		return false;

	// precedent : prendre la position moins 1
	if ($trouver == 'precedent') {
		if ($pos) {
			return ($pos - 1);
		}
		return false;
	}
	
	// suivant : tester l'existence d'un suivant
	if ($trouver == 'suivant') {
		if ($row = sql_fetch($res, $serveur)) {
			return ($pos + 1);
		}
		return false;
	}
	
	// sinon, retourner la position de la ligne contenant l'enregistrement demande
	return $pos;
}
?>
