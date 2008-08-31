<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
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
?>