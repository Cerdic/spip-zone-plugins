<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

require_once find_in_path('lib/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;

function action_formidable_tablesorter_export() {
	$data = json_decode(_request('data'));
	$type_export = _request('type_export');
	$style_entete = (new StyleBuilder())
		->setFontBold()
		->build();
	if ($type_export == 'csv') {
		$writer = WriterEntityFactory::createCSVWriter();
	} elseif ($type_export == 'ods') {
		$writer = WriterEntityFactory::createODSWriter();
	} else {
		$writer = WriterEntityFactory::createXLSXWriter();
	}
	$writer->openToBrowser("export.$type_export");
	//
	$i = 0;
	foreach ($data as $row => $content) {
		$i++;
		if ($i == 1 and $type_export != 'csv') {
			$writer->addRow(WriterEntityFactory::createRowFromArray($content, $style_entete));
		} else {
			$writer->addRow(WriterEntityFactory::createRowFromArray($content));
		}
	}
	$writer->close();
}
