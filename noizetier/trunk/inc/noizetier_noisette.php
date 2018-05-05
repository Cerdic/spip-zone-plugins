<?php
/**
 * Ce fichier contient l'API complémentaire spécifique au noiZetier de gestion des noisettes.
 *
 * @package SPIP\NOIZETIER\NOISETTE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Réordonne les noisettes d'un bloc d'une page ou d'un objet à partir d'un index donné du tableau.
 * L'ordre est renvoyé pour l'ensemble des noisettes du bloc.
 * Si l'index à partir duquel les noisettes sont réordonnées n'est pas fourni ou est égal à 0
 * la fonction réordonne toutes les noisettes.
 *
 * @api
 *
 * @param array	$ordre
 * @param int	$index_initial
 *
 * @return bool
 */
function noizetier_noisette_ordonner($ordre, $index_initial = 0) {

	if ($index_initial < count($ordre)) {
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}

		// On modifie le rang de chaque noisette en suivant l'ordre du tableau à partir de l'index
		// initial.
		foreach ($ordre as $_cle => $_id_noisette) {
			if ($_cle >= $index_initial) {
				$modification = array('rang_noisette' => $_cle + 1);
				$where = array('id_noisette=' . intval($_id_noisette));
				sql_updateq('spip_noisettes', $modification, $where);
			}
		}

		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}

	return true;
}
