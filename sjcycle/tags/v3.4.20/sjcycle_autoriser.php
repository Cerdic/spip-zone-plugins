<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function sjcycle_autoriser(){}

// Affichage du bouton de menu pour Spip 3
function autoriser_sjcycle_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
function autoriser_sjcycle_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}