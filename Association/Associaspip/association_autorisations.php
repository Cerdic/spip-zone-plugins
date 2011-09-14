<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function association_autoriser(){}

// autorisation d'editer des membres (ou de creer un membre depuis un auteur spip)
function autoriser_associer_adherents_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo' && !$qui['restreint']); // on retourne ok pour tous les admins non restreints
}
?>
