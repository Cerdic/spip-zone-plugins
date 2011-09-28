<?php

// declarer la fonction du pipeline
function bilancontrib_autoriser(){}

/**
 * Autorisation a afficher le bilan
 */
function autoriser_bilancontrib_afficher_dist($faire,$quoi,$id,$qui,$opts){
	return ($qui['webmestre'] == 'oui') ;
}

function autoriser_bilancontrib21_bouton_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser('afficher','bilancontrib',$id,$qui,$options);
}
function autoriser_bilancontrib_bouton_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser('afficher','bilancontrib',$id,$qui,$options);
}

?>
