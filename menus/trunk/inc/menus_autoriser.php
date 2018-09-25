<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function menus_autoriser() {
}

function autoriser_menu_modifier_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo' and !$qui['restreint']) {
		return true;
	} else {
		return false;
	}
}

function autoriser_menus_tous_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'menu', $id, $qui, $opt);
}


/**
 * Autorisation de lier/délier l'élément (menus)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associermenus_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}