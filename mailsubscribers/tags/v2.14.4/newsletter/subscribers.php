<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/mailsubscribers');
include_spip('mailsubscribers_fonctions');

/**
 * Renvoi les inscrits a une ou plusieurs listes
 * si plusieurs listes sont demandee, c'est un OU qui s'applique (renvoie les inscrits a au moins une des listes)
 *
 * @param array $listes
 *   listes de diffusion. 'newsletter' si non precise
 * @param array $options
 *   count : si true renvoyer le nombre de resultats au lieu de la liste (perf issue, permet de tronconner)
 *   limit : ne recuperer qu'un sous ensemble des inscrits "10,20" pour recuperer 20 resultats a partir du 10e (idem SQL)
 * @return int|array
 *   liste d'utilisateurs, chacun decrit par un array dans le meme format que newsletter/subscriber
 */
function newsletter_subscribers_dist($listes = array(), $options = array()) {
	static $count = null;

	$select = "S.email,S.nom,'' as listes,S.lang,'on' AS status,S.jeton,S.id_mailsubscriber";
	$where = array();
	$limit = "";

	// si pas de liste precisee : liste newsletter par defaut (newsletter::newsletter)
	if (!$listes OR !is_array($listes)) {
		$listes = array(mailsubscribers_normaliser_nom_liste());
	}

	// si simple comptage d'une seule liste, faisons avec la fonction mailsubscribers_compte_inscrits
	// qui compte chaque liste en un seul coup et memoize
	if (isset($options['count']) AND $options['count'] AND count($listes) == 1) {
		$id_segment = 0;
		$l = explode('+',reset($listes));
		$liste = array_shift($l);
		$liste = mailsubscribers_normaliser_nom_liste($liste);
		if ($l) $id_segment = intval(array_shift($l));
		return mailsubscribers_compte_inscrits($liste, 'valide', $id_segment);
	}

	$w = array();
	foreach ($listes as $l){
		$id_segment = 0;
		$l = explode('+',$l);
		$identifiant = mailsubscribers_normaliser_nom_liste(array_shift($l));
		if ($l) $id_segment = intval(reset($l));
		if ($id_mailsubscribinglist = sql_getfetsel('id_mailsubscribinglist','spip_mailsubscribinglists','identifiant='.sql_quote($identifiant))){
			$w[] = "(L.id_mailsubscribinglist=".intval($id_mailsubscribinglist).' AND L.id_segment='.intval($id_segment).')';
		}
	}
	if (!$w) {
		$w = '0=1';
	}
	else {
		$w = '('.implode(' OR ',$w).')';
	}

	$where[] = $w;
	$where[] = 'L.statut=' . sql_quote('valide');
	$from = "spip_mailsubscribers as S JOIN spip_mailsubscriptions AS L ON (L.id_mailsubscriber = S.id_mailsubscriber)";

	// si simple comptage de plusieurs listes, on arrive ici
	if (isset($options['count']) AND $options['count']) {
		return sql_countsel($from, $where,'S.id_mailsubscriber');
	}

	if (isset($options['limit']) AND $options['limit']) {
		$limit = $options['limit'];
	}

	// selection, par date
	// ca permet ainsi que les derniers inscrits (en cours de diffusion) se retrouvent dans le dernier lot
	// et premier inscrits, premiers servis
	$rows = sql_allfetsel($select, $from, $where, 'S.id_mailsubscriber', "S.date", $limit);
	$rows = array_map('mailsubscribers_informe_subscriber', $rows);

	return $rows;
}
