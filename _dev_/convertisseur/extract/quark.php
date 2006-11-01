<?php

function do_quark($c) {

	// transformer les <0x2014> en &#2014;
	$c = preg_replace_callback("/<0x([0-9A-F]+)>/",
		create_function ('$m',
			'return "&#".hexdec($m[1]).";";'),
		$c);

	// virer les sauts de ligne du fichier original
	$c = str_replace("\n", " ", $c);
	$c = str_replace("\r", " ", $c);

	// un <parastyle...> = un paragraphe
	$c = preg_replace ('/<P(ara)?Style:([A-Z]*) [^>]*>/ims', "\n/\\2/", $c);

	// les zitaliques
	$c = preg_replace ('/<cTypeface:Italic>(.*?)<cTypeface:>/ims', '{\1}', $c);

	// virer tous les tags restants
	$c = preg_replace ('/<([^<>]|<[^>]*>)*>/ims', '', $c);

	// supprimer un sale petit tiret
	$c = str_replace("&#173;", '', $c);

	// backtick - http://www.fileformat.info/info/unicode/char/2018/index.htm
	$c = str_replace("&#145;", '&#8216;', $c);

	// Maintenant on repre les diffŽrents types de para (\n\n/TYPE/contenu)
	$paragraphes = preg_split ("@\n/@", $c);
	$champs = array();
	foreach ($paragraphes as $para) {
		if (preg_match('@([A-Z]+)/(.*)@ims', $para, $regs))
			$champ[$regs[1]] .= $regs[2]."\n\n";
	}

	if ($champ)
		return join("\n\n----\n\n", array_map('trim',$champ));
}


function extracteur_quark($fichier, &$charset) {
	if (lire_fichier($fichier, $texte))
		return do_quark($texte);
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['quark'] = 'extracteur_quark';

?>
