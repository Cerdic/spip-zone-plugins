<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation d'un code postal
 *
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $cp Le code postal testé
 * @param int $id_auteur[optional]
 */
function verifier_codepostal_dist($valeur,$options=array()) {
	if(!$valeur){
		return false;
	}
	else{
		if(preg_match('/^[A-Z]{1,2}[-|\s][0-9]{3,6}$|^[0-9]{3,6}$|^[0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}$|^[A-Z]{1,2} [0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}/i',$valeur)){
			return false;
		}
		else{
			return _T('inscription3:erreur_cp_valide');
		}
	}
}

?>