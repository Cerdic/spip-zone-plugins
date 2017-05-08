<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['xls'] = 'extracteur_xls';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1
// Extracteur basee sur la librairie catdoc : http://www.wagner.pp.ru/~vitus/software/catdoc/ (version Windows : http://blog.brush.co.nz/2009/09/catdoc-windows/ )
// Necessite xls2csv de la librairie catdoc (à définir dans _FULLTEXT_XLS_EXE dans mes_options.php ou dans le panneau de configuration) :
// Exemple pour utilisation en local sous Windows : define("_FULLTEXT_XLS_EXE","C:\catdoc\xls2csvt.exe");
// Exemple pour utilisation sous Linux : define("_FULLTEXT_XLS_EXE","/usr/local/bin/xls2csv");
// Exemple  d'option pour extraction de .xls au format Windows vers format iso-8859-1  : define("_FULLTEXT_XLS_CMD_OPTIONS","-s cp1252 -d 8859-1 ");
// NOTE : l'enregistrement se fait en "csv-like" en base avec double guillemet (") autour des colonnes et la virgule (,) comme caractere de separation.
// Ce n'est pas forcemment l'ideal mais l'indexation semble fonctionner

// https://code.spip.net/@extracteur_xls
function extracteur_xls($fichier, &$charset, $bin = '', $opt = '') {
	$charset = 'iso-8859-1';

	$texte = '';
	$output = array();
	if ((defined('_FULLTEXT_XLS_EXE')) || ($bin)) {
		$exe = $bin ? $bin : _FULLTEXT_XLS_EXE;
	} else {
		// TODO : essayer de trouver tout seul l'executable
		spip_log('Erreur extraction XLS : Il faut spécifier _FULLTEXT_XLS_EXE dans mes_options.php ou dans le panneau de configuration');
		return false;
	}
	if ((defined('_FULLTEXT_XLS_CMD_OPTIONS') && '' != _FULLTEXT_XLS_CMD_OPTIONS) || ($opt)) {
		$options = $opt ? ' ' . $opt . ' ' : ' ' . _FULLTEXT_XLS_CMD_OPTIONS . ' ';
	} else {
		$options = ' ';
	}
	$cmd = $exe . $options . $fichier;
	spip_log('Extraction XLS avec ' . $exe . ' (' . $cmd . ')', 'extract');
	$sortie = exec($cmd, $output, $return_var);
	if ($return_var != 0) {
		if ($return_var == 69) {
			$erreur = 'Le contenu de ce fichier XLS est protégé.';
			spip_log('Erreur extraction ' . $fichier . ' protege (code ' . $return_var . ') : ' . $erreur, 'extract');
			$return_var = 3;
			return $return_var;
		} else {
			spip_log('Erreur extraction ' . $fichier . ' (code ' . $return_var . ') : ' . $erreur, 'extract');
			return false;
		}
	} else {
		spip_log('Fichier XLS ' . $fichier . ' a ete extrait avec ' . $options, 'extract');
		foreach ($output as $out) {
			$texte .= $out . "\n";
		}
		return $texte;
	}
}
