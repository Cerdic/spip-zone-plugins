<?php

function generer_url_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	// passage en int
	settype($chapitre_debut,'int');
	settype($chapitre_fin,'int');
	settype($verset_fin,'int');
	settype($verset_debut,'int');
	// Correction pour correspondre au systeme de NA
	if ($verset_fin == 0){
		$verset_fin= 9999;	
	}
	if ($verset_debut == 0){
		$verset_debut=1;
	}
	
	$livres = 	bible_tableau('gateway');
	$base 	= 'http://www.nestle-aland.com/en/online-lesen/text/bibeltext/lesen/stelle';
	$id		= ($livres[$lang][$livre]) -46; // numéro du livre
	
	$debut  = $chapitre_debut * 10000 + $verset_debut;
	$fin	= $chapitre_fin * 10000 + $verset_fin;
	return ($base."/$id/$debut/$fin");
}
function recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	

	$url = generer_url_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);	
	return extraire_passage($url,$verset_debut,$verset_fin);
}

function extraire_passage($url,$verset_debut,$verset_fin){
	include_spip('inc/querypath');
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url),'utf-8');
	$tab = explode('<body>',$code);
	$code = $tab[2];
	$tab = explode('</body>',$code);
	$code = $tab[0];
	$qp = spip_query_path($code,'body #main',array('omit_xml_declaration'=>true,'encoding'=>'UTF-8','use_parser'=>'xml'));
	
}
?>
