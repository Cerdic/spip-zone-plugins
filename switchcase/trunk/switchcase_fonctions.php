<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * @param string $switch
 * @param array ...$cases  tableau suite des cas testés et valeurs renvoyées
 * @return mixed            la valeur correspondant au switch reçu
 *
 * Filtre |switchcase : comme |? mais pour plus de 2 valeurs
 * La valeur par défaut doit être spécifiée en dernier par 'defaut', 'default' ou 'case_default'
 *          [(#TRUC|switchcase{
 * 			    banane,jaune,
 * 			    orange,orange,
 * 			    ciel,bleu,
 * 			    case_default,inconnue
 * 				})]
 * Ou bien : [(#TRUC|switchcase{
 * 			    banane,jaune,
 * 			    orange,orange,
 * 			    ciel,bleu,
 * 			    inconnue
 * 				})]
 */
function switchcase($switch, ... $cases) {
	$last_case = $case = $val = '';
	$default_sans_case = (count($cases) % 2);

	while ($case = array_shift($cases)) {
		$val = array_shift ($cases);
		if ($switch == $case) {
			return $val;
		}
		$last_case = $case;
	}
	// dernier cas : case_default, <default_value>
	if ($last_case == 'case_default') {
		return $val;
	}
	// pas de value : case est la <default_value>
	if ($default_sans_case) {
		return $last_case;
	}
	return '';
}
