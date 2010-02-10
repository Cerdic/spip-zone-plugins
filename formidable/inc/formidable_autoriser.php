<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Juste pour l'appel du pipeline
function formidable_autoriser(){}

// Seuls les admins peuvent éditer les formulaires
function autoriser_formulaires_bouton_dist($faire, $type, $id, $qui, $options){
	if (isset($qui['statut']) and $qui['statut'] <= '0minirezo') return true;
	else return false;
}

?>
