<?php

/**
 * Déclaration des tâches du génie
 *
 * @plugin SVP pour SPIP
 * @license GPL
 * @package SPIP\Maintenancekit\Genie
 */
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Recalculer tous les rubriques suite à une effacement ou un déplacement de masse
 *
 *
 * @param array $taches_generales
 *     Tableau des tâches et leur périodicité en seconde
 * @return array
 *     Tableau des tâches et leur périodicité en seconde
 */
function genie_maintenancekit_recalculer_status_rubriques_dist($taches_generales) {

	if ($rubriques = sql_select('id_rubrique', 'spip_rubriques')) {
		include_spip("inc/rubriques");

		while ($rubrique = sql_fetch($rubriques)) {
			$id_rubrique = $rubrique['id_rubrique'];
			depublier_branche_rubrique_if($id_rubrique);
		}
	}

	spip_log("recalcul des status des touts les rubriques","maintenancekit");
	return $taches_generales;
}
