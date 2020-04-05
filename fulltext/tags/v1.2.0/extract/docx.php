<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les classes fonctionnent
$GLOBALS['extracteur']['docx'] = 'extracteur_docx';

/**
 * NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
 * le charset du site, mais il *doit* signaler le charset dans lequel
 * il envoie le contenu, de facon a ce qu'il soit converti au moment
 * voulu ; dans le cas contraire le document sera lu comme s'il etait
 * dans le charset iso-8859-1
 * Cet extracteur se base sur le fait que les fichiers .odt/.docx sont en fait des archives,
 * dont la configurations et les contenus sont enregistres dans des fichiers .xml
 * Necessite PHP 5.2+
 * Necessite php_zip.dll sous Windows
 * Necessite le parametre -enable-zip pour Linux.
 * Si vous ne pouvez pas utiliser ZipArchive (librairies manquantes ou vieille version de PHP),
 * vous pouvez utilisez la librairie PclZip ( http://www.phpconcept.net/pclzip )
 * Necessite lib/fonctions_zip.php
 */
function extracteur_docx($fichier, &$charset, $bin = '', $opt = '') {
	if (include_spip('lib/fonctions_zip')) {
		$charset = 'UTF-8';
		//On lis des XML en utf-8
		spip_log('Extraction DOCX avec PHP 5.2+', 'extract');
		//On mets le retour à vide
		$texte = '';
		//Utilisation de la fonction docx2text, renvoyant une chaine vide en cas d'erreur
		$texte = docx2text($fichier);
		//Test si le retour n'est plus vide
		if (($texte) && ($texte != '')) {
			spip_log('Extraction DOCX de ' . $fichier . ' terminee avec succes', 'extract');
			return $texte;
		} else {
			spip_log('Erreur extraction DOCX : Le fichier texte n\'existe pas ou n\'est pas lisible.', 'extract');
			return false;
		}
	} else {
		return false;
	}
}
