<?php

if (!defined('_ECRIRE_INC_VERSION')) return;
/*

[(#BOUTON_ACTION{<:fusionpdf:generez_le_pdf:>,
#URL_ACTION_AUTEUR{fusion_pdf,
document/#ID_ARTICLE/#ID_RUBRIQUE/3-6/PDFusion/35,
#SELF|parametre_url{pdf,done}},
ajax})]	

*/

//pompage de editer_produit (merci Rastapopoulos)
/**
 * Action de fusion
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_fusion_pdf_dist($arg=null) {	
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
		
	// On recupere les infos de l'argument
	@list($objet, $id_article, $id_rubrique, $pages, $pretitre, $raccourcir) = explode('/', $arg);
	$id_article = intval($id_article);
	$id_rubrique = intval($id_rubrique);
	$raccourcir = intval($raccourcir);
	
	spip_log("fusion pour pdf article $id_article a partir du document de rubrique $id_rubrique",'fusionpdf');

	// todo?  AND autoriser('fusion_pdf', 'article',$arg[1]))
	if ($objet == 'document' and $id_article and $id_rubrique) {
		$fusion=fusion_pdf_post($objet, $id_article, $id_rubrique, $pages, $pretitre, $raccourcir);
	}
	
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_article'");	

}


function fusion_pdf_post($objet, $id_article, $id_rubrique, $pages, $pretitre,$raccourcir){
	
	include_spip('inc/filtres');

	//le titre de la rubrique est de style "012. titre"
	$titre_rubrique=sql_getfetsel('titre','spip_rubriques',"id_rubrique='$id_rubrique'");
	$numero_rubrique=recuperer_numero($titre_rubrique);
	$titre_article=sql_getfetsel('titre','spip_articles',"id_article='$id_article'");
	
	//retourne un titre pour le fichier pdf court et comprehensible
	include_spip('inc/fusionpdf_fonctions');
	$titre_sortie=titrature($titre_article,$numero_rubrique,$pretitre,$raccourcir);
		
	//on recupere le chemin du pdf de la rubrique
	//la condition ici est index et num (cf like)
	//todo mettre une condition generique sur le titre (Numero ? ou mot clef? ou facon de l'ecrire
	$type='rubrique';
	$pdf_rub = sql_select("D.id_document,D.fichier", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", 
	"T.id_objet=" . intval($id_rubrique) . " AND T.objet=" . sql_quote($type)
	." AND D.mode='document' AND D.extension ='pdf'"
	." AND D.fichier LIKE '%$numero_rubrique%'"
	." AND D.fichier LIKE '%index%'"
	);
	
	$doc_depart = sql_fetch($pdf_rub);
		spip_log("fusionpost2".$doc_depart['fichier']." fera titre = $titre_sortie fin",'fusionpdf');

		if (!$doc_depart) return false;

	$pdf_depart = $doc_depart['fichier'];
	$fichier_base="pdf/".$titre_sortie.".pdf";
		$inputpdf=_DIR_IMG.$pdf_depart;
		$outputpdf=_DIR_IMG.$fichier_base;
		
		//verifier que ce n'est pas encore dans la base
		if ($doc = sql_fetsel('id_document', 'spip_documents', 'fichier='.$fichier_base.' AND id_article='.$id_article))
		return false;
	
	//todo > reprendre les numeros des pages de N-N (5-7)
	// soit champ supp pour article
	// soit taper dans cvs, mais en cas d'erreur, attention confusion et grosse galre!
	fusionner($inputpdf,$outputpdf,$pages);
	
	if(file_exists($outputpdf)){
		$date_article=sql_getfetsel('date','spip_articles',"id_article='$id_article'");
		
	//preparer les champs
			$champs['date'] = $date_article;
			$champs['fichier'] = $fichier_base;
			$champs['taille'] =  filesize($outputpdf);
			$champs['largeur'] = 0;
			$champs['hauteur'] = 0;
			$champs['mode'] = 'document';
			$champs['extension'] = 'pdf';
			$champs['statut'] = 'publie';
			
	//les inserer avec pipeline adequat	
	$id_document = insert_document($champs);

	if($id_document){
	
	//puis sauvegarder le document dans l'article
	spip_log("insertion doc= $id_document",'fusionpdf');
		$document_lien = sql_insertq(
			'spip_documents_liens',
			array(
				'id_document'=>$id_document,
				'id_objet'=>$id_article,
				'objet'=>'article',
				'vu'=>'non'
			)
		);
	}
	

	}

	
		return $titre_sortie ." et ". $pdf_depart;
}


//verifier la ligne de modif dans fpdf pour le output
function fusionner($inputpdf,$outputpdf,$pages) {
	// pour fusionner divers pdfs entre eux
	//le inputpdf pourrait etre un tableau (cheminpdf1=>pages,cheminpdf2=>pages)
	if(include_once(find_in_path('lib/PDFMerger/PDFMerger.php'))){
	
	$pdf = new PDFMerger;
	$pdf->addPDF($inputpdf,$pages)
		->merge('file', $outputpdf);			
	} else 	spip_log("librairie introuvable input= $inputpdf output= $outputpdf",'fusionpdf');

	
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
