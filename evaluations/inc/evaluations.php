<?php
/**
 * Fonctions d'aides et outils pour les évaluations
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Trouve l'id_evaluation à partir d'un identifiant numérique ou textuel
 *
 * @param int|string $id_ou_identifiant id_evaluation ou identifiant
 * @return int id_evaluation
**/
function evaluations_obtenir_identifiant($id_ou_identifiant) {
	static $faits = array();

	// déjà trouvé
	if (isset($faits[$id_ou_identifiant])) {
		return $faits[$id_ou_identifiant];
	}

	// vide
	if (!intval($id_ou_identifiant) and !strlen($id_ou_identifiant)) {
		return $faits[$id_ou_identifiant] = 0;
	}

	// identifiant texte
	if (!intval($id_ou_identifiant) and $id_ou_identifiant) {
		$id_evaluation = sql_getfetsel('id_evaluation', 'spip_evaluations', 'identifiant='. sql_quote($id_ou_identifiant));
		return $faits[$id_ou_identifiant] = (int) $id_evaluation;
	}

	// identifiant numérique = id_evaluation
	return $faits[$id_ou_identifiant] = (int) $id_ou_identifiant;

}
