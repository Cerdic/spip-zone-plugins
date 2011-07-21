<?php 

/*******************************************************\
 *  voir http://pdfmerger.codeplex.com/                         
 *                                                                         
 * 	1-5    recup toutes les pages de 1 ˆ 5 
 * 	1,3,9  recup les pages 1,3 et 9                                                                         *
 * 	all    recup toutes les pages 
 *
 * MODIF essentielle sur fpdf/fpdf.php
 * A SAVOIR merge marche uniquement sur file (case 'F')
 * DONC ligne 1049 remplacer break par return true;
 *
\*******************************************************/

// [(#ID_ARTICLE|pdfmerger{grospdfdedepart.pdf,[1,2,3],nomdesortie.pdf})]
// qui sauve le fichier genere dans la base tant que document joint ˆ l'article
// pas besoin de le refabriquer si il existe dŽjˆ
// sinon le bouton "telecharger" le genere
// seuls les abonnes a l'article voit ce fichier ˆ tŽlŽcharger
// tandis que les abonnes mensuel/annuel ont accs au grospdf de la revue

/*function pdfmerger($id,$outputpdf,$args='') {

	if(include_once(find_in_path('lib/PDFMerger/PDFMerger.php'))){
	
	$pdf = new PDFMerger;
	
	$pdf->addPDF('samplepdfs/52_index.pdf', '1-2')
		->addPDF('samplepdfs/two.pdf', '1-2')
		->addPDF('samplepdfs/three.pdf', 'all')
		->merge('file', $outputpdf);			
	}
	 
	
	return " Le fichier <a href='".$outputpdf."'>PDF</a> a ete genere!";
}*/

//Pratiques-No1_vichy-et-l-ordre-des-medecins
//utilise par action fusion_pdf
//[(#TITRE|titrature{[(#TITRE_PARENT|recuperer_numero)]})]
function titrature($titre,$numero_revue='',$pretitre="Pratiques",$raccourcir=35){
include_spip('inc/texte');

	$numero_revue=$numero_revue?"-No$numero_revue":$numero_revue;
	$titre=couper($titre,$raccourcir);
	$titre=translitteration($titre);
	$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
	$titre = preg_replace(',\.([^.]+)$,', '', $titre);
	$titre= str_replace(' ','-',strtolower($titre));
	$titre=$pretitre."$numero_revue"."_".$titre;
	
return $titre;	
}

?>
