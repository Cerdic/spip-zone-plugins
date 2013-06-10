<?php


function ligne_xtag($style, $contenu) {
	$style = trim(nettoyage_xtag($style));
	$contenu = trim(nettoyage_xtag($contenu));

	if (!strlen($contenu)) return '';

	switch(true) {
		case preg_match(',\bSURTITRE\b,i', $style):
			return "<ins class='surtitre'>\n$contenu\n</ins>\n\n";

		case preg_match(',\bSOUSTITRE\b,i', $style):
			return "<ins class='soustitre'>\n$contenu\n</ins>\n\n";
	
		case preg_match(',\bTITRE\b,i', $style):
			return "<ins class='titre'>\n$contenu\n</ins>\n\n";

		case preg_match(',\bNOTES\b,i', $style):
			return "[[<>\n$contenu\n]]\n\n";

		case preg_match(',\b(TEXTE|LETTRINE)\b,i', $style):
			return "$contenu\n\n";

		case preg_match(',\b(CHAP(EAU|O))\b,i', $style):
			return "<ins class='chapo'>\n$contenu\n</ins>\n\n";

		case preg_match(',\bINTER(TITRE)?\b,i', $style):
			return "{{{ $contenu }}}\n\n";

		case $style === '$':
			return "{{{ $contenu }}}\n\n";

	}

	return "@@$style\n\n$contenu\n\n";
}
 
function do_quark($c) {

	// transformer les <0x2014> en &#2014;
	$c = preg_replace_callback("/<0x([0-9A-F]+)>/",
		create_function ('$m',
			'return "&#".hexdec($m[1]).";";'),
		$c);

	// transformer les sauts de ligne du fichier original
	$c = preg_replace(",\r\n?,", "\n", $c);

	// transformer les tags contenant un @
	$c = preg_replace(',(<[^<>]*)@,ms', '\1-', $c);

	$x = '';
	$c = preg_split(",@([^<>]*?[:>]),ms", $c, -1, PREG_SPLIT_DELIM_CAPTURE);


	$d = '';
	unset($c[0]); // ignore le premier terme
	foreach ($c as $cpt => $chaine) {
		switch ($cpt%2) {
			case 1:	// marqueur
				if (preg_match(',(.*?):,', $chaine, $regs))
					$var = $regs[1];
				else
					unset($var);
				break;

			case 0:	// valeur
				if (isset($var) AND strlen($chaine))
					$d .= ligne_xtag($var,$chaine);
				break;
		}
	}

	return $d;
}

function nettoyage_xtag($c) {

	// fines, insecables
	$c = str_replace('<\\!s>', '~', $c);
	$c = str_replace('<\\!f>', '~', $c);
	$c = str_replace('<\\n>', '~', $c);
	$c = str_replace('<\\!e>', ' ', $c);

	// un <parastyle...> = un paragraphe
	$c = preg_replace ('/<P(ara)?Style:([A-Z]*) [^>]*>/ims', "\n/\\2/", $c);

	// les zitaliques
	$c = preg_replace ('/<cTypeface:Italic>(.*?)<cTypeface:>/ims', '{\1}', $c);
	$c = preg_replace ('/<[$]?f[^<>]+Italic[^<>]+>(.*?)<f[$]>/', '{\1}', $c);
	$c = preg_replace ('/<It(-\d+)?>/', '<I>', $c);
	$c = preg_replace ('/<I>(.*?)<[$I]>/', '{\1}', $c);
	$c = preg_replace ('/<I>(.*?)(\n|$)/', '{\1}\2', $c);
	$c = preg_replace ('/{(\s*)}/ms', '\1', $c);

	// supprimer un sale petit tiret
	$c = str_replace("&#173;", '', $c);

	// backtick - http://www.fileformat.info/info/unicode/char/2018/index.htm
	$c = str_replace("&#145;", '&#8216;', $c);

	// paras simples
	$c = preg_replace(",\n+,", "\n\n", $c);

	// co case = majuscules
	$c = preg_replace('/<Ko[(]"case","cpsp"[)]>([^<>]+)/imse', 'mb_strtoupper("\1")', $c);

	// virer tous les tags restants
	$c = strip_tags($c);

	// insecables zarbis
	$c = str_replace (' >', '~', $c);

	// espaces en italique ou en romain
	$c = preg_replace(',[{] *~ *[}],', '~', $c);
	$c = preg_replace(',[}] *~ *[{],', '~', $c);
	$c = preg_replace(',[{] +[}],', ' ', $c);
	$c = preg_replace(',[}] +[{],', ' ', $c);
	$c = preg_replace(',([ ~])[}],', '}\1', $c);
	$c = preg_replace(',[{]([ ~]),', '\1{', $c);
	$c = preg_replace(',[ ~]?([{]»),', '{»', $c);
	$c = preg_replace(',[{][}]|[}][{],', '', $c);

	// espaces multiples
	$c = preg_replace(",  +,", " ", $c);

	// fin d'italique ponctuation
	$c = preg_replace('/([{}])([.,] *)/', '\2\1', $c);


	#echo "<pre>".htmlspecialchars($c).'</pre>';

	return $c;
}

// pas specifique a quark : fonction nommee ainsi pour eventuelle integration
// a inc/charsets
function quark_bom_detect($t) {
	foreach (array(
		chr(0x00).chr(0x00).chr(0xFE).chr(0xFF) => 'UTF-32BE',
		chr(0xFF).chr(0xFE).chr(0x00).chr(0x00) => 'UTF-32LE',
		chr(0xFE).chr(0xFF) => 'UTF-16BE',
		chr(0xFF).chr(0xFE) => 'UTF-16LE',
		chr(0xEF).chr(0xBB).chr(0xBF) => 'utf-8'
	) as $bom => $charset)
		if (strpos($t, $bom)===0)
			return $charset;
}



function extracteur_quark($fichier, &$charset) {
	if (lire_fichier($fichier, $texte)) {

		if ($c = quark_bom_detect($texte)
		AND $c != 'utf-8'
		AND init_mb_string())
			$texte = mb_convert_encoding($texte, 'utf-8', $c);

		if (is_utf8($texte))
			$charset = 'utf-8';

		return do_quark($texte);
	}
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['quark'] = 'extracteur_quark';

?>
