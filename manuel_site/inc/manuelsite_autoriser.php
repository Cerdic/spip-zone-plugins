<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function manuelsite_autoriser(){}

// Affichage du bouton de menu pour Spip 2.1
function autoriser_manuelsite2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
// Affichage du bouton de menu pour Spip 3
function autoriser_manuelsite_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
// Affichage de la page de config pour Spip 2 et 3
function autoriser_manuelsite_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>