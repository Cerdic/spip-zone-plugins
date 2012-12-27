<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function angle2direction($degre) {
	$dir = '';
	switch(round($degre / 22.5) % 16)
	{
		case 0:  $dir = 'N'; break;
		case 1:  $dir = 'NNE'; break;
		case 2:  $dir = 'NE'; break;
		case 3:  $dir = 'ENE'; break;
		case 4:  $dir = 'E'; break;
		case 5:  $dir = 'ESE'; break;
		case 6:  $dir = 'SE'; break;
		case 7:  $dir = 'SSE'; break;
		case 8:  $dir = 'S'; break;
		case 9:  $dir = 'SSW'; break;
		case 10: $dir = 'SW'; break;
		case 11: $dir = 'WSW'; break;
		case 12: $dir = 'W'; break;
		case 13: $dir = 'WNW'; break;
		case 14: $dir = 'NW'; break;
		case 15: $dir = 'NNW'; break;
	}
	return $dir;
}

function kilometre2mile($km) {
	return 0.6215*$km;
}

function celsius2farenheit($c) {
	return $c*9/5 + 32;
}

function millimetre2inch($mm) {
	return $mm/25.4;
}

function millibar2inch($mbar) {
	return $mbar/33.86;
}

function temperature2ressenti($temperature, $vitesse_vent) {

	// La temperature ressentie n'est calculee que pour des temperatures ambiantes comprises entre
	// -50°C et +10°C
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