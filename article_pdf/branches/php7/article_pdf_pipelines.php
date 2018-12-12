<?php

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/*
 * Pipeline créé dans article_pdf.html
 *  		
 		$data = pipeline('build_pdf',array(
			'args'=>array(
				'objet' 	=> 'article',
				'id_objet' 	=> $id_article,
				'file_name' => $files_pdf
			), 
			'data'=> $file_out,
    	));
		
 *
 *
*/
function article_pdf_build_pdf($flux){
	
	// le pdf vient d'être créée
	// il existe et la demande de lier le pdf est configurée
	$file_out = $flux['data'];
	$pdf_to_document = false;
	
	if (file_exists($file_out)) {
		include_spip('inc/config');
		$pdf_to_document = lire_config('article_pdf/pdf_to_document');
	}
	
	if($pdf_to_document == 'oui'){
		
		include_spip('inc/autoriser');
		include_spip('inc/modifier');
		include_spip('action/editer_document');
		include_spip('action/ajouter_documents');
	
		$objet = $flux['args']['objet'];
		$id_objet = $flux['args']['id_objet'];
		$titre_pdf = $flux['args']['file_name'];
		
		$file['name'] = strtolower(translitteration($titre_pdf));

		//chercher le document correspondant, pour le modifier sinon le créer
		$id_document = sql_getfetsel('id_document','spip_documents','fichier LIKE '.sql_quote('%pdf/'.$file['name']));
		spip_log("DEJJJJJJAAAAA * out est $file_out * fichier $titre_pdf devient ".'pdf/'.$file['name'] ." id_document =".$id_document, 'article_pdf_build_pdf');
		
		$file['extension'] 	= 'pdf';
		$file['tmp_name'] 	= $file_out;
		$file['mode']		= 'document';
		
		if(! $id_document){
			$id_document = "new";
		}
		
		// donner une autorisation exceptionnelle temporaire
		autoriser_exception('associerdocuments', $objet, $id_objet);

		$ajouter_un_document = charger_fonction('ajouter_un_document', 'action');
		$id_document_ajout = $ajouter_un_document($id_document, $file, $objet, $id_objet, 'document');

		spip_log("fichier= $fichier Traitement = ".$pdf_to_document." ? avec récupération de ".$objet.$id_objet." chemin du pdf = ".$file_out,'article_pdf_build_pdf');
		autoriser_exception('associerdocuments', $objet, $id_objet, false);
	}
	
	if($id_document_ajout > 0){
		$id_document 		= $id_document_ajout;
		$champs['credits'] 	= 'Extraction PDF [->'.$objet.$id_objet.']';
			
		// donner une autorisation exceptionnelle temporaire
		autoriser_exception('modifier', 'document', $id_document);
		// réaliser l'action désirée
		document_modifier($id_document, $champs);
		// retirer l'autorisation exceptionnelle
		autoriser_exception('modifier', 'document', $id_document, false);
		
		$flux['data'] = $id_document;
		
	} else {
		$erreur = _T('medias:erreur_insertion_document_base', array('fichier' => "<em>" . $file_out . "</em>"));
		spip_log("article_pdf $erreur", "article_pdf_build_pdf");
	}
	
	return $flux;
}