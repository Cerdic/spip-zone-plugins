<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function accordeon_autoriser(){}

// Affichage du bouton de menu pour Spip 2.1
function autoriser_accordeon2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
// Affichage du bouton de menu pour Spip 3
function autoriser_accordeon_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
function autoriser_accordeon_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>