<?php
/**
 * shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\formulaires\shortcut_url_export_logs
 */

/**
 * Gestion du formulaire d'export des shortcut_url des sites 
 *
 * @package SPIP\Formulaires
**/
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_shortcut_url_export_logs_charger_dist(){

	$valeurs = array();
	return $valeurs;
	
}

/**
 * Vérifications du formulaire de shortcut_url
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_shortcut_url_export_logs_verifier_dist(){

	$erreurs = array();
	return $erreurs;

}

/**
 * Traitement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_shortcut_url_export_logs_traiter_dist(){

	include_spip('inc/exporter_csv');
	$donnees = '';
	$date = _request('annee').'-'._request('mois');
	$req = sql_select('DISTINCT id_shortcut_url', 'spip_shortcut_urls_logs', 'DATE(date_modif) like "' . $date . '%" and humain="oui"');
	foreach ($req as $cle => $valeur) {
		$id_shortcut_url = sql_select('id_shortcut_url, description, url', 'spip_shortcut_urls', 'id_shortcut_url=' . intval($valeur['id_shortcut_url']));
		foreach ($id_shortcut_url as $c => $v) {
			$count_shortcut_url =  sql_countsel('spip_shortcut_urls_logs', 'id_shortcut_url=' . intval($v['id_shortcut_url']));
			$donnees .= $count_shortcut_url . ',';
			$donnees .= exporter_csv_ligne($v);
		}
	}

	$date_jour = date('Y-m-d_H-i');
	$nom_fichier_csv = 'shortcut_urls_logs_'.$date_jour.'.csv';

	header('Content-Type: text/csv; charset=utf-8');
	header("Content-Disposition: attachment; filename=$nom_fichier_csv");
	header("Content-Length: ".strlen($donnees));
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo _T('shortcut_url:titre_csv_export', array('date' => $date, 'date_jour' => $date_jour)) . "\r\n";
	echo "nb click,id,description,url\r\n";
	echo $donnees;

	return array('editable' => false, 'message_ok'=>_T('shortcut_url:config_export_ok'));

}