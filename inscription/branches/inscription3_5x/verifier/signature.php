<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation d'une signature (nom d'inscription)
 *
 * @param string $valeur 
 * 		Le login testé
 * @param array $options [optional]
 * 		Le tableau des options
 * @return false|string 
 * 		Retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 */
function verifier_signature_dist($valeur,$options=array()) {
	if(!$valeur)
		return false;
	else{
		// Vérifier si le nom est déjà utilisé
		if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($options['id_auteur'])."' AND nom = '$valeur'"))
			return _T('inscription3:erreur_signature_deja_utilise');
	}
	return;
}

?>