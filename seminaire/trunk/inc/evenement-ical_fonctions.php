<?php

//
// Export iCal
//

// celui du core ne fonctionne pas comme la RFC 5545

// http://doc.spip.org/@filtrer_ical
function filtrer_ical2($texte) {
    include_spip('inc/charsets');
    $texte = html2unicode($texte);
    $texte = unicode2charset(charset2unicode($texte, $GLOBALS['meta']['charset'], 1), 'utf-8');
	# la RFC impose des lignes de 75 car max, 
	# Lines of text SHOULD NOT be longer than 75 octets, excluding the line break
	if (strlen($texte) > 75)
	{
#	 $texte = chunk_split($texte);
	}

    #$texte = preg_replace("/\\/", " antislash ", $texte);
    $texte = preg_replace("/,/", "\\\,", $texte);
    $texte = preg_replace("/\n/", "\\n", $texte);
	#$texte = chunk_split($texte, 74);
    #$texte = preg_replace("/\r\n /", "\\n", $texte);
    #$texte = preg_replace("/\r\n/", "\\n", $texte);
    $texte = preg_replace("/:/", "\\\:", $texte);
    $texte = preg_replace("/;/", "\\\;", $texte);
	#$texte = "TOTO" . $texte;
    return $texte;
}

function folding_ical($texte, $arg1='FIXME') {
    include_spip('inc/charsets');
    $texte = html2unicode($texte);
    $texte = unicode2charset(charset2unicode($texte, $GLOBALS['meta']['charset'], 1), 'utf-8');
	# la RFC impose des lignes de 75 car max, 
	# Lines of text SHOULD NOT be longer than 75 octets, excluding the line break
	$texte = $arg1 . ":" . $texte;
	$texte = chunk_split($texte, 70);
    $texte = preg_replace("/\\\\/", "\\\\\\\\", $texte);
	#$texte = addcslashes($texte, "\92");
    $texte = preg_replace("/,/", "\\\,", $texte);
    #$texte = preg_replace("/:/", "\\\:", $texte);
    $texte = preg_replace("/;/", "\\\;", $texte);
    $texte = preg_replace("/\r\n/", "\r\n ", $texte);
    $texte = rtrim($texte);

	#$texte = "\b\r\n" . $arg1 . ":" . $texte;
    return $texte;
}

?>
