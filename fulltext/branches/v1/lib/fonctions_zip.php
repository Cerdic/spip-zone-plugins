<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//Fonctions necessaires pour l'extraction des fichiers .odt/.docx
// Necessite PHP 5.2+
// Necessite php_zip.dll sous Winwows
// Necessite -le paramètre -enable-zip pour Linux.
// Si vous ne pouvez pas utiliser ZipArchive (librairies manquantes ou vieille version de PHP), vous pouvez utiliser la librairie PclZip ( http://www.phpconcept.net/pclzip )

function docx2text($filename) {
	return readZippedXML($filename, "word/document.xml");
}

function odt2text($filename) {
	return readZippedXML($filename, "content.xml");
}

function readZippedXML($archiveFile, $dataFile) {
	// Creation d'une archive ZIP
	$zip = new ZipArchive;

	// Ouvrir l'archive ZIP
	if (true === $zip -> open($archiveFile)) {
		// Si c'est bon, rechercher du fichier de donnees (passe en argument) dans l'archive.
		if (($index = $zip -> locateName($dataFile)) !== false) {
			//Si le fichier est trouve, lire les chaines de caracteres.
			$data = $zip -> getFromIndex($index);
			// Fermez le zip
			$zip -> close();
			// Charger le XML d'une chaine de caractere
			// Passez les errors et les warnings
			$xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
			// Renvoyer les donnees  sans les tags XML de formatage
			return strip_tags($xml -> saveXML());
		}
		$zip -> close();
	}

	// En cas d'echec, on renvoit une chaine vide
	return "";
}
?>
