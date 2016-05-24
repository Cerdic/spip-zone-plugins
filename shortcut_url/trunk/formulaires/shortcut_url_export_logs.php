<?php
/**
 * Gestion du formulaire d'export des shortcut_url des sites
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @license    GNU/GPL
 * @package    SPIP\formulaires\shortcut_url_export_logs
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_shortcut_url_export_logs_charger_dist() {
	$valeurs = array();
	return $valeurs;
}

/**
 * Vérifications du formulaire de shortcut_url
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_shortcut_url_export_logs_verifier_dist() {
	$erreurs = array();
	return $erreurs;
}

/**
 * Traitement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Retours du traitement
 */
function formulaires_shortcut_url_export_logs_traiter_dist() {
	include_spip('inc/exporter_csv');
	$donnees = array(array(' ', ' ', ' ', ' ', ' '), array(_T('shortcut_url:csv_nb_click'), _T('shortcut_url:csv_id'), _T('shortcut_url:csv_shortcut'), _T('shortcut_url:csv_url'), _T('shortcut_url:csv_description')));
	$date = _request('annee').'-'._request('mois');
	$req = sql_select(
		'DISTINCT urls.id_shortcut_url, shortcut.titre, shortcut.description, shortcut.url',
		'spip_shortcut_urls_logs as urls LEFT join spip_shortcut_urls as shortcut on urls.id_shortcut_url = shortcut.id_shortcut_url',
		'DATE(urls.date_modif) like "' . $date . '%" and urls.humain="oui"'
	);
	foreach ($req as $valeur) {
		$count_shortcut_url =  sql_countsel(
			'spip_shortcut_urls_logs',
			'id_shortcut_url=' . intval($valeur['id_shortcut_url']). ' AND DATE(date_modif) like "' . $date . '%" and humain="oui"'
		);
		$valeur = array_merge(array('nb_clicks' => $count_shortcut_url), $valeur);
		$donnees[] = $valeur;
	}

	$donnees = array_sort_clicks($donnees, 'nb_clicks', SORT_DESC);
	$exporter_csv = charger_fonction('exporter_csv', 'inc');
	$date_jour = date('Y-m-d_H-i');
	$nom_fichier_csv = 'shortcut_urls_logs_'.$date.'_-_'.$date_jour;

	$exporter_csv(
		$nom_fichier_csv,
		$donnees,
		',',
		array(_T('shortcut_url:titre_csv_export', array('date' => $date, 'date_jour' => $date_jour)))
	);

	return array('editable' => false, 'message_ok'=>_T('shortcut_url:config_export_ok'));
}

/**
 * Fonction de tri d'un arrau
 *
 * @param array $array
 *        Le tableau à trier
 * @param string $on
 *        Le champ sur lequel trier
 * @param define $order
 *        Dans quel ordre trier
 * @return array Le tableau trié
 */
function array_sort_clicks($array, $on = 'nb_clicks', $order = SORT_ASC) {
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}
