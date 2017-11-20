<?php
/**
 * Plugin mailsubscribers
 * (c) 2017 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Exporter la base au format CSV
 *
 * @param null|string $arg
 */
function action_mailsubscribers_export_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (!autoriser('exporter', '_mailsubscribers')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$args = explode("-", $arg);
	$statut = $args[0];
	$id_liste = isset($args[1]) ? intval($args[1]) : false;

	$where = array();
	// '' ou 'all' pour tout exporter (sauf poubelle)
	if (in_array($statut, array('', 'all'))) {
		$where[] = 'M.statut<>' . sql_quote('poubelle');
	} else {
		$where[] = 'M.statut=' . sql_quote($statut);
	}

	$entetes = array(
		'email',
		'nom',
		'lang',
		'date',
		'statut',
		'listes',
	);

	$exporter_csv = charger_fonction('exporter_csv', 'inc');
	$listes = sql_get_select('group_concat(L.identifiant)','spip_mailsubscriptions as S JOIN spip_mailsubscribinglists as L ON L.id_mailsubscribinglist=S.id_mailsubscribinglist','S.id_segment=0 AND S.id_mailsubscriber=M.id_mailsubscriber');
	// si un id_liste est present, restreindre l'export à cette liste
	if ($id_liste) {
		$identifiant = sql_getfetsel('identifiant', 'spip_mailsubscribinglists', 'id_mailsubscribinglist	=' . intval($id_liste));
		$titre = _T('mailsubscriber:titre_mailsubscribers') . "-" . $GLOBALS['meta']['nom_site'] . "-" . $identifiant . "-" . date('Y-m-d');
		$where[] = "N.id_mailsubscribinglist=$id_liste";
		$res = sql_select(
			"M.email,M.nom,M.lang,M.date,M.statut,($listes) as listes",
			"spip_mailsubscribers AS M LEFT JOIN spip_mailsubscriptions as N ON M.id_mailsubscriber=N.id_mailsubscriber",
			$where
		);
	} else {
		$titre = _T('mailsubscriber:titre_mailsubscribers') . "-" . $GLOBALS['meta']['nom_site'] . "-" . date('Y-m-d');
		$res = sql_select(
			"M.email,M.nom,M.lang,M.date,M.statut,($listes) as listes",
			"spip_mailsubscribers AS M",
			$where
		);
	}
	$exporter_csv($titre, $res, ',', $entetes);

}
