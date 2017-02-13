<?php
/**
 * Ajouter un article mis à jour aux alertes des abonnés
 *
 * @plugin     Alertes
 * @copyright  2016-2017
 * @author     Teddy
 * @licence    GNU/GPL
 * @package    SPIP/Alertes/Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_ajouter_alerte_article_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode('-', $arg);

	if (is_array($arg) and count($arg) == 3) {
		list($id_article, $id_rubrique, $id_secteur) = $arg;
		include_spip('base/abstract_sql');
		if ($id_rubrique) {
			$rubriques_abo = sql_allfetsel('id_auteur', 'spip_alertes',
				"objet='rubrique' AND id_objet=" . $id_rubrique);
			if (is_array($rubriques_abo) and count($rubriques_abo) > 0) {
				foreach ($rubriques_abo as $auteur) {
					sql_insertq('spip_alertes_cron', array(
						'id_auteur' => $auteur['id_auteur'],
						'id_objet' => $id_article,
						'objet' => 'article',
						'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
					));
				}
			}
		}
		if ($id_secteur) {
			$secteurs_abo = sql_allfetsel('id_auteur', 'spip_alertes', "objet='secteur' AND id_objet=" . $id_secteur);
			if (is_array($secteurs_abo) and count($secteurs_abo) > 0) {
				foreach ($secteurs_abo as $auteur) {
					sql_insertq('spip_alertes_cron', array(
						'id_auteur' => $auteur['id_auteur'],
						'id_objet' => $id_article,
						'objet' => 'article',
						'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
					));
				}
			}
		}
	}
}