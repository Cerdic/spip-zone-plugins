<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function feuillederoute_autoriser(){}

// declarations d'autorisations d'edition
function autoriser_feuillederoute_editer($faire, $type, $id, $qui, $opt) {
	// commenter/decommenter ce qui est necessaire pour autoriser ou non a modifier le texte de la feuille de route
// 	if( in_array($qui['statut'], array('0minirezo', '1comite','6forum')))	// webmestres + admins + redacteurs
	if ($qui['statut'] == '0minirezo')	// webmestres + admins
// 	if ($qui['webmestre']=='oui')	// que les webmestres
		return true;
}

?>