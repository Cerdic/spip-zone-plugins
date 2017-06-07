<?php

function generer_url_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang){
	$ref = construire_ref_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);

	return "http://www.bibelwissenschaft.de/bibelstelle/$ref/$wissen";
}

function construire_ref_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	
	$ref = str_replace(' ','',strip_tags(afficher_references_archive($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,'',',',"de",false,false,false,"raccourcie")));
	//petit livre ?

	$petit_livre=bible_tableau('petit_livre','de');
	
	if (in_array(strtolower($livre),$petit_livre)) {
		
		$ref = str_replace($livre,$livre.'1,',$ref);
	} 
	else {
		$ref = str_replace($livre,$livre,$ref);
	}
	
	return $ref;
}

function recuperer_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang){
	
	include_spip('inc/bible_tableau');
	$livre_gateways = bible_tableau('gateway');
	$livre_lang = $livre_gateways[$lang][$livre];
	$livre_al	= array_flip($livre_gateways['de']);
	$livre_or = $livre;
	$livre		= $livre_al[$livre_lang];
	
	$url = generer_url_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang);

	$param_cache = array('url'=>$url,'wissen'=>$wissen,'version_wissen'=>2);
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}


	
	

	//recuperation de la page


	include_spip("inc/distant");
	include_spip("inc/charsets");
	
	$code = importer_charset(recuperer_page($url),'utf-8');
	
	
	// récupération du passage
	$resultat = array();

	// prendre juste la partie du html qui nous intéresse
	$code 	= str_replace('<div class="lineBreak"></div>','',$code);
	$tab 	= explode('<div class="markdown">',$code);
	$code 	= $tab[1];
	$tab 	= explode("</div>",$code);
	$code 	= $tab[0]; 
	
	// purger
	$code 	= preg_replace("#</?(p|strong)>#","",$code);
	$code	= preg_replace("#<h2.*.</h2>#U","",$code); // pas d'interitre
	$code	= preg_replace('# data-location=".*"#U',"",$code);
	$code   = trim($code);
	// par chapitre
	$chapitres = explode('<span class="chapter">',$code);
	array_shift($chapitres);
	foreach ($chapitres as $chapitre){
		$tab = explode("</span>",$chapitre,2);
		
		$chap = $tab[0]; // le numéro de chapitre
		$versets = explode('<span class="verse">',$tab[1]);
		array_shift($versets);
		// par versets
		foreach ($versets as $verset){
			$tab2 = explode("</span>",$verset,2);
			$resultat[$chap][$tab2[0]] = trim($tab2[1]);
			
			}
		}
	
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$resultat);
	}

	return $resultat;
}
?>