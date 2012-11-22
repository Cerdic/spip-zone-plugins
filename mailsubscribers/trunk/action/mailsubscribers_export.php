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
function action_mailsubscribers_export_dist($statut=null){
	if (is_null($statut)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$statut = $securiser_action();
	}

	if (!autoriser('exporter','_mailsuscribers')){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$where = array();
	// '' ou 'all' pour tout exporter (sauf poubelle)
	if (in_array($statut,array('','all')))
		$where[] = "statut<>".sql_quote('poubelle');
	else
		$where[] = "statut=".sql_quote($statut);


	$entetes = array(
		_T('mailsubscriber:label_email'),
		_T('mailsubscriber:label_nom'),
		_T('mailsubscriber:label_lang'),
		_T('public:date'),
		_T('mailsubscriber:label_statut'),
		_T('mailsubscriber:label_listes'),
	);

	$titre = _T('mailsubscriber:titre_mailsubscribers')."-".$GLOBALS['meta']['nom_site']."-".date('Y-m-d');
	$exporter_csv = charger_fonction("exporter_csv","inc");
	$res = sql_select("email,nom,lang,date,statut,listes","spip_mailsubscribers",$where);
	$exporter_csv($titre, $res, ',', $entetes);

}