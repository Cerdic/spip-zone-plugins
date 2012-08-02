<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function socialtags_autoriser(){}

// Affichage du bouton de menu pour Spip 2.1
function autoriser_socialtags2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}
// Affichage du bouton de menu pour Spip 3
function autoriser_socialtags_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}

// Autorisation de configuration pour Spip 2
function autoriser_socialtags_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}
// Pour Spip 3 : pas de "s" final à socialtag, car il est supprime par objet_type() (ecrire/inc/objets.php)
// juste avant la recherche de la fonction d'autorisation
function autoriser_socialtag_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}

?>
