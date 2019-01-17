<?php

include_spip("inc/filtres");

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['SaveAsXML'] = 'extracteur_saveasxml';

function extracteur_saveasxml($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {
		return convertir_extraction_saveasxml($texte);
	}
}

function convertir_extraction_saveasxml($c) {
	$item = convertir_saveasxml($c);
	$texte = extracteur_preparer_insertion($item);
	return $texte ;
}

function convertir_saveasxml($c) {
	
	$u = nettoyer_saveasxml($c);
	$item["xml"] = "<code><pre>" . htmlspecialchars($u) . "</pre></code>" ;
	
	// surcharge perso prétraitement
	if(find_in_path('convertisseur_perso.php'))
		include_spip("convertisseur_perso");
	
	if (function_exists('nettoyer_conversion_saveasxml')){
		$u = nettoyer_conversion_saveasxml($u);
		if($balises_insertions = extraire_balises($u,"ins")){
			foreach($balises_insertions as $ba){
				$item[extraire_attribut($ba,"class")] = textebrut($ba) ;
				$u = str_replace($ba,"",$u);
			}
		}
	}
	
	$u = preg_replace("`</?Sect>`Uims","",$u);
	$u = preg_replace("`</?Part>`Uims","",$u);
	
	// Titre
	$balise_titre = extraire_balise($u, "h1");
	// $item["logs"][] = "<code><pre>" . htmlspecialchars($balise_titre) . "</pre></code>" ;
	$item["titre"] = textebrut($balise_titre);
	$u = str_replace($balise_titre,'',$u);
	
	$balises_figures = extraire_balises($u,"Figure");
	
	// Images et lettrines
	foreach($balises_figures as $b){
		$item["logs"][] = htmlspecialchars($b) ;
		if(preg_match("`ActualText=`",$b)){
			$u = preg_replace("`$b\R?`", textebrut($b),$u);
		}
		// ImageData
		elseif(preg_match("`ImageData`",$b)){
			$balise_image = extraire_balise($b,"ImageData") ;
			$image = extraire_attribut($balise_image,"src");
			$legende = textebrut(str_replace($balise_image,"",$b));
			$u = str_replace($b, "<$image>\n$legende\n\n" ,$u);
		}
	}
	
	// Paragraphes
	$u = preg_replace("`</?p>`Ui","\n\n",$u);
	
	// Texte
	$item["texte"] =  htmlspecialchars($u) ;
	
	// passer la main pour une surcharge éventuelle
	$c = $item ;
	
	if (function_exists('nettoyer_conversion')){
		$item = nettoyer_conversion_saveasxml($item);
	}
	

	//$item["xml"] = $u ;

	return $item ;
}

//
function nettoyer_saveasxml($xml){
	$b = extraire_balise($xml, 'x:xmpmeta') ;
	$xml = str_replace($b,'',$xml);
	$xml = preg_replace("`^<(\?|!|/?TaggedPDF-doc).*>\s?$`Uims","",$xml);
	return trim($xml) ;
}
