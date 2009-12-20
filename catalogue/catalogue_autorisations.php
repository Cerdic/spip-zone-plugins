<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
// fonction pour le pipeline, n'a rien a effectuer
function catalogue_autoriser(){}
// declarations d'autorisations
function autoriser_catalogue_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'catalogue', $id, $qui, $opt);
}
function autoriser_catalogue_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}
?>