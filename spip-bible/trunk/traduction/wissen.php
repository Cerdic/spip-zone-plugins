<?php

function generer_url_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang){
	$ref = construire_ref_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	return "http://www.bibelwissenschaft.de/nc/online-bibeln/".$wissen."/lesen-im-bibeltext/bibelstelle/".$ref."/anzeige/single/#iv";
}

function construire_ref_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	
	$ref = str_replace(' ','',strip_tags(afficher_references($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,'',',',$lang,'false')));
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
	
	$ref = construire_ref_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);

	$param_cache = array('ref'=>$ref,'wissen'=>$wissen,'version_wissen'=>2);
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}


	
	

	//recuperation du passage

	$url = "http://www.bibelwissenschaft.de/nc/online-bibeln/".$wissen."/lesen-im-bibeltext/bibelstelle/".$ref."/anzeige/single/#iv";
	
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url),'utf-8');
	
	
	
	//selection du passage
	$tableau = explode('<div class="boxcontent-bible">',$code);
	$code = $tableau[1];
	
	$code = preg_replace('#<h1>[0-Z]*</h1>#','',$code);
	
	$tableau = explode('<div id="popupcontent">',$code);
	$code = $tableau[0];
	//suppression des intertitres
	$n = 1;
	while (preg_match('#<h[1-7]>#',$code)){
	   $code = wissen_supprimer_intertitre($n,$code);
	   $n++;
	}
	$resultat = array();		
	$code = strip_tags($code,'<span>');
	$tableau_chapitre = preg_split('!<span class="chapter">([0-9]*)</span> !',$code);
	preg_match_all('!<span class="chapter">([0-9]*)</span> !',$code,$liste_chapitre);
	$index = 0;
	array_shift($tableau_chapitre);
	
	foreach ($liste_chapitre[1] as $chapitre){
		
		$tableau_verset = preg_split('!<span class="verse">([0-9]*)</span>!',$tableau_chapitre[$index]);
		array_shift($tableau_verset);

		preg_match_all('!<span class="verse">([0-9]*)</span>!',$tableau_chapitre[$index],$liste_verset);
		$index2 = 0;

		
	
		foreach($liste_verset[1] as $verset){
				$resultat[$liste_chapitre[1][$index]][$verset] = trim(str_replace('&nbsp;',' ',strip_tags($tableau_verset[$index2])));
				$index2++;	
		}
				
		$index ++;		
	}
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$resultat);
	}

	return $resultat;
	}
function wissen_supprimer_intertitre($n, $code){
    if(preg_match('#<h'.$n.'>#',$code)){
			$tableau = explode('<h'.$n.'>',$code);
			
			$d = 0;
			$tableau2 = array();
			foreach ($tableau as $j){
				if (preg_match('#</h'.$n.'>#',$j)){
					
					$tableau3 = explode('</h'.$n.'>',$j);
					$tableau2[$d]=$tableau3[1];
					
				
				}
				$d++;
			
			}
			
			$code = implode('',$tableau2);
        }
    return $code;
}
?>