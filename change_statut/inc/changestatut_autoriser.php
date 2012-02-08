<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function changestatut_autoriser(){}

function autoriser_changestatut_dist($faire, $type, $id, $qui, $opt) {
	// si on est ou etait webmestre
	return (($qui['webmestre'] == 'oui') OR ($qui['statut_orig'] == 'webmestre'));
}
// autorisation des boutons pour Spip 2.1
function autoriser_versadmin21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_versredacteur21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_verswebmestre21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
// autorisation des boutons pour Spip 3
function autoriser_versadmin21_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_versredacteur21_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_verswebmestre21_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
?>