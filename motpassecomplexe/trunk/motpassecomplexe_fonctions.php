<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Verification de la validite d'un mot de passe pour le mode d'auth concerne
 * c'est ici que se font eventuellement les verifications de longueur mini/maxi
 * ou de force.
 *
 * @param string $new_pass
 * @return string
 *  message d'erreur si login non valide, chaine vide sinon
 */
function motpassecomplexe_verifier_pass($new_pass){

	 // charge les constantes
	// define('_PASS_LONGUEUR_MINI', '6');											// longueur minimale - defaut: 6
	if (!defined('_MOTCOMPLEXE_MINUSCULE')) define('_MOTCOMPLEXE_MINUSCULE', 1);	// nb de minuscules  - defaut: 1
	if (!defined('_MOTCOMPLEXE_MAJUSCULE')) define('_MOTCOMPLEXE_MAJUSCULE', 1);	// nb de majuscules  - defaut: 1
	if (!defined('_MOTCOMPLEXE_CHIFFRE')) define('_MOTCOMPLEXE_CHIFFRE', 1);		// nb de chiffres  - defaut: 1
	if (!defined('_MOTCOMPLEXE_SPECIAL')) define('_MOTCOMPLEXE_SPECIAL', 1);		// nb de caractères spéciaux  - defaut: 1

	$requis = array (
		'nb' => _PASS_LONGUEUR_MINI,
		'nb_min' => _MOTCOMPLEXE_MINUSCULE,
		'nb_maj' => _MOTCOMPLEXE_MAJUSCULE,
		'nb_int' => _MOTCOMPLEXE_CHIFFRE,
		'nb_spe' => _MOTCOMPLEXE_SPECIAL,
	);

	$nb = strlen($new_pass);
	$nb_min = motpassecomplexe_count_pattern($new_pass, '![^a-z]+!');
	$nb_maj = motpassecomplexe_count_pattern($new_pass, '![^A-Z]+!');
	$nb_int = motpassecomplexe_count_pattern($new_pass, '![^0-9]+!');
	$nb_spe = $nb - motpassecomplexe_count_pattern($new_pass, '![^A-z0-9 ]+!');

	$verifications = array(
		array (
			'constante' => _PASS_LONGUEUR_MINI,
			'operateur' => '>=',
			'variable' =>  $nb,
		),
		array (
			'constante' => _MOTCOMPLEXE_MINUSCULE,
			'operateur' => '>=',
			'variable' => $nb_min,
		),
		array (
			'constante' => _MOTCOMPLEXE_MAJUSCULE,
			'operateur' => '>=',
			'variable' => $nb_maj,
		),
		array (
			'constante' => _MOTCOMPLEXE_CHIFFRE,
			'operateur' => '>=',
			'variable' => $nb_int,
		),
		array (
			'constante' => _MOTCOMPLEXE_SPECIAL,
			'operateur' => '>=',
			'variable' => $nb_spe,
		),
	);


	foreach ($verifications as $verification) {
		if (!version_compare($verification['variable'], $verification['constante'], $verification['operateur'])) {
			return _T('motpassecomplexe:info_passe_trop_court', $requis);
		}
	}

	return '';
}

/**
 * Compte le nombre de caractères d'une chaine vérifiant une regex 
 *
 * @param string $login
 *	chaine à verifier
 * @param string $pattern
 * 	motif regex à tester
 * @return int
 *	la longueur
 */
function motpassecomplexe_count_pattern($str, $pattern) {
	return strlen(preg_replace($pattern, '', $str));
}