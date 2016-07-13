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

	$select = "email,nom,'' as listes,lang,'on' AS status,jeton,id_mailsubscriber";
	$where = array();
	$limit = "";

	// si pas de liste precisee : liste newsletter par defaut (newsletter::newsletter)
	if (!$listes OR !is_array($listes)) {
		$listes = array(mailsubscribers_normaliser_nom_liste());
	}

	// si simple comptage d'une seule liste, faisons avec la fonction mailsubscribers_compte_inscrits
	// qui compte chaque liste en un seul coup et memoize
	if (isset($options['count']) AND $options['count'] AND count($listes) == 1) {
		$liste = mailsubscribers_normaliser_nom_liste(reset($listes));

		return mailsubscribers_compte_inscrits($liste);
	}


	$identifiants = array_map('mailsubscribers_normaliser_nom_liste', $listes);
	$ids = sql_allfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists', sql_in('identifiant', $identifiants));
	$ids = array_map('reset', $ids);

	$sous_where = sql_get_select('id_mailsubscriber', 'spip_mailsubscriptions',
		'statut=' . sql_quote('valide') . ' AND ' . sql_in('id_mailsubscribinglist', $ids));
	$sous_where = "(SELECT * FROM ($sous_where) AS S)";
	$where[] = "id_mailsubscriber IN $sous_where";

	// si simple comptage de plusieurs listes, on arrive ici
	if (isset($options['count']) AND $options['count']) {
		return sql_countsel("spip_mailsubscribers", $where);
	}

	if (isset($options['limit']) AND $options['limit']) {
		$limit = $options['limit'];
	}

	// selection, par date
	// ca permet ainsi que les derniers inscrits (en cours de diffusion) se retrouvent dans le dernier lot
	// et premier inscrits, premiers servis
	$rows = sql_allfetsel($select, "spip_mailsubscribers", $where, "", "date", $limit);
	$rows = array_map('mailsubscribers_informe_subscriber', $rows);

	return $rows;
}
