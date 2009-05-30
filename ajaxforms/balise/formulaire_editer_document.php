<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_FORMULAIRE_EDITER_DOCUMENT_dist ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_EDITER_DOCUMENT', array('id_document'));
}

function balise_FORMULAIRE_EDITER_DOCUMENT_stat($args,$filtres) {
	// si on force les parametres par #FORMULAIRE_EDITER_DOCUMENT{12}
	// on enleve les parametres calcules
	if (isset($args[2])) {
		array_shift($args);
	}
	$id_objet = $args[0];
	return array($id_objet);
}

?>
