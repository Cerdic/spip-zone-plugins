<?php

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['xml_ocr'] = 'extracteur_xml_ocr';

function extracteur_xml_ocr($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {
		return convertir_extraction_xml_ocr($texte);
	}
}

function convertir_extraction_xml_ocr($c) {
	$item = convertir_xml_ocr($c);
	$texte = extracteur_preparer_insertion($item);

	return $texte ;
}

function convertir_xml_ocr($u) {

	include_spip("inc/filtres");
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
	$article = str_replace("<HHY/>","", $article);
	$article = str_replace("<UHY/>","", $article);



	# balises uniques
	$titraille = array("SURTITRE", "TITRE", "CHAPO");

	foreach($titraille as $t){
		$b = extraire_balise($article, $t) ;
		$l = strtolower($t) ;
		$item[$l] = trim(textebrut($b)) ;
		$article = str_replace($b, "", $article);
	}

	# balises multiples
	$balises_multiples = array('AUTEUR','AFFILIATION');

	foreach($balises_multiples as $t){
		$aa = array();
		$elms = extraire_balises($article, $t) ;
		foreach($elms as $a){
			$aa[] = preg_replace("/^\s*\*\s*|(\.|\s|:)*$/","", textebrut($a)) ;
			$article = str_replace($a, "", $article);
		}
		$l = strtolower($t) . "s" ;
		$aa = array_unique($aa);
		$item[$l] = join("@@" , $aa) ;
	}

	# notes
	$balises_notes = extraire_balises($article, 'NOTE') ;
	foreach($balises_notes as $n){
		$notes[] = trim(textebrut($n)) ;
		$article = preg_replace("/<NOTE[^>]*>.*<\/NOTE>/U", "", $article);
	}

	# paragraphes
	$article = preg_replace("#</*P>#","\n\n", $article);

	# inters
	$article = preg_replace("#<INTERTITRE>,*\s*#","{{{", $article);
	$article = preg_replace("#,*\s*</INTERTITRE>#","}}}", $article);

	# Citations
	$article = preg_replace("#<EXERGUE>#","<blockquote>", $article);
	$article = preg_replace("#</EXERGUE>#","</blockquote>", $article);

	# Italiques
	$article = preg_replace("#<I>,*\s*#","{", $article);
	$article = preg_replace("#,*\s*</I>#","}", $article);

	# Illustrations en mode ressource du plugin ressource depuis une collection du plugin serveur de fichiers
	// <ILLUSTRATION id="A-MV-1988-2-FR-0001-0001" xhg="41" yhg="1200" xbd="1347" ybd="3291" nump="5"/>
	$path = url_absolue(dirname(urldecode(_request("fichier"))));
	$article = preg_replace("/<ILLUSTRATION id=\"(.*)\".*>/U","<$path/\\1.jpg>", $article);

	# texte + notes
	$item['texte'] = $article ;

	if($notes){
		$item['texte'] .= "[[<>\n" . join("\n", $notes) ."\n]]" . "\n" ;
	}

	# note signature
	$item['signature'] = $item['affiliations'] ;

	return $item ;
}

