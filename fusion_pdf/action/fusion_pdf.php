<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_fusion_pdf_dist($arg=null) {	
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
 	
	// On recupere les infos de l'argument
	@list($path_parent,$interval,$prefix,$titre_sale,$objet,$id_objet) = explode('//', $arg);
	$id_objet = intval($id_objet);
	
	spip_log("fusion pour pdf $path_parent, $interval,$prefix,$titre_sale,$objet,$id_objet",'fusionpdf');

	// todo autoriser('fusion_pdf', 'article',$arg[1]))
	if ($path_parent && $interval!=' ') {
		$fusion=fusion_pdf_post($path_parent, $interval,$prefix, $titre_sale,$objet,$id_objet);
	}
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_$objet/$id_objet'");	

}


function fusion_pdf_post($path_parent, $interval, $prefix,$titre_sale,$objet,$id_objet){
	
	$fichier_propre=titrature($titre_sale,$prefix).'.pdf';
	$outputpdf=_DIR_IMG.'/pdf/'.$fichier_propre;
	
	spip_log("fusionpost2 generer $outputpdf a partir de $path_parent",'fusionpdf');	

	
	//verifier que ce n'est pas encore dans la base
	if ($doc = sql_fetsel('id_document', 'spip_documents', 'fichier='.$outputpdf." AND id_$objet=".$id_objet))
	return false;
	
	//on extrait les pages et on fusionne
	fusionner($path_parent,$outputpdf,$interval);
	
	$date_objet=sql_getfetsel('date',"spip_".$objet."s","id_$objet='$id_objet'");
		
	//preparer les champs
			$champs['date'] = $date_objet;
			$champs['fichier'] = $outputpdf;
			$champs['taille'] =  filesize($outputpdf);
			$champs['largeur'] = 0;
			$champs['hauteur'] = 0;
			$champs['mode'] = 'document';
			$champs['extension'] = 'pdf';
			$champs['statut'] = 'publie';
			
	//les inserer avec pipeline adequat	
	$id_document = insert_document($champs);

	if($id_document){
	
	//puis sauvegarder le document dans l'objet demande
	spip_log("insertion doc= $id_document",'fusionpdf');
		$document_lien = sql_insertq(
			'spip_documents_liens',
			array(
				'id_document'=>$id_document,
				'id_objet'=>$id_objet,
				'objet'=>$objet,
				'vu'=>'non'
			)
		);
	}

}


//verifier la ligne de modif dans fpdf pour le output
function fusionner($path_parent,$outputpdf,$interval) {
	// pour fusionner divers pdfs entre eux
	//le path_parent pourrait etre un tableau (cheminpdf1=>pages,cheminpdf2=>pages)
	if(include_once(find_in_path('lib/PDFMerger/PDFMerger.php'))){
	
	$pdf = new PDFMerger;
	$pdf->addPDF($path_parent,$interval)
		->merge('file', $outputpdf);			
	} else 	spip_log("librairie introuvable input= $path_parent output= $outputpdf",'fusionpdf');

	
}


/**
 * Cree un nouveau document et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par defaut lors de l'insertion
 * @return int id_document
 */
function insert_document($champs=array()) {
	$id_document = false;
	if (!$champs) return;

		// Envoyer aux plugins avant insertion
		$champs = pipeline('pre_insertion',
			array(
				'args' => array(
					'table' => 'spip_documents',
				),
				'data' => $champs
			)
		);
		// Inserer l'objet
		$id_document = sql_insertq('spip_documents', $champs);
		
		// Envoyer aux plugins apres insertion
		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_documents',
				),
				'data' => $champs
			)
		);

	return $id_document;
}


?>
