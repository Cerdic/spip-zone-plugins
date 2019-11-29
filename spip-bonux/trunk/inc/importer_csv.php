<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Tetue
 * Licence GPL
 *
 * Fonctions de lecture d'un fichier CSV pour transformation en array()
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/charsets');

/**
 * Based on an example by ramdac at ramdac dot org
 * Returns a multi-dimensional array from a CSV file optionally using the
 * first row as a header to create the underlying data as associative arrays.
 *
 * @param string $file Filepath including filename
 * @param bool $head Use first row as header.
 * @param string $delim Specify a delimiter other than a comma.
 * @param int $len Line length to be passed to fgetcsv
 * @return array or false on failure to retrieve any rows.
 */

/**
 * Importer le charset d'une ligne
 *
 * Importe un texte de CSV dans un charset et le passe dans le charset du site (utf8 probablement)
 * Les CSV peuvent sous ms@@@ avoir le charset 'iso-8859-1', mais pasfois aussi 'windows-1252' :/
 *
 * @param mixed $texte
 * @param bool|string $definir_charset_source
 *     false : ne fait rien
 *     string : utilisera pour les prochains imports le charset indiqué
 *     true : remet le charset d'import par défaut de la fonction
 * @return array
 */
function importer_csv_importcharset($texte, $definir_charset_source = false) {
	// le plus frequent, en particulier avec les trucs de ms@@@
	static $charset_source = 'iso-8859-1';
	if ($definir_charset_source) {
		if ($definir_charset_source === true) {
			$charset_source = 'iso-8859-1';
		} else {
			$charset_source = $definir_charset_source;
		}
	}
	// mais open-office sait faire mieux, donc mefiance !
	if (is_utf8($texte)) {
		$charset = 'utf-8';
	} else {
		$charset = $charset_source;
	}
	return importer_charset($texte, $charset);
}

/**
 * enlever les accents des cles presentes dans le head,
 * sinon ca pose des problemes ...
 *
 * @param string $key
 * @return string
 */
function importer_csv_nettoie_key($key) {
	return translitteration($key);
}

/**
 * Lit un fichier csv et retourne un tableau
 * si $head est true, la premiere ligne est utilisee en header
 * pour generer un tableau associatif
 *
 * @param string $file
 * @param bool $head
 * @param string $delim
 * @param string $enclos
 * @param int $len
 * @param string $charset_source
 *     Permet de définir un charset source du CSV, si différent de utf-8 ou iso-8859-1
 * @return array
 */
function inc_importer_csv_dist($file, $head = false, $delim = ',', $enclos = '"', $len = 10000, $charset_source = '') {
	$return = false;
	if (@file_exists($file)
		and $handle = fopen($file, 'r')) {
		if ($charset_source) {
			importer_csv_importcharset('', $charset_source);
		}
		if ($head) {
			$header = fgetcsv($handle, $len, $delim, $enclos);
			if ($header) {
				$header = array_map('importer_csv_importcharset', $header);
				$header = array_map('importer_csv_nettoie_key', $header);
				$header_type = array();
				foreach ($header as $heading) {
					if (!isset($header_type[$heading])) {
						$header_type[$heading] = "scalar";
					} else {
						$header_type[$heading] = "array";
					}
				}
			}
		}
		while (($data = fgetcsv($handle, $len, $delim, $enclos)) !== false) {
			$data = array_map('importer_csv_importcharset', $data);
			if ($head and isset($header)) {
				$row = array();
				foreach ($header as $key => $heading) {
					if ($header_type[$heading] == "array") {
						if (!isset($row[$heading])) {
							$row[$heading] = array();
						}
						if (isset($data[$key]) and strlen($data[$key])) {
							$row[$heading][] = $data[$key];
						}
					} else {
						$row[$heading] = (isset($data[$key])) ? $data[$key] : '';
					}
				}
				$return[] = $row;
			} else {
				$return[] = $data;
			}
		}
		if ($charset_source) {
			importer_csv_importcharset('', true);
		}
	}

	return $return;
}
