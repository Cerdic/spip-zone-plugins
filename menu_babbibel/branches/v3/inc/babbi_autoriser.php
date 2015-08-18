<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function babbi_autoriser(){}

// Affichage du bouton de menu pour Spip 2.1
function autoriser_babbi2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
// Affichage du bouton de menu pour Spip 3
function autoriser_babbi_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

// Autorisation de configuration pour Spip 2
function autoriser_babbi_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>