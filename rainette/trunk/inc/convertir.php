<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Conversion des kilomètres en miles.
 *
 * @param	float	$kilometre
 * @return	float
 */
function kilometre2mile($kilometre) {
	return 0.6215*$kilometre;
}

/**
 * Conversion des températures celsius en farenheit.
 *
 * @param	int		$celsius
 * @return	float
 */
function celsius2farenheit($celsius) {
	return $celsius*9/5 + 32;
}

/**
 * Conversion des millimètres en pouces.
 *
 * @param	float	$millimetre
 * @return	float
 */
function millimetre2inch($millimetre) {
	return $millimetre/25.4;
}

/**
 * Conversion des pressions millibar en pouces.
 *
 * @param	float	$millibar
 * @return	float
 */
function millibar2inch($millibar) {
	return $mbar/33.86;
}

/**
 * Calcul de la température ressentie (refroidissement éolien) en degrés celsius.
 *
 * Le calcul n'a de sens que pour des températures réelles supérieures à -50°C et inférieures à 10°C.
 * Au-delà de ces valeurs, la fonction renvoie la température réelle fournie en entrée.
 *
 * @param	int		$temperature	Temmpérature réelle mesurée en celsius
 * @param	float	$vitesse_vent	Vitesse du vent
 * @return	int						Température ressentie arrondie en entier
 */
function temperature2ressenti($temperature, $vitesse_vent) {

	if (($temperature >= -50) AND ($temperature <= 10)) {
		if ($vitesse_vent > 4.8)
			$ressenti = 13.12 + 0.6215*$temperature + (0.3965*$temperature - 11.37)*pow($vitesse_vent, 0.16);
		else
			$ressenti = $temperature + 0.2*(0.1345*$temperature - 1.59)*$vitesse_vent;
		$ressenti = round($ressenti, 0);
	}
	else
		$ressenti = $temperature;

	return intval($ressenti);

}

?>