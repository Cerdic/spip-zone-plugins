<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function forumsectorise_autoriser(){}

// Affichage du bouton de menu pour Spip 2.1
function autoriser_forumsectorise2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
// Affichage du bouton de menu pour Spip 3
function autoriser_forumsectorise_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
function autoriser_forumsectorise_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>