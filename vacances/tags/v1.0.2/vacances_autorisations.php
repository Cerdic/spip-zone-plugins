<?php
/**
 * Définit les autorisations du plugin Vacances
 *
 * @plugin     Vacances
 * @copyright  2017
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Vacances\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function vacances_autoriser() {
}


// bouton de menu
function autoriser_vacances_menu_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('voir', 'vacances');
}

// voir la page des liens
function autoriser_voir_vacances_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}
