<?php
/**
 * Plugin critere suivant precedent
 * Licence GPL - 2010 - Matthieu Marcillaud 
 * 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


function critere_suivant_dist($idb, &$boucles, $crit) {
	calculer_critere_suivant_precedent_dist($idb, $boucles, $crit, 'suivant');
}

function critere_precedent_dist($idb, &$boucles, $crit) {
	calculer_critere_suivant_precedent_dist($idb, $boucles, $crit, 'precedent');
}

/**
 * Calcul des critères {suivant} et {precedent}.
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
#
# A GARDER ? je sais pas à quoi ça sert...
#
	$t = $boucle->id_table . '.' . $primary;
	if ($boucle->primary
		AND !preg_match('/[,\s]/',$primary)
		AND !in_array($t, $boucle->select))
	  $boucle->select[]= $t;

	  
}


// $trouver : suivant / precedent / (toute autre valeur retourne la position courante de l'id demande.
function quete_position_primary($primary, $valeur, $trouver, $res, $serveur=''){
	// on ne devrait pas arriver ici si la cle primaire est inexistante
	// ou composee, mais verifions
	if (!$primary OR preg_match('/[,\s]/',$primary))
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
