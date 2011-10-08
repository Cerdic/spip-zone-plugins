<?php 

/*******************************************************\
 *  voir http://pdfmerger.codeplex.com/                         
 *                                                                         
 * 	1-5    recup toutes les pages de 1 à 5 
 * 	1,3,9  recup les pages 1,3 et 9                                                                         *
 * 	all    recup toutes les pages 
 *
 * MODIF essentielle sur fpdf/fpdf.php
 * A SAVOIR merge marche uniquement sur file (case 'F')
 * DONC ligne 1049 remplacer break par return true;
 *
\*******************************************************/


//utilise par action fusion_pdf
//genere le titre propre du pdf
function titrature($titre,$prefix,$raccourcir=35){
include_spip('inc/texte');
	
	$titre=couper($titre,$raccourcir);
	$titre=translitteration($titre);
	$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
	$titre = trim(preg_replace(',\.([^.]+)$,', '', $titre));
	$titre= str_replace(' ','-',strtolower($titre));
	$prefix=$prefix?$prefix.'_':'';
	$titre=$prefix.$titre;
	
return $titre;	
}

?>
