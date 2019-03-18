<?php
/**
 * API de vérification : vérification de la validité d'un numéro de téléphone
 *
 * @plugin     verifier
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie un numéro de téléphone. Pour l'instant seulement avec le schéma français.
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   pays
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_telephone_dist($valeur, $options = array()) {
	$erreur = _T('verifier:erreur_telephone');
	if (!is_string($valeur)) {
		return $erreur;
	}
	$ok = '';

	// On accepte differentes notations, les points, les tirets, les espaces, les slashes
	$tel = preg_replace('#\.|/|-| #i', '', $valeur);

	// Pour les prefixes, on accepte les notations +33 et 0033
	// si on trouve un indicatif de pays, il est prioritaire sur le pays par defaut passe en option
	$telephone_prefixes_pays = charger_fonction('telephone_prefixes_pays', 'verifier');
	$prefixes = $telephone_prefixes_pays();
	if (isset($options['prefixes_pays']) and $options['prefixes_pays']) {
		$prefixes = $prefixes +  $options['prefixes_pays'];
	}
	foreach ($prefixes as $prefix => $code_pays) {
		$regexp = '/^(\+|00)'.$prefix.'/';
		if (preg_match($regexp, $tel)) {
			$options['pays'] = $code_pays;
			// on normalise le prefixe, mais on ne le remplace par par zero, car ça n'est valable que pour certains pays et ça casse la verif sur d'autres
			$tel = preg_replace($regexp, '+'.$prefix, $tel);
			break;
		}
	}

	// si on connait le pays (par option ou par indicatif) et qu'on a une fonction de verification pour ce pays on utilise
	$pays = (isset($options['pays']) ? strtolower($options['pays']) : null);
	if ($pays and $verifier_telephone_pays = charger_fonction('telephone_pays_' . $options['pays'], 'verifier', true)) {
		if ($e = $verifier_telephone_pays($tel, $erreur)) {
			return $e;
		}
		return $ok;
	}

	// On interdit les 000 etc. mais je pense qu'on peut faire plus malin
	// On interdit egalement les "numéros" tout en lettres
	// TODO finaliser les numéros à la con
	if (intval($tel) == 0) {
		return $erreur;
	}

	return $ok;
}


function verifier_telephone_prefixes_pays_dist() {

	$indicatifs = array(
		'32' => 'be',
		'33' => 'fr',
		'34' => 'es',
		'41' => 'ch',
	);

	return $indicatifs;
}


function verifier_telephone_pays_ch_dist($tel, $message_erreur_defaut) {
	if (!preg_match('/^(0|\+41)[0-9]{9}$/', $tel)) {
		return $message_erreur_defaut;
	}
}

function verifier_telephone_pays_es_dist($tel, $message_erreur_defaut) {
	if (!preg_match('/^(\+34)?[69][0-9]{8}$/', $tel)) {
		return $message_erreur_defaut;
	}
}

function verifier_telephone_pays_fr_dist($tel, $message_erreur_defaut) {
	if (!preg_match('/^(0|\+33)[1-9][0-9]{8}$/', $tel)) {
		return $message_erreur_defaut;
	}
}

function verifier_telephone_pays_be_dist($tel, $message_erreur_defaut) {
	// Patterns
	$pattern = '/^(0|\+32)[0-9]{8}$/';
	$pattern_mobile = '/^(0|\+32)4(60|[789]\d)[0-9]{6}$/';
	if (!preg_match($pattern, $tel) and !preg_match($pattern_mobile, $tel)){
		return $message_erreur_defaut;
	}
}