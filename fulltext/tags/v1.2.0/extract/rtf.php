<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['rtf'] = 'extracteur_rtf';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1

// https://code.spip.net/@extracteur_rtf
function extracteur_rtf($fichier, &$charset) {

	$charset = 'iso-8859-1';

	@exec('metamail -d -q -b -c application/rtf ' . escapeshellarg($fichier), $r, $e);
	if (!$e) {
		return @join(' ', $r);
	}

	# wvText
	# http://wvware.sourceforge.net/
	$temp = tempnam(_DIR_CACHE, 'rtf');
	@exec('wvText ' . escapeshellarg($fichier) . '> ' . $temp, $r, $e);
	lire_fichier($temp, $contenu);
	@unlink($temp);
	if (!$e) {
		return $contenu;
	}

	# unrtf
	# http://www.gnu.org/software/unrtf/unrtf.html
	# --html car avec --text les accents sont perdus :(
	exec($c = 'unrtf --html ' . escapeshellarg($fichier), $r, $e);
	if (!$e) {
		$html = join(' ', $r);
		$html = importer_charset($html, 'utf-8');
		include_spip('inc/sale');
		$a = sale($html);
		$a = preg_replace(',</?font\b[^>]*>,i', '', $a);
		$a = supprimer_tags($a);
		$a = preg_replace("/\n_ /", "\n\n", $a);
		$a = preg_replace("/{{([^}]+?)\n* *}}/", '{{{ $1 }}}', $a);
		$a = preg_replace('/^ +/m', '', $a);
		$a = preg_replace('/^ +/m', '', $a);
		$a = str_replace('``', '&#8220;', $a);
		$a = str_replace("''", '&#8221;', $a);
		$a = str_replace('&ndash;', '--', $a);
		$a = preg_replace("/`(.*?)'/S", '&#8216;\1&#8217;', $a);
		$a = preg_replace("/}}} +\r/S", "}}}\r", $a);
		$a = str_replace('&hellip;', '...', $a);
		$charset = 'utf-8';
		return $a;
	}

	# catdoc
	# http://www.45.free.net/~vitus/ice/catdoc/
	@exec('catdoc ' . escapeshellarg($fichier), $r, $e);
	if (!$e) {
		return join(' ', $r);
	}
}
