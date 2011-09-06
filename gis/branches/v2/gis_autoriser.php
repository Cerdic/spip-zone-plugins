<?php 

function gis_autoriser(){};

/**
 * Autorisation a modifier le logo d'un point
 * 
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_iconifier_dist($faire,$quoi,$id,$qui,$opts){
	return (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

?>