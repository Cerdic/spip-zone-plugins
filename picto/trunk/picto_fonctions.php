<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function filtre_rotation_picto($input) {
	if (empty($input)) {
		return '';
	}
	$input = strtoupper($input);
	if ($input == 'GAUCHE') {
		return '270';
	}
	if ($input == 'DROITE') {
		return '90';
	}
	if ($input == 'BAS') {
		return '180';
	}

	return '';
}

function filtre_symetrie_picto($input) {
	if (empty($input)) {
		return '';
	}
	$input = strtoupper($input);
	if ($input == 'H') {
		return 'horizontal';
	}
	if ($input == 'V') {
		return 'vertical';
	}

	return '';
}

function filtre_animation_picto($input) {
	if (empty($input)) {
		return '';
	}
	$input = strtoupper($input);
	if ($input == 'CONTINU' || $input == 'CONTINUE') {
		return 'spin';
	}
	if ($input == 'ETAPES' || $input == 'ETAPE') {
		return 'pulse';
	}

	return '';
}

function filtre_taille_picto($input) {
	if (empty($input)) {
		return '';
	}
	$value = floatval($input);
	if ($value <= 1) {
		$code = '';
	} else if ($value <= 1.3) {
		$code = 'lg';
	} else {
		$value = round($value);
		if ($value <= 2) {
			$value = 2;
		}
		if ($value >= 5) {
			$value = 5;
		}
		$code = $value . 'x';
	}

	return $code;
}

function filtre_aligner_picto($input) {
	if (empty($input)) {
		return '';
	}
	$input = strtoupper($input);

	if ($input == 'GAUCHE') {
		return 'left';
	}
	if ($input == 'DROITE') {
		return 'right';
	}

	return '';
}

/**
 * Lister le nom des classes mis à disposition par la librairie FontAwesome.
 *
 * @return array
 */
function lister_picto() {
	$style_picto = find_in_path('fontAwesome/css/font-awesome.min.css');
	$style_picto = file_get_contents($style_picto);
	$list = array();

	preg_match_all("/\.fa-([[:alpha:]]+-?[[:alpha:]]+?):before/", $style_picto, $picto);
	if (isset($picto[1]) and is_array($picto[1]) and count($picto[1])) {
		natsort($picto[1]);
		$list = array_merge($list, $picto[1]);
	}
	preg_match_all("/,\.fa-([[:alpha:]]+-?[[:alpha:]]+?):before/", $style_picto, $picto);
	if (isset($picto[1]) and is_array($picto[1]) and count($picto[1])) {
		natsort($picto[1]);
		$list = array_diff($list, $picto[1]);
		foreach ($picto[1] as $index => $alias) {
			$list[] = $alias . " <em>(alias)</em>";
		}
	}
	natsort($list);

	return $list;
}