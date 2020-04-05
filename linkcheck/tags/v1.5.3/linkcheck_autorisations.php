<?php
/**
 * Plugin LinkCheck
 * (c) 2013 Benjamin Grapeloux, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function linkcheck_autoriser() {
}

// bouton de menu
function autoriser_linkcheck_menu_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('voir', 'linkchecks');
}

// voir la page des liens
function autoriser_voir_linkchecks_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_reinitialiser_linkcheck_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
