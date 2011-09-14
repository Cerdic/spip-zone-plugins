<?php

//// ATTENTION lors de l'édition de ce fichier: il contient
//// des caractères en UTF-8. Si votre éditeur ou logiciel de
//// transfert gère mal les encodages, cela pourrait provoquer
//// des problèmes.


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

/* Renvoie le symbole monétaire (en UTF-8) d'une devise, s'il existe.
 * Attention lors de la création de sites, l'utilisation de symboles
 * monetaires est une mauvaise idee car c'est ambigu: certains symboles
 * (par exemple '$') sont utilises pour plusieurs monnaies.
 * Pour eviter cela, la fonction ne renvoie un symbole que s'il est
 * non-ambigu. Pour couvrir plus de cas, il est possible de passer un
 * parametre supplementaire:
 *  - 'etendu': Renvoie un symbole plus precis, par exemple US$ ou Mex$.
 *              Attention: il peut ne pas exister (ex: peso argentin).
 *  - 'ambigu': Renvoie un symbole simple (utile dans les rares cas ou
 *              l'ambiguite n'est pas un probleme). Attention: certaines
 *              monnaies, actuellement ou dans le futur, peuvent ne pas
 *              avoir de symbole.
 *  - 'unique' (par defaut): Renvoie le symbole simple, mais uniquement
 *                           s'il n'est pas ambigu (ex: euro).
 * Dans tous les cas, il est possible que la fonction renvoie un resultat
 * vide. Prevoyez donc une valeur de remplacement (par exemple le nom de
 * la monnaie en toute lettres, ou encore "¤").
 *
 * Note: La question de la position du symbole monétaire n'est pas abordée
 * ici. C'est une question complexe qui fait intervenir les conventions du
 * pays et pas uniquement de la devise. Par ailleurs, il faut généralement
 * le montant pour déterminer le format complet. Exemples:
 *  - LSL: 1L mais 2M (loti/maloti)
 *  - TZS: 1/- (ou 1/=) pour 1; 1/5 pour 1.5; -/5 (ou =/5) pour 0.5, etc.
 *  - USD: $42.5
 *  - EUR: €42.5 ou 42.5€ suivant les pays
 *  - SEK: 42.5 kr (simple et ambigü) ou SEK 42.5
 *  - CVE: 42$50, 42$00, 42.5 Esc.
 * La classe NumberFormatter peut aider pour la présentation des nombres
 * (séparateur de milliers et de décimales suivant la locale), mais pas
 * pour le symbole monétaire ni sa position car sa gestion est simpliste:
 * il ne prend par exemple pas en compte l'ambigüité.
 */
function symbole_monetaire($devise, $format='unique') {
    $sym = '';
    $symE = null;
    switch($devise) {
    // TODO: http://en.wikipedia.org/wiki/List_of_circulating_currencies à partir de MYR
        case 'AFN': $sym = '؋'; break;
        case 'ALL': $sym = 'L'; $symE = 'Lek'; break;
        case 'AMD': $sym = 'դր.'; break;
        case 'ANG': $sym = 'ƒ'; $symE = 'NAƒ'; break;
        case 'AOA': $sym = 'Kz'; break;
        case 'ARS': $sym = '$'; $symE = ''; break;
        case 'AUD': $sym = '$'; $symE = 'A$'; break;
        case 'AWG': $sym = 'ƒ'; $symE = 'Afl.'; break;
        case 'AZN': $sym = 'ман'; break; // Il y a un symbole mais pas encore en Unicode
        case 'BAM': $sym = 'KM'; break;
        case 'BMD': $sym = '$'; $symE = 'BD$'; break;
        case 'BBD': $sym = '$'; $symE = 'Bds$'; break;
        case 'BDT': $sym = '৳'; break;
        case 'BGN': $sym = 'лв'; $symE = ''; break;
        case 'BHD': $sym = '.د.ب'; break;
        case 'BIF': $sym = '₣'; $symE = 'FBu'; break;
        case 'BND': $sym = '$'; $symE = ''; break; // B$ est utilisé, mais ambigü avec BSD
        case 'BOB': $sym = 'Bs'; break;
        case 'BRL': $sym = '$'; $symE = 'R$'; break;
        case 'BSD': $sym = '$'; $symE = ''; break; // B$ est utilisé, mais ambigü avec BND
        case 'BTC': $sym = 'Ƀ'; break; // Utilise, mais pas encore de consensus
        case 'BTN': $sym = 'Nu.'; break;
        case 'BWP': $sym = 'P'; $symE = ''; break;
        case 'BYR': $sym = 'Br'; $symE = ''; break;
        case 'BZD': $sym = '$'; $symE = 'BZ$'; break;
        case 'CAD': $sym = '$'; $symE = 'C$'; break;
        case 'CDF': $sym = '₣'; $symE = 'FC'; break;
        case 'CHF': $sym = '₣'; $symE = 'CHF'; break;
        case 'CLP': $sym = '$'; $symE = ''; break;
        case 'CNY': $sym = '¥'; $symE = ''; break;
        case 'COP': $sym = '$'; $symE = 'Col$'; break;
        case 'CRC': $sym = '₡'; break;
        case 'CUC': $sym = '$'; $symE = 'CUC$'; break;
        case 'CUP': $sym = '₱'; break;
        case 'CVE': $sym = '$'; $symE = 'Esc.'; break;
        case 'CZK': $sym = 'Kč'; break;
        case 'DJF': $sym = '₣'; $symE = 'Fdj'; break;
        case 'DKK': $sym = 'kr'; $symE = 'Dkr'; break;
        case 'DOP': $sym = '$'; $symE = 'RD$'; break;
        case 'DZD': $sym = 'د.ج'; break;
        case 'EGP': $sym = 'ج.م'; break;
        case 'ERN': $sym = 'Nfk'; break;
        case 'ETB': $sym = 'Br'; $symE = ''; break;
        case 'EUR': $sym = '€'; break;
        case 'FJD': $sym = '$'; $symE = 'FJ$'; break;
        case 'GEL': $sym = 'ლ'; break;
        case 'GBP': $sym = '£'; $symE = ''; break;
        case 'GHS': $sym = '₵'; break;
        case 'GMD': $sym = 'D'; break;
        case 'GNF': $sym = '₣'; $symE = 'GFr'; break;
        case 'GTQ': $sym = 'Q'; break;
        case 'GYD': $sym = '$'; $symE = 'G$'; break;
        case 'HKD': $sym = '$'; $symE = 'HK$'; break;
        case 'HNL': $sym = 'L'; $symE = ''; break;
        case 'HRK': $sym = 'kn'; break;
        case 'HTG': $sym = 'G'; break;
        case 'HUF': $sym = 'Ft'; break;
        case 'IDR': $sym = 'Rp'; break;
        case 'ILS': $sym = '₪'; break;
        case 'INR': $sym = '₹'; break;
        case 'IQD': $sym = 'ع.د'; break;
        case 'IRR': $sym = '﷼'; break;
        case 'ISK': $sym = 'kr'; $symE = 'Íkr'; break;
        case 'JMD': $sym = '$'; $symE = 'J$'; break;
        case 'JOD': $sym = 'د.ا'; break;
        case 'JPY': $sym = '¥'; $symE = ''; break;
        case 'KES': $sym = 'Sh'; $symE = 'KSh'; break;
        case 'KGS': $sym = 'лв'; $symE = ''; break;
        case 'KMF': $sym = '₣'; $symE = 'CF'; break;
        case 'KPW': $sym = '₩'; $symE = ''; break;
        case 'KRW': $sym = '₩'; $symE = ''; break;
        case 'KWD': $sym = 'د.ك'; break;
        case 'KYD': $sym = '$'; $symE = 'CI$'; break;
        case 'KZT': $sym = '₸'; break;
        case 'LAK': $sym = '₭'; break;
        case 'LRD': $sym = '$'; $symE = 'LD$'; break;
        case 'LSL': $sym = 'M'; $symE = ''; break; // Singulier: L (pas la peine de rajouter une option juste pour ce cas)
        case 'LTL': $sym = 'Lt'; break;
        case 'LYD': $sym = 'ل.د'; break;
        case 'LBP': $sym = 'ل.ل'; break;
        case 'LVL': $sym = 'Ls'; break;
        case 'MGA': $sym = 'Ar'; break;
        case 'MKD': $sym = 'ден'; break;
        case 'MOP': $sym = 'MOP$'; break;
        case 'MMK': $sym = 'K'; break;
        case 'MWK': $sym = 'MK'; break;
        case 'MXN': $sym = '$'; $symE = 'Mex$'; break;
        case 'NAD': $sym = '$'; $symE = 'N$'; break;
        case 'NIO': $sym = '$'; $symE = 'C$'; break;
        case 'NOK': $sym = 'kr'; $symE = ''; break;
        case 'NZD': $sym = '$'; $symE = 'NZ$'; break;
        case 'PEN': $sym = 'S/.'; break;
        case 'RUB': $sym = 'руб.'; break;
        case 'SBD': $sym = '$'; $symE = 'SI$'; break;
        case 'SEK': $sym = 'kr'; $symE = 'SEK'; break;
        case 'SGD': $sym = '$'; $symE = 'S$'; break;
        case 'SHP': $sym = '£'; break;
        case 'SOS': $sym = 'Sh'; $symE = 'Sh.So.'; break;
        case 'SRD': $sym = '$'; $symE = ''; break;
        case 'THB': $sym = '฿'; break;
        case 'TOP': $sym = '$'; $symE = 'T$'; break;
        case 'TTD': $sym = '$'; $symE = 'TT$'; break;
        case 'TWD': $sym = '$'; $symE = 'NT$'; break;
        case 'TZS': $sym = ''; break; // Sous la forme "x/y". 1. Pas reconnaissable; 2. Pas codable ici; cf. http://en.wikipedia.org/wiki/Tanzanian_shilling#Symbol
        case 'UGX': $sym = 'Sh'; $symE = 'USh'; break;
        case 'USD': $sym = '$'; $symE = 'US$'; break;
        case 'UYU': $sym = '$'; $symE = '$U'; break;
        case 'UZS': $sym = 'лв'; $symE = ''; break;
        case 'ZAR': $sym = 'R'; break;
    }
    if ('unique' == $format) {
        $res = (is_null($symE) ? $sym : '');
    } elseif ('etendu' == $format) {
        $res = (is_null($symE) ? $sym : $symE);
    } elseif ('ambigu' == $format) {
        $res = $sym;
    } else {
        $res = '';
    }
    return $res;
}

?>
