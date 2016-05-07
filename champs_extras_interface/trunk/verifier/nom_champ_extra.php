<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifié une valeur comme devant être un nom de champ extra
 * 
 * Ce champ ne doit pas être utilisé par SPIP ou un plugin,
 * et ne doit pas être un mot clé de mysql.
 * 
 * Si c'est bon, doit aussi vérifier une expression régulière donnée
 * 
 * Options :
 * - modele : chaine représentant l'expression régulière tolérée
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   Contient une chaine représentant l'expression.
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_nom_champ_extra_dist($valeur, $options=array()){

	$erreur = '';
	// tester l'expression
	if (!$erreur) {
		$verifier = charger_fonction('verifier', 'inc');
		$options += array('modele' => '/^[\w]+$/');
		$erreur = $verifier($valeur, 'regex', $options);
	}

	return $erreur;
}


