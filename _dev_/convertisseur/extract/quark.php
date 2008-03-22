<?php


 
function do_quark($c) {

	// transformer les <0x2014> en &#2014;
	$c = preg_replace_callback("/<0x([0-9A-F]+)>/",
		create_function ('$m',
			'return "&#".hexdec($m[1]).";";'),
		$c);

	// transformer les sauts de ligne du fichier original
	$c = preg_replace(",\r\n?,", "\n", $c);

	$x = '';
	$c = preg_split(",@(.*?[:>]),ms", $c, -1, PREG_SPLIT_DELIM_CAPTURE);

	$d = array(); // on va le remplir
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
					$d[] = trim('* '.$var."\n\n".$chaine);
				break;
		}
	}

	$c = (join("\n\n", $d));

	// fines, insecables
	$c = str_replace('<\\!s>', '~', $c);
	$c = str_replace('<\\!f>', '~', $c);


	// un <parastyle...> = un paragraphe
	$c = preg_replace ('/<P(ara)?Style:([A-Z]*) [^>]*>/ims', "\n/\\2/", $c);

	// les zitaliques
	$c = preg_replace ('/<cTypeface:Italic>(.*?)<cTypeface:>/ims', '{\1}', $c);
	$c = preg_replace ('/<I>(.*?)<[$]>/ms', '{\1}', $c);

	// supprimer un sale petit tiret
	$c = str_replace("&#173;", '', $c);

	// backtick - http://www.fileformat.info/info/unicode/char/2018/index.htm
	$c = str_replace("&#145;", '&#8216;', $c);

	// paras simples
	$c = preg_replace(",\n+,", "\n\n", $c);

	// espaces multiples
	$c = preg_replace(",  +,", " ", $c);

	// virer tous les tags restants
	$c = strip_tags($c);

	// insecables zarbis
	$c = str_replace (' >', '~', $c);

	return $c;
}


function extracteur_quark($fichier, &$charset) {
	if (lire_fichier($fichier, $texte)) {
		if (is_utf8($texte))
			$charset = 'utf-8';

		return do_quark($texte);
	}
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['quark'] = 'extracteur_quark';

?>
