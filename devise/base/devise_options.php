<?php

/* Renvoie un tableau contenant les devises.
 * Si le parametre $description est fourni, un tableau associatif de la
 * forme ABC => texte est renvoye, ou 'texte' est interprete par la
 * fonction formater_devise().
 * Si $description est absent ou vide, un tableau simple contenant tous les
 * codes ISO dans l'ordre alphabetique est renvoye.
 */
function devises_codes($description='') {
    $dev = Array(
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
    if (0 == strlen($description)) {
        return $dev;
    } else {
        $arr = array_map(create_function('$d', "return formater_devise(\$d, '$description');"),
                         devises_codes());
        return array_combine($dev, $arr);
    }
}

/* Affiche le nom de la devise, au format desire. Le format peut prendre en
 * compte les champs suivants:
 *  - %C : code ISO de la devise
 *  - %N : nom de la devise
 *  - %sN: nom de la devise pour un montant singulier
 *  - %pN: nom de la devise pour un montant pluriel
 *  - %% : caractère '%'
 * La valeur par defaut du parametre $format est '%C - %N'.
 */
function formater_devise($devise, $format='%C - %N') {
    if (0 == strlen($devise)) {
        return '';
    }
    $codes_magiques = array('/%%/', '/%C/', '/%N/', '/%sN/', '/%pN/');
    $codes_interpretes = array('%', $devise, _T("devise:$devise"), _T("devise:s_$devise"), _T("devise:p_$devise"));
    $resultat = preg_replace($codes_magiques, $codes_interpretes, $format);
    return preg_replace($codes_magiques, $codes_interpretes, $format);
}

?>
