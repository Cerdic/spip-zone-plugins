<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_autoriser(){}

function autoriser_menu_modifier_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo' and !$qui['restreint'])
		return true;
	else
		return false;
}

function autoriser_menus_tous_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'menu', $id, $qui, $opt);
}

?>
