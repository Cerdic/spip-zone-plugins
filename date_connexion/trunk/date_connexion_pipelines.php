<?php
/**
 * Utilisations de pipelines par Date de connexion
 *
 * @plugin     Date de connexion
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_connexion\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des champs de date
 * @param array $tables
 * @return array
 */
function date_connexion_declarer_tables_objets_sql($tables) {
	$tables['spip_auteurs']['field']['date_connexion'] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
	$tables['spip_auteurs']['field']['date_connexion_precedente'] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
	$tables['spip_auteurs']['field']['date_suivi_activite'] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
	return $tables;
}

/**
 * Mise à jour des dates lors de la connexion de l’auteur
 *
 * La date de suivi d’activité prend la date de la pénultième connexion,
 * sauf si la date de suivi d’activité est déjà plus récente.
 *
 * @param array $flux
 * @return array
 */
function date_connexion_preparer_visiteur_session($flux) {
	if (!empty($flux['args']['row']['id_auteur'])) {
		spip_log(debug_backtrace(), 'mm.3');
		$id_auteur = $flux['args']['row']['id_auteur'];
		$date_connexion = date('Y-m-d H:i:s');
		$date_connexion_precedente = $flux['args']['row']['date_connexion'];
		$date_suivi_activite = $flux['args']['row']['date_suivi_activite'];
		if ($date_suivi_activite < $date_connexion_precedente) {
			$date_suivi_activite = $date_connexion_precedente;
		}
		sql_updateq(
			'spip_auteurs',
			array(
				'date_connexion' => $date_connexion,
				'date_connexion_precedente' => $date_connexion_precedente,
				'date_suivi_activite' => $date_suivi_activite,
			),
			array(
				'id_auteur = ' . sql_quote($id_auteur)
			)
		);
		$flux['args']['row']['date_connexion'] = $flux['data']['date_connexion'] = $date_connexion;
		$flux['args']['row']['date_connexion_precedente'] = $flux['data']['date_connexion_precedente'] = $date_connexion_precedente;
		$flux['args']['row']['date_suivi_activite'] = $flux['data']['date_suivi_activite'] = $date_suivi_activite;
	}
	return $flux;
}