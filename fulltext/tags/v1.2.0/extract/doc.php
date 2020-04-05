<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['doc'] = 'extracteur_doc';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1
// Extracteurs basee sur la librairie catdoc (a definir dans _FULLTEXT_DOC_EXE dans mes_options.php ou via le panneau de configuration) : http://www.wagner.pp.ru/~vitus/software/catdoc/  (version Windows : http://blog.brush.co.nz/2009/09/catdoc-windows/ )
// Exemple pour utilisation en local sous Windows : define("_FULLTEXT_DOC_EXE","C:\catdoc\catdoc.exe");
// Exemple pour utilisation sous Linux : define("_FULLTEXT_DOC_EXE","/usr/local/bin/catdoc");
// Exemple d'option pour extraction de .doc au format Windows vers format iso-8859-1  : define("_FULLTEXT_DOC_CMD_OPTIONS","-s cp1252 -d 8859-1 ");
// Les anciens developpements pour les autres librairies (metamail, wvText, antiword) ont ete conservee.
// https://code.spip.net/@extracteur_doc
function extracteur_doc($fichier, &$charset, $bin = '', $opt = '') {

	$charset = 'iso-8859-1';
	if ((defined('_FULLTEXT_DOC_EXE'))||($bin)) {
		$exe = $bin ? $bin : _FULLTEXT_DOC_EXE;
	} else {
		// TODO : essayer de trouver tout seul l'executable
		spip_log('Erreur extraction DOC : Il faut specifier _FULLTEXT_DOC_EXE dans mes_options.php ou dans le panneau de configuration');
		return false;
	}
	if ((defined('_FULLTEXT_DOC_CMD_OPTIONS') && '' != _FULLTEXT_DOC_CMD_OPTIONS)||($opt)) {
		$options = $opt ? ' '.$opt.' ' : ' '._FULLTEXT_DOC_CMD_OPTIONS.' ';
	} else {
		$options = ' ';
	}

	spip_log('Extraction DOC avec '.$exe, 'extract');
	$cmd = $exe.$options.$fichier;
	$sortie = exec($cmd, $output, $return_var);
	if ($return_var != 0) {
		if ($return_var == 3) {
			$erreur = 'Le contenu de ce fichier DOC est protégé.';
			spip_log('Erreur extraction '.$fichier.' protege (code '.$return_var.') : '.$erreur, 'extract');
			return $return_var;
		} else {
			spip_log('Erreur extraction '.$fichier.' (code '.$return_var.') : '.$erreur, 'extract');
			return false;
		}
	} else {
		//Go
		spip_log('Fichier DOC '.$fichier.' a ete extrait avec '.$options, 'extract');
		foreach ($output as $out) {
			$texte .= $out."\n";
		}
		return $texte;
	}

	//Anciens developpements pour autres binaires que catdoc. Antiword devrait fonctionner avec le code ci-dessus egalement.
	#metamail
	@exec('metamail -d -q -b -c application/msword '.escapeshellarg($fichier), $r, $e);
	if (!$e) {
		return @join(' ', $r);
	}

	# wvText
	# http://wvware.sourceforge.net/
	$temp = tempnam(_DIR_CACHE, 'doc');
	@exec('wvText '.escapeshellarg($fichier).'> '.$temp, $r, $e);
	lire_fichier($temp, $contenu);
	@unlink($temp);
	if (!$e) {
		return $contenu;
	}

	# antiword
	# http://www.winfield.demon.nl/
	@exec('antiword '.escapeshellarg($fichier), $r, $e);
	if (!$e) {
		return @join(' ', $r);
	}
}
