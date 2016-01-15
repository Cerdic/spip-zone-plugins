<?php

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['xml_ocr'] = 'extracteur_xml_ocr';

function extracteur_xml_ocr($fichier) {
	if (lire_fichier($fichier, $texte)) {		
		return convertir_extraction_xml_ocr($texte);
	}
}

function convertir_extraction_xml_ocr($c) {
	$item = convertir_xml_ocr($c);
	$texte = extracteur_preparer_insertion($item);
	return $texte ;
}
 
function extracteur_preparer_insertion($item){
	
	$texte = "" ;
	
	if($item['surtitre'])
		$texte .= "<ins class='surtitre'>" . trim($item['surtitre']) . "</ins>\n\n" ;

	if($item['titre'])
		$texte .= "<ins class='titre'>" . trim($item['titre']) . "</ins>\n\n" ;
	
	if($item['chapo'])
		$texte .= "<ins class='chapo'>" . trim($item['chapo']) . "</ins>\n\n" ;

	if($item['auteurs'])
		$texte .= "\n\n@@AUTEUR\n\n" . trim($item['auteurs']) . "\n\n" ;

	if($item['signature'])
		$texte .= "\n\n@@SIGNATURE\n\n" . trim($item['signature']) . "\n\n" ;

	$texte .=  "\n\n" . trim($item['texte']) . "\n" ;
	
	return $texte ;

} 
 
 
function convertir_xml_ocr($u) {


	$article = extraire_balise($u, 'ARTICLE') ;
	$attrs = explode(";" , extraire_attribut($article, "img")) ;
	
	foreach($attrs as $p){
		preg_match("/-(\d+)$/", $p, $m);
		$item['images_pages'][] = $p . ".jpg" ;
		$pages[] = $m[1] ;
	}
	# pages
	$item['pages'] = join(", " , $pages);
	$article = preg_replace("%</*ARTICLE[^>]*>%", "", $article);
	
	# nettoyage
	$article = str_replace("-<SHY/>","", $article);
	
	# balises uniques
	$titraille = array("SURTITRE", "TITRE", "CHAPO");
	
	foreach($titraille as $t){
		$b = extraire_balise($article, $t) ;
		$l = strtolower($t) ;
		$item[$l] = trim(textebrut($b)) ;
		$article = preg_replace("/<" . $t . "[^>]*>.*<\/" . $t . ">/U", "", $article);
	}	
	
	# auteurs
	$auteurs = extraire_balises($article, 'AUTEUR') ;
	foreach($auteurs as $a){
		$aa[] = textebrut($a) ;
		$article = preg_replace("/<AUTEUR[^>]*>.*<\/AUTEUR>/U", "", $article);
	}	
	$item['auteurs'] = join(", " , $aa) ;

	# notes
	$balises_notes = extraire_balises($article, 'NOTE') ;
	foreach($balises_notes as $n){
		$notes[] = trim(textebrut($n)) ;
		$article = preg_replace("/<NOTE[^>]*>.*<\/NOTE>/U", "", $article);
	}	

	# paragraphes
	$article = preg_replace(",</*P>,","\n\n", $article);
	
	# inters
	$article = preg_replace(",<INTERTITRE>,","{{{", $article);
	$article = preg_replace(",</INTERTITRE>,","}}}", $article);
	
	# Italiques
	
	# Illustrations
	
	# texte + notes
	$item['texte'] = $article ;
	
	if($notes){
		$item['texte'] .= "[[<>\n" . join("\n", $notes) ."\n]]" . "\n" ;
		$item['notes'] = $notes ;
	}	
	
	return $item ;
}

