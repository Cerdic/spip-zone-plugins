<?php
/**
 * critères pour les boucles des squelettes
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * {offres_en_cours}
 * {offres_en_cours #ENV{date}}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_offres_en_cours_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	$_dateref = emplois_calculer_date_reference($idb, $boucles, $crit);
	$_date_debut = "$id_table.date_debut";
	$_date_fin = "$id_table.date_fin";

	// si on ne sait pas si les heures comptent, on utilise toute la journee.
	// sinon, on s'appuie sur le champ 'illimite=oui'
	// pour savoir si les dates utilisent les heures ou pas.	
	$where =
		array("'AND'",
			array("'<='", "'$_date_debut'", "sql_quote(date('Y-m-d H:i:59'))"),
			array("'>='", "'$_date_fin'", "sql_quote(date('Y-m-d H:i:00'))")
		);

	if ($crit->not)
		$where = array("'NOT'",$where);
	$boucle->where[] = $where;
}

/**
 * Fonction privee pour mutualiser de code des criteres_publicite*
 * Retourne le code php pour obtenir la date de reference de comparaison
 * des offres a trouver 
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 * 
 * @return string code PHP concernant la date.
**/
function emplois_calculer_date_reference($idb, &$boucles, $crit) {
	if (isset($crit->param[0]))
		return calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		return "date('Y-m-d H:i:00')";
}


?>
