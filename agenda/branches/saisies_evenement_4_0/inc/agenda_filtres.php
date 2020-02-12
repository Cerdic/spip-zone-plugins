<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Filtres&criteres deprecies/obsoletes
 */


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
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 * @deprecated
 */
function critere_agendafull_dist($idb, &$boucles, $crit) {
	$params = $crit->param;

	if (count($params) < 1) {
		erreur_squelette(_T('zbug_info_erreur_squelette'), "{agenda ?} BOUCLE$idb");
	}

	$parent = $boucles[$idb]->id_parent;

	// les valeurs $date et $type doivent etre connus a la compilation
	// autrement dit ne pas etre des champs

	$date_deb = array_shift($params);
	$date_deb = $date_deb[0]->texte;

	$date_fin = array_shift($params);
	$date_fin = $date_fin[0]->texte;

	$type = array_shift($params);
	$type = $type[0]->texte;

	$annee = $params ? array_shift($params) : '';
	$annee = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois =  $params ? array_shift($params) : '';
	$mois = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour =  $params ? array_shift($params) : '';
	$jour = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($jour, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$annee2 = $params ? array_shift($params) : '';
	$annee2 = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee2, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois2 =  $params ? array_shift($params) : '';
	$mois2 = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois2, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour2 =  $params ? array_shift($params) : '';
	$jour2 = "\n" .  'sprintf("%02d", ($x = ' .
		calculer_liste($jour2, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$boucle = &$boucles[$idb];

	$quote_end = ",'".$boucle->sql_serveur."','text'";

	if ($type == 'jour') {
		$boucle->where[]= array("'AND'",
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",("sql_quote($annee . $mois . $jour$quote_end)")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",("sql_quote($annee . $mois . $jour$quote_end)")));
	} elseif ($type == 'mois') {
		$boucle->where[]= array("'AND'",
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m\')'",("sql_quote($annee . $mois$quote_end)")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m\')'",("sql_quote($annee . $mois$quote_end)")));
	} elseif ($type == 'semaine') {
		$boucle->where[]= array("'AND'",
					array("'>='",
					     "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",
					      ("date_debut_semaine($annee, $mois, $jour)")),
					array("'<='",
					      "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",
					      ("date_fin_semaine($annee, $mois, $jour)")));
	} elseif (count($crit->param) > 3) {
		$boucle->where[]= array("'AND'",
					array("'>='",
					      "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",
					      ("sql_quote($annee . $mois . $jour$quote_end)")),
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'", ("sql_quote($annee2 . $mois2 . $jour2$quote_end)")));
	// sinon on prend tout
	}
}


/**
 * Afficher de facon textuelle les dates de debut et fin en fonction des cas
 * - Le lundi 20 fevrier a 18h
 * - Le 20 fevrier de 18h a 20h
 * - Du 20 au 23 fevrier
 * - du 20 fevrier au 30 mars
 * - du 20 fevrier 2007 au 30 mars 2008
 *
 * @param string $date_debut : la date de début au format mysql
 * @param string $date_fin : la date de fin au format mysql
 * @param string $horaire : oui / non, permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * @param string $forme : forme que prendra la date :
 * 		- annee (afficher systématiquement l'année pour la date finale, ne pas faire appelle à affdate_jourcourt)
 * 		- abbr (afficher le nom des jours en abbrege)
 * 		- hcal (generer une date au format hcal)
 * 		- h-event (generer une date au format h-event, dans une balise <time> HTML5)
 * @return string
 * @deprecated 
 */
function agenda_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme = '') {
	$s = affdate_debut_fin($date_debut, $date_fin, $horaire, $forme);
	return unicode2charset(charset2unicode($s, 'AUTO'));
}
