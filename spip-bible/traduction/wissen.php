<?php


function recuperer_passage_wissen($livre,$ref,$wissen,$lang){
	
	include_spip('inc/bible_tableau');
	$livre_gateways = bible_tableau('gateway');
	$livre_lang = $livre_gateways[$lang][$livre];
	$livre_al	= array_flip($livre_gateways['de']);
	$livre_or = $livre;
	$livre		= $livre_al[$livre_lang];
	
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre','de');
	
	if (in_array(strtolower($livre),$petit_livre)) {
		
		$ref = str_replace($livre_or,$livre.'1,',$ref);
	} 
	else {
		$ref = str_replace($livre_or,$livre,$ref);
	}
		
	
	//recuperation du passage
	
	
	$url = "http://www.bibelwissenschaft.de/nc/online-bibeln/".$wissen."/lesen-im-bibeltext/bibelstelle/".$ref."/anzeige/single/#iv";
	
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url),'utf-8');
	
	
	
	//selection du passage
	$tableau = explode('<div class="boxcontent-bible">',$code);
	$code = $tableau[1];
	
	$code = eregi_replace('<h1>[0-Z]*</h1>','',$code);
	
	$tableau = explode('<div id="popupcontent">',$code);
	$code = $tableau[0];
	//suppression des intertitres
	$n = 1;
	while (eregi('<h[1-7]>',$code)){
	   $code = supprimer_intertitre($n,$code);
	   $n++;
	}
			
	$code = strip_tags($code,'<span>');
	
	$code = str_replace('<span class="chapter">','<br /><strong>',$code);
	$code = str_replace('</span> ','</strong>',$code);
	$code = str_replace('<span class="verse">','<br /><sup>',$code);
	$code = str_replace('</span>&nbsp;','</sup>',$code);
	$code = strip_tags($code,'<br><sup><strong>');
	$code = str_replace('</strong><br />','</strong>',$code);
	$code = eregi_replace('^<br />','',$code);
	$code = eregi_replace("</sup>"," </sup>",$code);
	return $code;
	}
function supprimer_intertitre($n, $code){
    if(eregi('<h'.$n.'>',$code)){
			$tableau = explode('<h'.$n.'>',$code);
			
			$d = 0;
			$tableau2 = array();
			foreach ($tableau as $j){
				if (eregi('</h'.$n.'>',$j)){
					
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
