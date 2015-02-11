<?php

/**
 * Fonctions pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @package    SPIP\Xiti\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/** Liste des caractères spéciaux dans le titre de la page
 * @param $texte
 * @return string
 */
function xiti_caracteres($texte) 
{ 
	$aaccent = array(" ","’","\x98\"","\x99’","#8217","&","?",",",";","\"","&nbsp","À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "ß", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "ÿ", "Ā", "ā", "Ă", "ă", "Ą", "ą", "Ć", "ć", "Ĉ", "ĉ", "Ċ", "ċ", "Č", "č", "Ď", "ď", "Đ", "đ", "Ē", "ē", "Ĕ", "ĕ", "Ė", "ė", "Ę", "ę", "Ě", "ě", "Ĝ", "ĝ", "Ğ", "ğ", "Ġ", "ġ", "Ģ", "ģ", "Ĥ", "ĥ", "Ħ", "ħ", "Ĩ", "ĩ", "Ī", "ī", "Ĭ", "ĭ", "Į", "į", "İ", "ı", "Ĳ", "ĳ", "Ĵ", "ĵ", "Ķ", "ķ", "Ĺ", "ĺ", "Ļ", "ļ", "Ľ", "ľ", "Ŀ", "ŀ", "Ł", "ł", "Ń", "ń", "Ņ", "ņ", "Ň", "ň", "ŉ", "Ō", "ō", "Ŏ", "ŏ", "Ő", "ő", "Œ", "œ", "Ŕ", "ŕ", "Ŗ", "ŗ", "Ř", "ř", "Ś", "ś", "Ŝ", "ŝ", "Ş", "ş", "Š", "š", "Ţ", "ţ", "Ť", "ť", "Ŧ", "ŧ", "Ũ", "ũ", "Ū", "ū", "Ŭ", "ŭ", "Ů", "ů", "Ű", "ű", "Ų", "ų", "Ŵ", "ŵ", "Ŷ", "ŷ", "Ÿ", "Ź", "ź", "Ż", "ż", "Ž", "ž", "ſ", "ƒ", "Ơ", "ơ", "Ư", "ư", "Ǎ", "ǎ", "Ǐ", "ǐ", "Ǒ", "ǒ", "Ǔ", "ǔ", "Ǖ", "ǖ", "Ǘ", "ǘ", "Ǚ", "ǚ", "Ǜ", "ǜ", "Ǻ", "ǻ", "Ǽ", "ǽ", "Ǿ", "ǿ"); 
	$saccent = array("_","_","_","_","_","","_","_","_","-","_","A", "A", "A", "A", "A", "A", "AE", "C", "E", "E", "E", "E", "I", "I", "I", "I", "D", "N", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "Y", "s", "a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "y", "y", "A", "a", "A", "a", "A", "a", "C", "c", "C", "c", "C", "c", "C", "c", "D", "d", "D", "d", "E", "e", "E", "e", "E", "e", "E", "e", "E", "e", "G", "g", "G", "g", "G", "g", "G", "g", "H", "h", "H", "h", "I", "i", "I", "i", "I", "i", "I", "i", "I", "i", "IJ", "ij", "J", "j", "K", "k", "L", "l", "L", "l", "L", "l", "L", "l", "l", "l", "N", "n", "N", "n", "N", "n", "n", "O", "o", "O", "o", "O", "o", "OE", "oe", "R", "r", "R", "r", "R", "r", "S", "s", "S", "s", "S", "s", "S", "s", "T", "t", "T", "t", "T", "t", "U", "u", "U", "u", "U", "u", "U", "u", "U", "u", "U", "u", "W", "w", "Y", "y", "Y", "Z", "z", "Z", "z", "Z", "z", "s", "f", "O", "o", "U", "u", "A", "a", "I", "i", "O", "o", "U", "u", "U", "u", "U", "u", "U", "u", "U", "u", "A", "a", "AE", "ae", "O", "o"); 
  	return str_replace($aaccent, $saccent, $texte); 
} 

/** Nettoyer les caractères spéciaux dans le titre de la page
 * @param $texte
 * @return string
 */
function xiti_nettoyeur($texte) 
{ 
	return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), 
	array('', '-', ''), xiti_caracteres($texte))); 
}

function xiti($texte) {
	$texte = strtolower(strtoascii($texte));
	$texte = preg_replace("[^a-z0-9_:~\\\/\-]","_",$texte); 
	return($texte);
}

/** Gérer les caractères non-latin
 * @param $texte
 * @return string
 */
function xiti_nonlatin($texte) {
	$texte = preg_replace("[^a-z0-9_:~\\\/\-]","_",$texte); 
	return($texte);
}

/** Nettoyer les URL's
 * @param $texte
 * @return string
 */
function xiti_xtdmc($texte) {
	return str_replace(array('http://www','http://'), array('','.'), $texte);
}

/** Passe une chaîne vers de l'ascii
 * @param $texte
 * @param $encoding
 * @return string
 */
function strtoascii($texte, $encoding = 'utf-8') {
    mb_regex_encoding($encoding); // jeu de caractères courant pour les expressions rationnelles. 
    
    // Tableau des corespondance
    $str_ascii = array(
        'A'     => 'ÀÁÂÃÄÅĀĂǍẠẢẤẦẨẪẬẮẰẲẴẶǺĄ',
        'a'     => 'àáâãäåāăǎạảấầẩẫậắằẳẵặǻą',
        'C'     => 'ÇĆĈĊČ',
        'c'     => 'çćĉċč',
        'D'     => 'ÐĎĐ',
        'd'     => 'ďđ',
        'E'     => 'ÈÉÊËĒĔĖĘĚẸẺẼẾỀỂỄỆ',
        'e'     => 'èéêëēĕėęěẹẻẽếềểễệ',
        'G'     => 'ĜĞĠĢ',
        'g'     => 'ĝğġģ',
        'H'     => 'ĤĦ',
        'h'     => 'ĥħ',
        'I'     => 'ÌÍÎÏĨĪĬĮİǏỈỊ',
        'J'     => 'Ĵ',
        'j'     => 'ĵ',
        'K'     => 'Ķ',
        'k'     => 'ķ',
        'L'     => 'ĹĻĽĿŁ',
        'l'     => 'ĺļľŀł',
        'N'     => 'ÑŃŅŇ',
        'n'     => 'ñńņňŉ',
        'O'     => 'ÒÓÔÕÖØŌŎŐƠǑǾỌỎỐỒỔỖỘỚỜỞỠỢ',
        'o'     => 'òóôõöøōŏőơǒǿọỏốồổỗộớờởỡợð',
        'R'     => 'ŔŖŘ',
        'r'     => 'ŕŗř',
        'S'     => 'ŚŜŞŠ',
        's'     => 'śŝşš',
        'T'     => 'ŢŤŦ',
        't'     => 'ţťŧ',
        'U'     => 'ÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛỤỦỨỪỬỮỰ',
        'u'     => 'ùúûüũūŭůűųưǔǖǘǚǜụủứừửữự',
        'W'     => 'ŴẀẂẄ',
        'w'     => 'ŵẁẃẅ',
        'Y'     => 'ÝŶŸỲỸỶỴ',
        'y'     => 'ýÿŷỹỵỷỳ',
        'Z'     => 'ŹŻŽ',
        'z'     => 'źżž',
        // Ligatures
        'AE'     => 'Æ',
        'ae'     => 'æ',
        'OE'     => 'Œ',
        'oe'     => 'œ'
    );
 
    // Convertion
    foreach ($str_ascii as $k => $v) {
        $texte = mb_ereg_replace('['.$v.']', $k, $texte);
    }
 
    return $texte;
}

?>