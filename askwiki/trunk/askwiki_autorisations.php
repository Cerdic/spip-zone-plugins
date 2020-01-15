<?php
/**
 * Définit les autorisations du plugin Askwiki
 *
 * @plugin     Askwiki
 * @copyright  2020
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Askwiki\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function askwiki_autoriser() {
}

// Seul admin a acces aux modifications/enregistrement
function autoriser_askwiki_modifier_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo');
}

// affichage bouton
function autoriser_askwiki_bt_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'askwiki');
}
