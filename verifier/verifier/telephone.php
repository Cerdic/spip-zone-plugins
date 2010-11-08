<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifie un numéro de téléphone. Pour l'instant seulement avec le schéma français.
 *
 * @param string $valeur La valeur à vérifier.
 * @param array $option [INUTILISE].
 * @return string Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_telephone_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_telephone');
	$ok = '';

	switch($options['pays']){
		case 'FR':
		default:
			// On accepte differentes notations, les points, les tirets, les espaces, les slashes
			$tel = preg_replace("#\.|/|-| #i",'',$valeur);

			// On interdit les 000 etc. mais je pense qu'on peut faire plus malin
			// TODO finaliser les numéros à la con
			if($tel == '0000000000') return $erreur;

			if(!preg_match("/^(0|\+33)[0-9]{9}$/",$tel)) return $erreur;
			break;
	}
	
	return $ok;
}
