<?php


/**
 * Gestion d'export de données au format CSV
 *
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/charsets');
include_spip('inc/filtres');
include_spip('inc/texte');

require_once find_in_path('lib/Spout/Autoloader/autoload.php');
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;

/**
 * Exporte une ressource sous forme de fichier CSV
 *
 * La ressource peut etre un tableau ou une resource SQL issue d'une requete
 * L'extension est choisie en fonction du delimiteur :
 * - si on utilise ',' c'est un vrai csv avec extension csv
 * - si on utilise ';' ou tabulation c'est pour E*cel, et on exporte avec une extension .xlsx
 *
 * @uses exporter_csv_ligne()
 *
 * @param string $titre
 *   titre utilise pour nommer le fichier
 * @param array|resource $resource
 * @param string $delim
 *   delimiteur
 * @param array $entetes
 *   tableau d'en-tetes pour nommer les colonnes (genere la premiere ligne)
 * @param bool $envoyer
 *   pour envoyer le fichier exporte (permet le telechargement)
 * @return string
 */
function inc_exporter_csv_dist($titre, $resource, $delim = ', ', $entetes = null, $envoyer = true) {

	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

	if ($delim == 'TAB') {
		$delim = "\t";
	}
	if (!in_array($delim, array(',', ';', "\t"))) {
		$delim = ',';
	}
	
	$charset = $GLOBALS['meta']['charset'];
	$folder = sous_repertoire(_DIR_CACHE, 'export');
	if ($delim == ',') {
		$extension = 'csv';
		$writer = WriterEntityFactory::createWriterFromFile("$folder$filename.$extension");
		$writer->openToFile("$folder$filename.$extension");
	} else {
		$extension = 'xlsx';
		$defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
                ->build();
		$writer = WriterEntityFactory::createXLSXWriter();
		$writer->setTempFolder(sous_repertoire(_DIR_CACHE, 'export'));
		$writer->setDefaultRowStyle($defaultStyle)
		       ->openToFile("$folder$filename.$extension");
	}
	$filename = "$filename.$extension";

	if ($entetes and is_array($entetes) and count($entetes)) {
		$writer->addRow(WriterEntityFactory::createRowFromArray($entetes));
	}

	// on passe par un fichier temporaire qui permet de ne pas saturer la memoire
	// avec les gros exports
	$fichier = $folder . $filename;

	while ($row = is_array($resource) ? array_shift($resource) : sql_fetch($resource)) {
		$writer->addRow(WriterEntityFactory::createRowFromArray($row));
	}
	
	$writer->close();
	
	if ($envoyer) {
		header("Content-Type: text/comma-separated-values; charset=$charset");
		header("Content-Disposition: attachment; filename=$filename");
		//non supporte
		//Header("Content-Type: text/plain; charset=$charset");
		header("Content-Length: $length");
		ob_clean();
		flush();
		readfile($fichier);
	}

	return $fichier;
}
