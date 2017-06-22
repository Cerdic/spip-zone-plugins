<?php

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}


function article_pdf_build_pdf($flux){
	
	//on vérifie que la demande de lier le pdf est configurée
	
	$id_objet = $flux['args']['id_objet'];
	$objet = $flux['args']['objet'];
	$file_out = $flux['data'];
	
	if (file_exists($file_out)) {
			include_spip('inc/config');
			$pdf_to_document = lire_config('article_pdf/pdf_to_document');
	}
	
	spip_log("Traitement = ".$pdf_to_document." ? avec récupération de ".$objet.$id_objet."chemin du pdf =".$file_out,'article_pdf_build_pdf');
	
	return $flux;
}