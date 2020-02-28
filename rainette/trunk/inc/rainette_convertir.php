<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * @param int $angle
 *                   Angle du vent exprimé en degrés.
 *
 * @return string
 *                Direction du vent en abrégée anglais standard selon 16 directions.
 */
function angle2direction($angle) {
	static $liste_directions = array(
		0  => 'N',
		1  => 'NNE',
		2  => 'NE',
		3  => 'ENE',
		4  => 'E',
		5  => 'ESE',
		6  => 'SE',
		7  => 'SSE',
		8  => 'S',
		9  => 'SSW',
		10 => 'SW',
		11 => 'WSW',
		12 => 'W',
		13 => 'WNW',
		14 => 'NW',
		15 => 'NNW',
		16 => 'N',
		17 => 'V'
	);

	$direction = '';
	if (is_numeric($angle)) {
		$direction = $liste_directions[round($angle / 22.5) % 16];
	} elseif (in_array(strtoupper($angle), $liste_directions)) {
		$direction = strtoupper($angle);
	}

	return $direction;
}

/**
 * @param int $indice_uv
 *                       Entier représentant l'indice UV
 *
 * @return string
 *                Chaine représentant le risque lié à l'indice UV. Cette chaine permet de calculer
 *                l'item de langue du risque dans la langue requise.
 */
function indice2risque_uv($indice_uv) {
	$risque_uv = '';
	if (is_int($indice_uv)) {
		if ($indice_uv >= 11) {
			$risque_uv = 'extreme';
		} elseif ($indice_uv >= 8) {
			$risque_uv = 'tres_eleve';
		} elseif ($indice_uv >= 6) {
			$risque_uv = 'eleve';
		} elseif ($indice_uv >= 3) {
			$risque_uv = 'modere';
		} elseif ($indice_uv >= 0) {
			$risque_uv = 'faible';
		}
	}

	return $risque_uv;
}

/**
 * Conversion des kilomètres en miles.
 *
 * @param float $kilometre
 *                         La valeur réelle en kilomètres.
 *
 * @return float
 *               La valeur réelle correspondante convertie en miles
 */
function kilometre2mile($kilometre) {
	return 0.6215 * $kilometre;
}

/**
 * Conversion des températures celsius en farenheit.
 *
 * @param int $celsius
 *                     La valeur réelle en degrés celsius.
 *
 * @return float
 *               La valeur réelle correspondante convertie en farenheit.
 */
function celsius2farenheit($celsius) {
	return $celsius * 9 / 5 + 32;
}

/**
 * Conversion des millimètres en pouces.
 *
 * @param float $millimetre
 *                          La valeur réelle en millimètres
 *
 * @return float
 *               La valeur réelle correspondante convertie en pouces.
 */
function millimetre2inch($millimetre) {
	return $millimetre / 25.4;
}

/**
 * Conversion des pressions millibar en pouces.
 *
 * @param float $millibar
 *                        La valeur réelle en millibars
 *
 * @return float
 *               La valeur réelle correspondante convertie en pouces.
 */
function millibar2inch($millibar) {
	return $millibar / 33.86;
}

/**
 * Calcul de la température ressentie (refroidissement éolien) en degrés celsius.
 *
 * Le calcul n'a de sens que pour des températures réelles supérieures à -50°C et inférieures à 10°C.
 * Au-delà de ces valeurs, la fonction renvoie la température réelle fournie en entrée.
 *
 * @param float $temperature
 *                            Température réelle mesurée en celsius.
 * @param float $vitesse_vent
 *                            Vitesse du vent en kilomètre par heure.
 *
 * @return float
 *               Température ressentie arrondie en entier et exprimée en degrés celsius.
 */
function temperature2ressenti($temperature, $vitesse_vent) {
	if (($temperature >= -50) and ($temperature <= 10)) {
		if ($vitesse_vent > 4.8) {
			$ressenti = 13.12 + 0.6215 * $temperature + (0.3965 * $temperature - 11.37) * pow($vitesse_vent, 0.16);
		} else {
			$ressenti = $temperature + 0.2 * (0.1345 * $temperature - 1.59) * $vitesse_vent;
		}
		$ressenti = round($ressenti, 0);
	} else {
		$ressenti = $temperature;
	}

	return $ressenti;
}

/**
 * Conversion en kilomètres d'une valeur en mètres.
 *
 * @param int $metre
 *                   La valeur entière en mètres
 *
 * @return float
 *               La valeur correspondante convertie en kilomètres.
 */
function metre2kilometre($metre) {
	return $metre / 1000;
}
