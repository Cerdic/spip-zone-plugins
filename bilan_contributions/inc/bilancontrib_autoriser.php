<?php

// declarer la fonction du pipeline
function bilancontrib_autoriser(){}

/**
 * Autorisation a voir les evenements prives
 */
function autoriser_bilancontrib_afficher_dist($faire,$quoi,$id,$qui,$opts){
	return ($qui['statut'] == '0minirezo')
}


?>
