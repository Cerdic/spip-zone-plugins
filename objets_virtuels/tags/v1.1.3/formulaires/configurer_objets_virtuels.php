<?php
/**
 * Utilisations de pipelines par Objets virtuels
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Installation
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de configuration des objets virtuels
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_objets_virtuels_charger_dist() {
	include_spip('objets_virtuels_fonctions');
	$valeurs = [];
	$valeurs['objets_virtuels'] = objets_virtuels_tables_actives();
	return $valeurs;
}

/**
 * Traitement du formulaire de configuration des objets virtuels
 *
 * @return array
 *     Retours du traitement
 **/
function formulaires_configurer_objets_virtuels_traiter_dist() {
	$res = array('editable' => true);
	$tables = _request('objets_virtuels');
	$tables = is_array($tables) ? array_filter($tables) : [];
	$err = [];

	// création du champ 'virtuel' dans les tables sélectionnées
	foreach ($tables as $key => $table) {
		$desc = sql_showtable($table);
		if (empty($desc['field']['virtuel'])) {
			sql_alter('TABLE '. $table . ' ADD virtuel text DEFAULT \'\' NOT NULL');
			// vérification de la présence du champ.
			$desc = sql_showtable($table);
			if (empty($desc['field']['virtuel'])) {
				unset($tables[$key]);
				$err[] = $table;
			}
		}
	}

	// création / mise à jour de la méta
	ecrire_config('objets_virtuels', implode(',', $tables));
	if (!in_array('spip_articles', $tables)) {
		ecrire_config('articles_redirection', 'non');
	} else {
		ecrire_config('articles_redirection', 'oui');
	}

	if ($err) {
		$res['message_error'] = _T('objets_virtuels:erreur_creation_champ_virtuel_dans_tables', ['tables' => implode(', ', $err)]);
	} else {
		$res['message_ok'] = _T('config_info_enregistree');
	}
	return $res;
}
