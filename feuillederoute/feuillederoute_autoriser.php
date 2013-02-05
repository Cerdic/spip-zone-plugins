<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function feuillederoute_autoriser(){}

// declarations d'autorisations
function autoriser_feuillederoute_editer($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo')	// tous les admins
// 	if ($qui['webmestre']=='oui')	// que le webmestre
		return true;
}
?>