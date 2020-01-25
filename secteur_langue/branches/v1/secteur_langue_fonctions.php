<?php
/**
 * Fonctions utiles au plugin Secteur par langue
 *
 * @plugin     Secteur par langue
 * @copyright  2019 - 2020
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Secteur_langue\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Détermine l'id_parent de la nouvelle rubrique traduite
 *
 * @param string $lang
 *   Langue de destination
 * @param string $id_trad
 *   L'id de traduction
 * @param bool $parent
 *   Si true, on cherche le parent de la rubrique.
 * @param bool $creer_racine
 *   si true, il faut créer la racine
 *
 * @return array
 *   un tablea avec Langue de destination, L'id de traduction et le bool creer_racine
  */
function rubrique_destination_traduction($lang, $id_trad, $parent = TRUE, $creer_racine = '') {
	$id_trad_parent = '';
	$trads = [];
	if ($lang AND $id_trad) {

		$select = 'id_rubrique';
		if ($parent) {
			$select = 'id_parent';
		}

		// on établit l'id_parent
		$id_trad_parent = sql_getfetsel($select, 'spip_rubriques', 'id_rubrique=' . $id_trad);

		//puis sa traduction
		if ($id_trad_parent) {
			$id_parent_trad = sql_getfetsel('id_trad', 'spip_rubriques', 'id_rubrique=' . $id_trad_parent);
		}

		if ($id_trad_parent == 0) {
			$trads = [
				'id_parent_trad' => 0,
				'id_trad' => $id_trad,
				'creer_racine' => $creer_racine
			];
		}
		// S'il il existe une traduction parente dans la langue demandé on retourne son id
		elseif ($id_parent_trad) {
			if ($rub = sql_fetsel(
					'id_rubrique,id_trad',
					'spip_rubriques',
					'id_trad=' . $id_parent_trad . ' AND lang=' . sql_quote($lang))) {
				$trads = [
					'id_parent_trad' => $rub['id_rubrique'],
					'id_trad' => $id_trad,
					'creer_racine' => $creer_racine
				];
			}
			else {
				$id_trad = sql_getfetsel('id_trad', 'spip_rubriques', 'id_trad=' . $id_parent_trad);
				$trads = rubrique_destination_traduction($lang, $id_trad, TRUE, 'oui');
			}
		}
		elseif ($id_trad_parent) {
			$trads = rubrique_destination_traduction($lang, $id_trad_parent, TRUE, 'oui');
		}
		else
			$trads = [
				'id_parent_trad' => 0,
				'id_trad' => $id_trad,
				'creer_racine' => ''
			];
		return $trads;

	}

	return $trads;
}
