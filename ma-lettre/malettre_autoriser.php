<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

//cf http://programmer.spip.org/Definir-des-boutons

// fonction pour le pipeline, n'a rien a effectuer
function malettre_autoriser(){}

// declarations d'autorisations
// voir lettre ? = tous les admins
/*function autoriser_malettre_voir_dist($faire, $type, $id, $qui, $opt) {
    return ($qui['statut'] == '0minirezo');
}*/

// declarations d'autorisations
	function autoriser_malettre_bouton_dist($faire, $type, $id, $qui, $opt) {
	        return autoriser('voir', 'malettre', $id, $qui, $opt);
	}
	
	function autoriser_malettre_voir_dist($faire, $type, $id, $qui, $opt) {
	        return true;
	}

?>
