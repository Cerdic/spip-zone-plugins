<?php
/**
 * Fonctions utiles au plugin Disponibilites objets
 *
 * @plugin     Disponibilites objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_disponibilites\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Les fonctions de dates_outils.
include_spip('filtres/dates_outils');
include_spip('filtres/inc_agenda_filtres');

// Les critères de dates_outils.
include_spip('criteres/inc_agenda_filtres');
include_spip('criteres/public_agenda');

/**
 * Calcule les dates disponibles d'un objet donnée.
 *
 * @param array $options
 *        	Des options passées surchargeant le contexte.
 * @param mixed $contexte
 *          Les variables du contexte
 * @return array
 *          Les dates disponibles.
 */
function dates_disponibles($options, $contexte = array()) {

	if (!is_array($contexte)) {
		$contexte = unserialize($contexte);
	}
	$contexte = array_merge($contexte, $options);
	/*
	 * Les indisponibles
	 */
	// Les dates considérés comme utilisées
	if (isset($contexte['utilisation_squelette']) and
		$chemin = $contexte['utilisation_squelette'] and
		find_in_path($chemin . '.html')) {
		$dates_utilisees = unserialize(recuperer_fond($chemin, $contexte));
	}
	else {
		$dates_utilisees = array();
	}

	// Les dates de l'objet encodés comme indisponibles
	$dates_indisponibles = unserialize(recuperer_fond('disponibilites/indisponibles', $contexte));

	/*
	 * Les disponibles
	 */
	$dates_disponibles = unserialize(recuperer_fond('disponibilites/disponibles', $contexte));

	/*
	 * Le décompte, les disponibles moins les indisponibles
	 */
	return array_diff($dates_disponibles, $dates_indisponibles, $dates_utilisees);
}
