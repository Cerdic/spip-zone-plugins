<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// fonction pour le pipeline, n'a rien a effectuer
function signature_autoriser(){}

// declarations d'autorisations

	
function autoriser_signature_signature_telecharger_dist($faire, $type, $id, $qui, $opt) {
	        return true;    // tout le monde
}

?>