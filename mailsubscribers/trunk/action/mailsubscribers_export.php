<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Exporter la base au format CSV
 *
 * @param null|string $statut
 */
function action_mailsubscribers_export_dist($statut = null) {
	if (is_null($statut)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$statut = $securiser_action();
	}

	if (!autoriser('exporter', '_mailsubscribers')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$where = array();
	// '' ou 'all' pour tout exporter (sauf poubelle)
	if (in_array($statut, array('', 'all'))) {
		$where[] = "statut<>" . sql_quote('poubelle');
	} else {
		$where[] = "statut=" . sql_quote($statut);
	}


	$entetes = array(
		'email',
		'nom',
		'lang',
		'date',
		'statut',
		'listes',
	);

	$titre = _T('mailsubscriber:titre_mailsubscribers') . "-" . $GLOBALS['meta']['nom_site'] . "-" . date('Y-m-d');
	$exporter_csv = charger_fonction("exporter_csv", "inc");
	$listes = sql_get_select('group_concat(L.identifiant)','spip_mailsubscriptions as S JOIN spip_mailsubscribinglists as L ON L.id_mailsubscribinglist=S.id_mailsubscribinglist','S.id_segment=0 AND S.id_mailsubscriber=M.id_mailsubscriber');
	$res = sql_select("M.email,M.nom,M.lang,M.date,M.statut,($listes) as listes", "spip_mailsubscribers AS M", $where);
	$exporter_csv($titre, $res, ',', $entetes);

}
