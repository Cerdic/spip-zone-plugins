<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// ----------------------
// FILTRES INSEE
// ----------------------
// CF. <http://fr.wikipedia.org/wiki/Code_Insee>
// Exemple : siren=732 829 320
// Exemple : siret=404 833 048 00022 tva=FR 83 404 833 048

/**
 * Calcule un numéro SIREN.
 *
 * @param numeric $_num          Le numéro SIREN à compléter (8 ou 9 chiffres)
 * @param bool    $remplacer_cle Doit-on renvoyer un SIREN valide (en remplaçant la clé le cas échéant) ?
 */
function calculer_siren($_num = null, $remplacer_cle = false) {
	if (is_null($_num)) {
		return 0;
	}
	$num = preg_replace('/[^0-9]/', '', $_num);
	if (strlen($num) > 9) {
		return false;
	}
	if ($remplacer_cle && strlen($num) == 9) {
		$num = substr($num, 0, 8);
	}
	if (strlen($num) == 8) {
		$_num = $_num.cle_de_luhn($num);
	}

	return $_num;
}

/**
 * Vérifier la validité d'un numéro SIREN.
 *
 * @param numeric $num Le numéro à vérifier (8 ou 9 chiffres)
 */
function siren_valide($num = null) {
	if (is_null($num)) {
		return 0;
	}
	$num = calculer_siren($num);
	$num = preg_replace('/[^0-9]/', '', $num);
	if (strlen($num) != 9) {
		return false;
	}

	return luhn_valide($num);
}

/**
 * Calcule un numéro SIRET.
 *
 * @param numeric $_num_siren    Le numéro SIREN (8 ou 9 chiffres)
 * @param numeric $_num_siret    Le numéro SIRET (1 à 5 chiffres)
 * @param bool    $remplacer_cle Doit-on renvoyer un SIREN valide (en remplaçant la clé le cas échéant) ?
 */
function calculer_siret($_num_siren = null, $_num_siret = null, $remplacer_cle = false) {
	if (is_null($_num_siren) || is_null($_num_siret)) {
		return 0;
	}
	$_num_siren = calculer_siren($_num_siren);
	$num_siret = preg_replace('/[^0-9]/', '', $_num_siret);
	$num_siren = preg_replace('/[^0-9]/', '', $_num_siren);
	if (strlen($num_siret) > 5) {
		return false;
	} elseif (strlen($num_siret) <= 3) {
		$num_siret = sprintf('%04d', $num_siret);
	} elseif ($remplacer_cle && strlen($num_siret) == 5) {
		$num_siret = substr($num_siret, 0, 4);
	}
	if (strlen($num_siret) == 4) {
		$_num_siret = $num_siret.cle_de_luhn($num_siren.$num_siret);
	}

	return $_num_siren.(substr_count($_num_siren, ' ') ? ' ' : '').$_num_siret;
}

/**
 * Vérifier la validité d'un numéro SIRET.
 *
 * @param numeric $num_siren Le numéro SIREN (8 ou 9 chiffres) | Si unique argument, considéré comme SIRET complet
 * @param numeric $num_siret Le numéro SIRET (1 à 5 chiffres)
 */
function siret_valide($num_siren = null, $num_siret = null) {
	if (is_null($num_siren)) {
		return false;
	}
	$num_siren = preg_replace('/[^0-9]/', '', $num_siren);
	if (is_null($num_siret)) {
		$bckup = $num_siren;
		$num_siren = substr($bckup, 0, 9);
		$num_siret = substr($bckup, 9);
	}
	$num_siren = calculer_siren($num_siren);
	if (!siren_valide($num_siren)) {
		return false;
	}
	$num_siret = calculer_siret($num_siren, $num_siret);
	$num_siret = preg_replace('/[^0-9]/', '', $num_siret);
	if (strlen($num_siret) != 14) {
		return false;
	}

	return luhn_valide($num_siren.$num_siret);
}

/**
 * Renvoie l'identifiant de TVA intracommunautaire correspondant à un numéro SIREN tel qu'il est calculé par l'INSEE
 * Valable seulement en France.
 *
 * @param numeric $_num_siren Le numéro SIREN utilisé
 *
 * @return string L'identifiant de TVA qui préfixe le numéro SIREN
 */
function calculer_tva_intracom($_num_siren = null) {
	if (is_null($_num_siren)) {
		return 0;
	}
	$num_siren = calculer_siren($_num_siren);
	$num_siren = preg_replace('/[^0-9]/', '', $num_siren);
	$tva = (12 + 3 * ($num_siren % 97)) % 97;

	return 'FR '.$tva;
}

/**
 * Vérifier la validité d'un numéro de TVA intracommunautaire.
 *
 * @param numeric $num_siren Le numéro TVA
 */
function tva_intracom_valide($_num_tva = null) {
	if (is_null($_num_tva)) {
		return 0;
	}
	$num_tva = preg_replace('/[^0-9]/', '', $_num_tva);
	if (strlen($num_tva) != 11) {
		return false;
	}
	$num_siren = substr($num_tva, 2);
	$tva_verif = str_replace('FR ', '', calculer_tva_intracom($num_siren));

	return $tva_verif == substr($num_tva, 0, 2);
}

/**
 * Formule de Luhn.
 *
 * @param numeric $_num La suite de chiffre sur laquelle calculée la clé (sans la clé donc ...)
 *
 * @return numeric La clé calculée
 */
function cle_de_luhn($_num = null) {
	if (is_null($_num)) {
		return 0;
	}
	$num = $_num.'0';
	$length = strlen($num);
	$checksum = 0;
	for ($i = $length - 1; $i >= 0; --$i) {
		$digit = $num[$i];
		if ((($length - $i) % 2) == 0) {
			$digit = $digit * 2;
			if ($digit > 9) {
				$digit = $digit - 9;
			}
		}
		$checksum += $digit;
	}
	$cleLuhn = 10 - ($checksum % 10);
	if ($cleLuhn == 10) {
		$cleLuhn = 0;
	}

	return $cleLuhn;
}

/**
 * Vérifie que le dernier chiffre de la suite est bien la clé de Luhn de la suite.
 *
 * @param numeric $num La suite à vérifier AVEC la clé en fin
 *
 * @return bool OK si le dernier chiffre est bien la clé de Luhn de la suite précédente
 */
function luhn_valide($num = null) {
	if (is_null($num)) {
		return 0;
	}
	$_num = substr($num, 0, strlen($num) - 1);

	return (intval($num) == intval($_num.cle_de_luhn($_num)));
}

/*
$siren = '404 833 04';
$siren_full = calculer_siren($siren);
$siren_ok = siren_valide($siren_full);

$siren_fake = '404 833 043';
$siren_fake_ok = siren_valide($siren_fake);
$siren_fake_recalc = calculer_siren($siren, true);
$siren_fake_recalc_ok = siren_valide($siren_fake_recalc);

$siret = '2';
$siret_full = calculer_siret($siren, $siret);
$siret_ok = siret_valide($siret_full);

$siret_fake = '00024';
$siret_fake_ok = siret_valide($siren_fake.$siret_fake);
$siret_fake_recalc = calculer_siret($siren_fake_recalc, $siret_fake, true);
$siret_fake_recalc_ok = siret_valide($siret_fake_recalc);

$tva_intracom = calculer_tva_intracom($siren);
$tva_intracom_full = $tva_intracom.' '.$siren_full;
$tva_ok = tva_intracom_valide($tva_intracom_full);

$tva_intracom_fake = 'FR 76';
$tva_fake_ok = tva_intracom_valide($tva_fake.$siren_fake);
$tva_fake_recalc = calculer_tva_intracom($siren_fake_recalc);
$tva_fake_recalc_ok = tva_intracom_valide($tva_fake_recalc.$siren_fake_recalc);

	echo "<ul>";
	echo "<li>SIREN : '$siren'</li>";
	echo "<li>SIRET : '$siret'</li>";
	echo "<li>SIREN completé : '$siren_full' (valide : ".var_export($siren_ok, 1).")</li>";
	echo "<li>SIRET completé : '$siret_full' (valide : ".var_export($siret_ok, 1).")</li>";
	echo "<li>TVA intracommunautaire calculé : '$tva_intracom_full' (valide : ".var_export($tva_ok, 1).")</li>";
	echo "</ul>";
	echo "<ul>";
	echo "<li>SIREN faux : '$siren_fake' (valide : ".var_export($siren_fake_ok, 1).")</li>";
	echo "<li>SIRET faux : '$siret_fake' (valide : ".var_export($siret_fake_ok, 1).")</li>";
	echo "<li>TVA faux : '$tva_intracom_fake' (valide : ".var_export($tva_fake_ok, 1).")</li>";
	echo "</ul>";
	echo "<ul>";
	echo "<li>SIREN faux recalculé : '$siren_fake_recalc' (valide : ".var_export($siren_fake_recalc_ok, 1).")</li>";
	echo "<li>SIRET faux recalculé : '$siret_fake_recalc' (valide : ".var_export($siret_fake_recalc_ok, 1).")</li>";
	echo "<li>TVA faux recalculé : '$tva_fake_recalc' (valide : ".var_export($tva_fake_recalc_ok, 1).")</li>";
	echo "</ul>";

exit;
*/

/**
 * Complète les références depuis SIREN et SIRET.
 *
 * @param numeric $num_siren Le numéro SIREN (8 ou 9 chiffres) | Si unique argument, considéré comme SIRET complet
 * @param numeric $num_siret Le numéro SIRET (1 à 5 chiffres)
 *
 * @return array( SIREN , SIRET , TVA )
 */
function completer_insee($num_siren = null, $num_siret = null, $notva = false, $forcer_siret = false) {
	if (is_null($num_siren)) {
		return false;
	}
	$num_siren = preg_replace('/[^0-9]/', '', $num_siren);
	if (is_null($num_siret)) {
		if (strlen($num_siren) > 9) {
			$bckup = $num_siren;
			$num_siren = substr($bckup, 0, 9);
			$num_siret = substr($bckup, 9);
		} else {
			if ($forcer_siret) {
				$num_siret = '1';
			} else {
				$num_siret = false;
			}
		}
	}
	$siren_ok = calculer_siren($num_siren);
	$siret_ok = $num_siret ? calculer_siret($siren_ok, $num_siret) : '';
	$tva_intracom_ok = $notva === true ? '' : calculer_tva_intracom($siren_ok);

	return array($siren_ok, str_replace($siren_ok, '', $siret_ok), $tva_intracom_ok);
}
