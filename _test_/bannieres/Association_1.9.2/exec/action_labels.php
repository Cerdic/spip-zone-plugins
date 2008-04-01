<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	define('FPDF_FONTPATH','font/');
	include_spip('pdf/pdf_label');

	// Formatage des feuilles d'etiquettes
	$pdf = new PDF_Label(
		array(
			'name'=>'OLW4786', 	//Nom du format	
			'paper-size'=>'A4', 		//Format du support
			'marginLeft'=>25, 		//Marge intérieure gauche
			'marginTop'=>12, 		//Marge supérieure avant la première étiquette
			'NX'=>2, 					//Nombre de colonnes
			'NY'=>7, 					//Nombre de rangées
			'SpaceX'=>0, 			// Espace horizontal entre les étiquettes
			'SpaceY'=>2, 			//Espace vertical entre les étiquettes
			'width'=>105, 			//Largeur de l'étiquette
			'height'=>39, 				//Hauteur de l'étiquette
			'metric'=>'mm', 			//Unité de mesure
			'font-size'=>10			//Taille de la police
		), 1, 1
	);

	$pdf->Open();
	//$pdf->AddPage();			//S'il reste une feuille entamee dans l'imprimante

	// On imprime les étiquettes
	if ( isset( $_POST['label'] ) ) {
		$label_tab=(isset($_POST["label"])) ? $_POST["label"]:array(); 
		$count=count ($label_tab);
		
		for ( $i=0 ; $i < $count ; $i++ ) {
			$id = $label_tab[$i];
			$query=spip_query ( "SELECT id , nom_famille , prenom, adresse , code_postal , ville , IF( sexe ='F' , 'Mme' , 'M.' ) AS cher FROM spip_auteurs_elargis WHERE id='$id' ORDER BY nom_famille, adresse" );
			//$query=spip_query ( "SELECT DISTINCT id_asso , nom_famille , adresse , code_postal , ville , IF(COUNT(adresse )=2 , 'M. et Mme' , IF(sexe ='F' , 'Mme' , 'M.' )) AS cher , IF(COUNT(adresse )=1 , prenom , '' ) AS prenom FROM spip_auteurs_elargis WHERE statut_interne <>'sorti' GROUP BY adressee  ORDER BY id_asso" );
			$data=spip_fetch_array($query);
			$adresse= $data['adresse'];
			$adresse = utf8_decode($adresse); 
			$prenom = $data['prenom'];
			$prenom = utf8_decode($prenom); 
			
			//Mise en page de l'etiquette
			$pdf->Add_PDF_Label(sprintf("%s\n\n%s\n%s\n%s",$id, $data['cher'].' '.$prenom.' '.$data['nom_famille'], $adresse, $data['code_postal'].' '.$data['ville']));
		}
	}

	$pdf->Output();
?>
