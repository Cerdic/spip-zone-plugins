<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
// fonction pour le pipeline, n'a rien a effectuer
function greve_autoriser(){}
// declarations d'autorisations
function autoriser_greves_bouton_dist($faire, $type, $id, $qui, $opt) {
        return autoriser('edit', 'greves', $id, $qui, $opt);
}
function autoriser_greves2_bouton_dist($faire, $type, $id, $qui, $opt) {
        return autoriser('edit', 'greves', $id, $qui, $opt);
}
function autoriser_greves_edit_dist($faire, $type, $id, $qui, $opt) {
        if ($qui['webmestre'] == 'oui')
        	return true;
        else
        	return false;
}
?>
