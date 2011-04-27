<?php
function changestatut_autoriser(){}

function autoriser_changestatut_dist($faire, $type, $id, $qui, $opt) {
	// si on est ou etait admin
	return (($qui['webmestre'] == 'oui') OR ($qui['statut_orig'] == 'webmestre'));
}
// autorisation des boutons
function autoriser_versadmin21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_versredacteur21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
function autoriser_verswebmestre21_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('changestatut', $type, $id, $qui, $opt);
}
?>