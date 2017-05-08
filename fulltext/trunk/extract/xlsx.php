<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['xlsx'] = 'extracteur_xlsx';

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1
// Cet extracteur se base sur le fait que les fichiers .xlsx sont en fait des archives, dont la configurations et les contenus sont enregistres dans des fichiers .xml
// Necessite PHP 5.2+
// Necessite php_zip.dll sous Winwows
// Necessite le parametre -enable-zip pour Linux.
// Si vous ne pouvez pas utiliser ZipArchive (librairies manquantes ou vieille version de PHP), vous pouvez utilisez la librairie PclZip ( http://www.phpconcept.net/pclzip )
// Necessite lib/simplexlsx.class.php
// NOTE : l'enregistrement se fait en "csv-like" en base avec double guillemet (") autour des colonnes et la virgule (,) comme caractere de séparation.
// Ce n'est pas forcemment l'ideal mais l'indexation semble fonctionner

// https://code.spip.net/@extracteur_docx

function extracteur_xlsx($fichier, &$charset, $bin = '', $opt = '') {
	$charset = 'UTF-8';
	//On lis des XML en utf-8
	$texte = '';
	//Initialiser a vide
	if (include_spip('lib/simplexlsx.class')) {
		spip_log('Extraction XLSX avec class simplexlsx', 'extract');
		$xlsx = new SimpleXLSX($fichier);
		//Combien on a de feuilles ?
		$nb_sheets = $xlsx -> sheetsCount();

		//Parcours des feuilles
		for ($i = 1; $i <= $nb_sheets; $i++) {
			list($num_cols, $num_rows) = $xlsx -> dimension($i);
			//Parcours des lignes/colonnes de la feuille
			foreach ($xlsx->rows($i) as $r) {
				for ($j = 0; $j < $num_cols; $j++) {
					$texte .= '"' . $r[$j] . '", ';
					//NOTE : formatage "CSV-like", ce qui n'est peut etre pas l'ideal
				}
			}
		}
		//Sinon non-vide, on envoie
		if ($texte != '') {
			spip_log('Extraction XLSX de ' . $fichier . ' terminee avec succes', 'extract');
			return $texte;
		} else {
			spip_log('Extraction XLSX de ' . $fichier . ' a echoue', 'extract');
			return false;
		}
	} else {
		spip_log('Extraction XLSX a echoue : la class SimpleXLSX (lib/simplexlsx.class.php) ne semble pas etre disponible', 'extract');
		return false;
	}
}
