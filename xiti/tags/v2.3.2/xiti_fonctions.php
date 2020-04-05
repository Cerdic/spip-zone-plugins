<?php
/**
 * Fonctions pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014-2017
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @package	SPIP\Xiti\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Liste des caractères spéciaux dans le titre de la page
 * @param $texte
 * @return string
 */
function xiti_caracteres($texte) {
	return strtoascii($texte);
}

/**
 * Nettoyer les caractères spéciaux dans le titre de la page
 * @param $texte
 * @return string
 */
function xiti_nettoyeur($texte) {
	return strtolower(
		preg_replace(
			array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
			array('', '-', ''),
			xiti_caracteres($texte)
		)
	);
}

function xiti($texte) {
	$slugify = chercher_filtre('slugify');
	$texte_xiti = $texte;
	if ($slugify) {
		$texte_xiti = $slugify($texte_xiti);
		/**
		 * On ne doit pas retourner quelquechose de vide, on passe par l'ancienne fonction
		 */
		if (trim($texte_xiti) == '') {
			$texte_xiti = strtolower(strtoascii($texte));
			$texte_xiti = preg_replace('[^a-z0-9_:~\\\/\-]', '_', $texte_xiti);
		}
	} else {
		$texte_xiti = strtolower(strtoascii($texte_xiti));
		$texte_xiti = preg_replace('[^a-z0-9_:~\\\/\-]', '_', $texte_xiti);
	}

	return $texte_xiti;
}

/**
 * Gérer les caractères non-latin
 * @param string $texte
 * @return string
 */
function xiti_nonlatin($texte) {
	return xiti($texte);
}

/**
 * Nettoyer les URL's
 * @param string $texte
 * @return string
 */
function xiti_xtdmc($url) {
	$url = str_replace(array('http://www','https://www', 'http://', 'https://'), array('','','',''), $url);
	$url = preg_replace('/\/.*/', '', $url);
	return '.' . $url;
}

/**
 * Passe une chaîne vers de l'ascii
 * @param string $texte
 * @param string $encoding
 * @return string
 */
function strtoascii($texte, $encoding = 'utf-8') {
	$aaccent = array(' ', '#8217;', '&nbsp;', '#', '’', '\x98\"', '\x99’', '&amp;', '&', '?', ',', ';', '"', );
	$saccent = array('_', '_', '_', '_', '_', '_', '_', '', '', '_', '_', '_', '-');

	$texte = str_replace($aaccent, $saccent, $texte);
	mb_regex_encoding($encoding); // jeu de caractères courant pour les expressions rationnelles.

	// Tableau des corespondance
	$str_ascii = array(
		'A' => 'ÀÁÂÃÄÅĀĂǍẠẢẤẦẨẪẬẮẰẲẴẶǺĄĀĂĄǍ',
		'a' => 'àáâãäåāăǎạảấầẩẫậắằẳẵặǻą',
		'B' => 'ß',
		'C' => 'ÇĆĈĊČĆĈĊČ',
		'c' => 'çćĉċčćĉċč',
		'D' => 'ÐĎĐ',
		'd' => 'ďđ',
		'E' => 'ÈÉÊËĒĔĖĘĚẸẺẼẾỀỂỄỆ',
		'e' => 'èéêëēĕėęěẹẻẽếềểễệ',
		'F' => 'ƒ',
		'f' => 'ſ',
		'G' => 'ĜĞĠĢ', 'g' => 'ĝğġģ', 'H' => 'ĤĦ',
		'h' => 'ĥħ',
		'I' => 'ÌÍÎÏĨĪĬĮİǏỈỊ',
		'IJ' => 'Ĳ',
		'ij' => 'ĳ',
		'i' => 'ĩīĭįıǐí',
		'J' => 'Ĵ',
		'j' => 'ĵ',
		'K' => 'Ķ',
		'k' => 'ķ',
		'L' => 'ĹĻĽĿŁ',
		'l' => 'ĺļľŀł',
		'N' => 'ÑŃŅŇ',
		'n' => 'ñńņňŉ',
		'O' => 'ÒÓÔÕÖØŌŎŐƠǑǾỌỎỐỒỔỖỘỚỜỞỠỢ',
		'o' => 'òóôõöøōŏőơǒǿọỏốồổỗộớờởỡợð',
		'R' => 'ŔŖŘ',
		'r' => 'ŕŗř',
		'S' => 'ŚŜŞŠ',
		's' => 'śŝşš',
		'T' => 'ŢŤŦ',
		't' => 'ţťŧ',
		'U' => 'ÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛỤỦỨỪỬỮỰ',
		'u' => 'ùúûüũūŭůűųưǔǖǘǚǜụủứừửữự',
		'W' => 'ŴẀẂẄ',
		'w' => 'ŵẁẃẅ',
		'Y' => 'ÝŶŸỲỸỶỴ',
		'y' => 'ýÿŷỹỵỷỳ',
		'Z' => 'ŹŻŽ',
		'z' => 'źżž',
		// Ligatures
		'AE' => 'ÆǼ',
		'ae' => 'æǽ',
		'OE' => 'Œ',
		'oe' => 'œ'
	);

	// Conversion
	foreach ($str_ascii as $k => $v) {
		$texte = mb_ereg_replace('['.$v.']', $k, $texte);
	}

	return $texte;
}
