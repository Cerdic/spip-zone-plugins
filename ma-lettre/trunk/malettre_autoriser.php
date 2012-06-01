<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// fonction pour le pipeline, n'a rien a effectuer
function malettre_autoriser(){}

// declarations d'autorisations
function autoriser_malettre_bouton_dist($faire, $type, $id, $qui, $opt) {
	        return autoriser('voir', 'malettre', $id, $qui, $opt);        
}
	
function autoriser_malettre_voir_dist($faire, $type, $id, $qui, $opt) {
	        return (in_array($qui['statut'],array('0minirezo')));    // pour l'instant uniquement les admins
}

?>