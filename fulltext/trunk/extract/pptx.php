<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['pptx'] = 'extracteur_pptx';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1
// Cet extracteur se base sur le fait que les fichiers .pptx sont en fait des conteneurs, dont la configurations et les contenus sont enregistres dans des fichiers .xml
// Necessite PHP 5.2+
// Necessite php_zip.dll sous Winwows
// Necessite le parametre -enable-zip pour Linux.
// Si vous ne pouvez pas utiliser ZipArchive (librairies manquantes ou vieille version de PHP), vous pouvez utilisez la librairie PclZip ( http://www.phpconcept.net/pclzip )
// Necessite lib/simplepptx.class.php
// https://code.spip.net/@extracteur_pptx

function extracteur_pptx($fichier, &$charset, $bin = '', $opt = '') {
	$charset = 'UTF-8';
	//On lis des XML en utf-8
	$texte = '';
	//Initialiser a vide
	if (include_spip('lib/simplepptx.class')) {
		spip_log('Extraction PPTX avec class simplepptx', 'extract');
		$pptx = new SimplePPTX($fichier, true);
		$texte = $pptx -> contenu;
		//Sinon non-vide, on envoie
		if ($texte != '') {
			spip_log('Extraction PPTX de ' . $fichier . ' terminae avec succas', 'extract');
			return $texte;
		} else {
			spip_log('Extraction PPTX de ' . $fichier . ' a echoua', 'extract');
			return false;
		}
	} else {
		spip_log('Extraction PPTX a echoue : : la class SimplePPTX (lib/simplepptx.class.php) ne semble pas etre disponible', 'extract');
		return false;
	}
}
