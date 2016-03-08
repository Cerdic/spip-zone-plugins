<?php
/**
 * Enregistrer la popularite en base.
 *
 * Surcharge du plugin Statistiques de la dist pour prendre en compte tous les objets éditoriaux.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Administrations
 */

/**
 * Gestion du calcul des popularités (cron)
 *
 * @plugin Statistiques pour SPIP
 * @license GNU/GPL
 * @package SPIP\Statistiques\Genie
 **/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Calcule des coefficients de popularité en fonction de l'intervalle
 * écoulé depuis le précédent calcul
 *
 * Popularite, modele logarithmique
 *
 * @param int $duree Intervalle écoulé depuis le précédent calcul
 * @return array {
 * @type float $a Coefficient d'amortissement
 * @type float $b Constante multiplicative
 * }
 **/
function genie_popularite_constantes($duree) {
	// duree de demi-vie d'une visite dans le calcul de la popularite (en jours)
	$demivie = 0.5;
	// periode de reference en jours
	$periode = 1;
	// $a est le coefficient d'amortissement depuis la derniere mesure
	$a = pow(2, -$duree / ($demivie * 24 * 3600));
	// $b est la constante multiplicative permettant d'avoir
	// une visite par jour (periode de reference) = un point de popularite
	// (en regime stationnaire)
	// or, magie des maths, ca vaut log(2) * duree journee/demi-vie
	// si la demi-vie n'est pas trop proche de la seconde ;)
	$b = log(2) * $periode / $demivie;

	return array($a, $b);
}

/**
 * Cron de calcul des popularités des articles
 *
 * @uses genie_popularite_constantes()
 *
 * @param int $t
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif si la tâche a été terminée, négatif pour réexécuter cette tâche
 **/
function genie_popularites_dist($t) {

	include_spip('inc/config');
	include_spip('inc/base');

	// Si c'est le premier appel, ne pas calculer
	$t = $GLOBALS['meta']['date_popularites'];
	ecrire_meta('date_popularites', time());

	if (!$t) {
		return 1;
	}

	$duree = time() - $t;
	list($a, $b) = genie_popularite_constantes($duree);

	if ($tables_objets = lire_config('activer_statistiques_objets')) {
		$max = array();
		$tot = array();
		foreach($tables_objets as $table_objet){
			// du passe, faisons table (SQL) rase
			sql_update($table_objet, array('maj' => 'maj', 'popularite' => "popularite * $a"), 'popularite>1');
			// données des metas
			$row = sql_fetsel('MAX(popularite) AS max, SUM(popularite) AS tot', $table_objet);
			$max[] = $row['max'];
			$tot[] = $row['tot'];
		}
	}

	// enregistrer les metas...
	ecrire_meta("popularite_max", max($max));
	ecrire_meta("popularite_total", array_sum($tot));


	// Une fois par jour purger les referers du jour ; qui deviennent
	// donc ceux de la veille ; au passage on stocke une date_statistiques
	// dans spip_meta - cela permet au code d'etre "reentrant", ie ce cron
	// peut etre appele par deux bases SPIP ne partageant pas le meme
	// _DIR_TMP, sans tout casser...

	$aujourdhui = date("Y-m-d");
	if (($d = $GLOBALS['meta']['date_statistiques']) != $aujourdhui) {
		spip_log("Popularite: purger referer depuis $d");
		ecrire_meta('date_statistiques', $aujourdhui);
		if (strncmp($GLOBALS['connexions'][0]['type'], 'sqlite', 6) == 0) {
			spip_query("UPDATE spip_referers SET visites_veille=visites_jour, visites_jour=0");
		} else
			// version 3 fois plus rapide, mais en 2 requetes
			#spip_query("ALTER TABLE spip_referers CHANGE visites_jour visites_veille INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',CHANGE visites_veille visites_jour INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
			#spip_query("UPDATE spip_referers SET visites_jour=0");
			// version 4 fois plus rapide que la premiere, en une seule requete
			// ATTENTION : peut poser probleme cf https://core.spip.net/issues/2505
		{
			sql_alter("TABLE spip_referers DROP visites_veille,
			CHANGE visites_jour visites_veille INT(10) UNSIGNED NOT NULL DEFAULT '0',
			ADD visites_jour INT(10) UNSIGNED NOT NULL DEFAULT '0'");
		}
	}

	// et c'est fini pour cette fois-ci
	return 1;

}
