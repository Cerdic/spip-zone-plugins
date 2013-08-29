<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_ocr_analyse_document_dist($t) {
	//Recuperation de la configuration
	$ocr = @unserialize($GLOBALS['meta']['ocr']);
	if(is_array($ocr)){
		//Nombre de documents traites par iteration
		$nb_docs = $ocr['nb_docs'] ? $ocr['nb_docs'] : @define('_OCR_NB_DOCS',5);
		if ($docLists = sql_select("id_document,fichier", "spip_documents", "ocr_analyse = 'non'", "", "maj", "0,".intval($nb_docs+1))) {
			while($nb_docs-- AND $row = sql_fetch($docLists)) {
				$doc = $row['fichier'];
				$id_document = $row['id_document'];
	      		spip_log('Génie - Analyse OCR de '.$doc, 'ocr');
				if (include_spip('inc/ocr_analyser')) {
					ocr_analyser($id_document);
				}
			}
			if ($row = sql_fetch($docLists)){
				spip_log("il reste des docs a indexer...", 'ocr');
				return 0-$t; // il y a encore des docs a indexer
			}
		}
	}
	return 0;
}
?>
