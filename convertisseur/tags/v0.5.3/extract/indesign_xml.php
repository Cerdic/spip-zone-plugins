<?php

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['indesign_xml'] = 'extracteur_indesign_xml';

function extracteur_indesign_xml($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {
		return convertir_extraction_indesign_xml($texte);
	}
}

function convertir_extraction_indesign_xml($c) {
	$item = convertir_indesign_xml($c);
	$texte = extracteur_preparer_insertion($item);

	return $texte ;
}

function convertir_indesign_xml($u) {
	include_spip("inc/filtres");

	$article = extraire_balise($u, 'Root');
	// Ménage
	$article = preg_replace("#</?(Root|Article)>#","", $article);

	// titre
	$balise = extraire_balise($article, 'GrandTitre');
	$item['titre'] = supprimer_tags($balise);
	$article = str_replace($balise, "", $article);

	# Italiques
	$article = preg_replace("#<Itallique>#","{", $article);
	$article = preg_replace("#</Itallique>#","}", $article);
	$article = preg_replace("#{\s}#u"," ", $article);

	// chapo
	$balise = extraire_balise($article, 'Chapo');
	$item['chapo'] = supprimer_tags($balise);
	$article = str_replace($balise, "", $article);

	// Paragraphes
	$article = preg_replace("#</?(TXTcourant|TXTEncadre)>#","\n\n", $article);

	// <Exergue>
	$article = preg_replace("#<Exergue>#","<blockquote class='exergue'>", $article);
	$article = preg_replace("#</Exergue>#","</blockquote>", $article);

	//Inter
	$article = preg_replace("#<(Inter|PetitTitre)>#","{{{", $article);
	$article = preg_replace("#</(Inter|PetitTitre)>#","}}}", $article);

	//var_dump("<textarea>$article</textarea>");

	//Gras
	$article = preg_replace("#<Gras>#","{{", $article);
	$article = preg_replace("#</Gras>#","}}", $article);

	// Légendes
	$legendes = extraire_balises($article, 'Legende');
	foreach($legendes as $legende)
		$article = str_replace($legende, "", $article);
	foreach($legendes as $legende)
	$article = "@LEGENDE\n" . supprimer_tags($legende) . "\n\n" . $article ;

	$item['texte'] = $article ;

	$item['xml'] = "<pre style='border:1px solid #cccccc; padding:5px'>" . entites_html($u) . "</pre>" ;

	return $item ;
}

