<?php
/**
 * Ce fichier contient les autorisations nécessaire à la gestion de l'archivage des objets.
 *
 * @package SPIP\ARCHOBJET\AUTORISATIONS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
/**
 * Fonction vide pour le pipeline homonyme.
 */
function archobjet_autoriser() {
}

/**
 * Autorisation de base pour l'archivage d'un type de contenu.
 * - l'objet doit être archivable
 * - et l'utilisateur doit être un administrateur même restreint.
 *
 * @param string $faire   Action demandée
 * @param string $type    Type d'objet sur lequel appliquer l'action
 * @param int    $id      Identifiant de l'objet
 * @param array  $qui     Description de l'auteur demandant l'autorisation
 * @param array  $options Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
**/
function autoriser_archivage_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	if ($type) {
		// Lecture des tables autorisées à l'archivage
		include_spip('inc/config');
		$tables_autorisees = lire_config('archobjet/objets_archivables', array());

		// Rechercher
		include_spip('base/objets');
		$table = table_objet_sql($type);

		// Si l'objet est archivable et que l'utilisateur est un administrateur non restreint on autorise
		// l'archivage.
		if (
			in_array($table, $tables_autorisees)
			and ($qui['statut'] == '0minirezo')
		) {
			$autoriser = true;
		}
	}

	return $autoriser;
}

/**
 * Autorisation d'archiver l'objet concerné.
 * - l'archivage doit être autorisé sur le type d'objet
 * - et l'objet non déjà archivé.
 *
 * @param string $faire   Action demandée
 * @param string $type    Type d'objet sur lequel appliquer l'action
 * @param int    $id      Identifiant de l'objet
 * @param array  $qui     Description de l'auteur demandant l'autorisation
 * @param array  $options Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
**/
function autoriser_objetmodifierarchivage_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	if (
		autoriser('archivage', $type)
		and $type
		and ($id_objet = intval($id))
		and ($action = $options['action'])
	) {
		// Vérification de l'état d'archivage
		include_spip('inc/archobjet_objet');
		$etat_archivage = objet_etat_archivage(
			$type,
			$id_objet
		);
		if (
			(
				($action == 'archiver')
				and !$etat_archivage['est_archive']
			)
			or (
				($action == 'desarchiver')
				and $etat_archivage['est_archive']
				)
			) {
			$autoriser = true;
		}
	}

	return $autoriser;
}
