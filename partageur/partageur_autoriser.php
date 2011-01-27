<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function partageur_autoriser(){}


// declarations d'autorisations
function autoriser_partageur_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo');
}



function autoriser_partageur_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
	    case 'voir':
			case 'editer': 
			case 'modifier': 
			case 'importer':
				return ($qui['statut'] == '0minirezo'); 
				break;
			default:
				return false;
				break;
		}  
                               
   return false; 
}




?>