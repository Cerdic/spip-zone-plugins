<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Exporter la base au format CSV
 *
 * @param null|string $args
 */
function action_mailsubscribers_export_dist($args = null){
	if (is_null($args)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$args = $securiser_action();
	}

	if (!autoriser('exporter','_mailsubscribers')){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$args = explode("~~", $args);
	$statut = $args[0];
	$liste = (isset($args[1])) ? trim($args[1]) : false;

	$where = array();
	// '' ou 'all' pour tout exporter (sauf poubelle)
	if (in_array($statut,array('','all'))) {
		$where[] = "statut<>".sql_quote('poubelle');
	} else {
		$where[] = "statut=".sql_quote($statut);
	}

	if ($liste) {
		//$where[] = "listes LIKE '%".$liste."%'";
		$where = array();
		$where[] = "listes LIKE '%".$liste.",%' OR listes LIKE '%".$liste."' AND statut='valide'";
	}

	$entetes = array(
		'email',
		'nom',
		'lang',
		'date',
		'statut',
		'listes',
	);

	$titre = _T('mailsubscriber:titre_mailsubscribers')."-".$GLOBALS['meta']['nom_site']."-".date('Y-m-d');
	$exporter_csv = charger_fonction("exporter_csv","inc");
	$res = sql_select("email,nom,lang,date,statut,listes","spip_mailsubscribers",$where);
	$exporter_csv($titre, $res, ',', $entetes);

}
