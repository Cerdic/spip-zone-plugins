<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['ppt'] = 'extracteur_ppt';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1
// Extracteur basee sur la librairie catdoc : http://www.wagner.pp.ru/~vitus/software/catdoc/ (version Windows : http://blog.brush.co.nz/2009/09/catdoc-windows/ )
// Necessite catppt de la librairie catdoc (à définir dans _FULLTEXT_PPT_EXE dans mes_options.php ou dans le panneau de configuration) :
// Exemple pour utilisation en local sous Windows : define("_FULLTEXT_PPT_EXE","C:\catdoc\catppt.exe");
// Exemple pour utilisation sous Linux : define("_FULLTEXT_PPT_EXE","/usr/local/bin/catppt");
// https://code.spip.net/@extracteur_ppt
function extracteur_ppt($fichier, &$charset, $bin = '', $opt = '') {
	$charset = 'iso-8859-1';

	$texte = '';
	$output = array();
	if ((defined('_FULLTEXT_PPT_EXE')) || ($bin)) {
		$exe = $bin ? $bin : _FULLTEXT_PPT_EXE;
	} else {
		// TODO : essayer de trouver tout seul l'exécutable
		spip_log('Erreur extraction PPT : Il faut specifier _FULLTEXT_PPT_EXE dans mes_options.php ou utiliser le panneau de configuration');
		return false;
	}
	if ((defined('_FULLTEXT_PPT_CMD_OPTIONS') && '' != _FULLTEXT_PPT_CMD_OPTIONS) || ($opt)) {
		$options = $opt ? ' ' . $opt . ' ' : ' ' . _FULLTEXT_PPT_CMD_OPTIONS . ' ';
	} else {
		$options = ' ';
	}
	$cmd = $exe . $options . $fichier;
	spip_log('Extraction PPT avec ' . $exe . ' (' . $cmd . ')', 'extract');
	$sortie = exec($cmd, $output, $return_var);
	if ($return_var != 0) {
		if ($return_var == 3) {
			$erreur = 'Le contenu de ce fichier PPT est protégé.';
			spip_log('Erreur extraction ' . $fichier . ' protege (code ' . $return_var . ') : ' . $erreur, 'extract');
			return $return_var;
		} else {
			spip_log('Erreur extraction ' . $fichier . ' (code ' . $return_var . ') : ' . $erreur, 'extract');
			return false;
		}
	} else {
		spip_log('Fichier PPT ' . $fichier . ' a ete extrait avec ' . $options, 'extract');
		foreach ($output as $out) {
			$texte .= $out . "\n";
		}
		return $texte;
	}
}
