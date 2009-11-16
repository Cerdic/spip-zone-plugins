<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Verifie une valeur suivant une expression reguliere.
 * Options :
 * - modele : chaine representant l'expression
 */
function verifier_regex_dist($valeur, $options=array()){
	if (preg_match($options['modele'], $valeur))
		return '';
	else
		return _T('verifier:erreur_regex');
}

?>
