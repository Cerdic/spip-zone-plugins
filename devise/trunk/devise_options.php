<?php
/**
 * Options du plugin Devise
 *
 * @plugin     Devise
 * @author     Davux
 * @licence    GNU/GPL
 * @package    SPIP\Devise\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Renvoie un tableau contenant les devises.
 *
 * Par défaut il s'agit d'un tableau simple avec les
 * codes ISO dans l'ordre alphabétique.
 * Si on donne un $format, il s'agit d'un tableau associatif
 * avec des paires code ISO => texte interprété par formater_devise
 *
 * @uses formater_devise
 * @param String $format
 * @return Array
 */
function devises_codes($format = '') {

	$retour = array();
	$devises = array(
		'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN',
		'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BTC', 'BTN', 'BWP', 'BYR', 'BZD',
		'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CUC', 'CUP', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD',
		'EEK', 'EGP', 'ERN', 'ETB', 'EUR',
		'FJD', 'FKP', 'GBP', 'GEL', 'GHS', 'GIP', 'GMD', 'GNF', 'GTQ', 'GWP', 'GYD',
		'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'IQD', 'IRR', 'ISK',
		'JMD', 'JOD', 'JPY',
		'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT',
		'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LTL', 'LVL', 'LYD',
		'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN',
		'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD',
		'OMR',
		'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG',
		'QAR',
		'RON', 'RSD', 'RUB', 'RWF',
		'SAR', 'SBD', 'SCR', 'SDG', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SYP', 'SZL',
		'THB', 'TJS', 'TMT', 'TND', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'USD', 'UYI', 'UYU', 'UZS',
		'VEF', 'VND', 'VUV',
		'WST',
		'YER',
		'ZAR', 'ZMK', 'ZWL',
	);
	if ($format) {
		foreach ($devises as $devise) {
			$retour[$devise] = formater_devise($devise, $format);
		}
	} else {
		$retour = $devises;
	}

	return $retour;
}

/**
 * Affiche le nom de la devise au format desiré.
 *
 * Le format peut prendre en compte les champs suivants:
 *  - %C : code ISO de la devise
 *  - %N : nom de la devise
 *  - %sN: nom de la devise pour un montant singulier
 *  - %pN: nom de la devise pour un montant pluriel
 *  - %% : caractère '%'
 * La valeur par défaut du parametre $format est '%C - %N'.
 *
 * @param String $devise
 * @param String $format
 * @return String
 */
function formater_devise($devise, $format = '%C - %N') {

	$texte = '';
	if (strlen($devise)) {
		$codes_magiques = array('/%%/', '/%C/', '/%N/', '/%sN/', '/%pN/');
		$codes_interpretes = array('%', $devise, _T("devise:$devise"), _T("devise:s_$devise"), _T("devise:p_$devise"));
		$resultat = preg_replace($codes_magiques, $codes_interpretes, $format);
		$texte = preg_replace($codes_magiques, $codes_interpretes, $format);
	}

	return $texte;
}
