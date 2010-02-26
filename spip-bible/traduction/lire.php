<?php

function recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	
	$url_base="http://lire.la-bible.net/texte.php?versions[]=".$lire;
	
	
	
	
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);

	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
	
	} 

	
	//determination de lu livre
	include_spip('inc/bible_tableau');
	$tableau = bible_tableau('lire_la_bible');
	$livre =  $tableau[$livre];

	
		//recuperation du passage
	include_spip("inc/distant");
	include_spip("inc/charsets");
	
	
	$texte = '';
	$i = $chapitre_debut;
	while ($i<=$chapitre_fin){
		$url = $url_base."&reference=".$livre."+".$i;
		
	
		
		$i == $chapitre_debut ? $debut = $verset_debut : $debut=1;
		$i == $chapitre_fin ? $fin = $verset_fin : $fin = '';
		$verset_debut =='' and $i==$chapitre_debut ? $debut=1 : $debut=$debut;
		$verset_fin =='' and $i==$chapitre_fin ? $debut=1 : $debut=$debut;		
		$fin == '' ? $fin ='' : $fin =$fin +1; 
		
		$code = recuperer_versets(lire_traiter_code(importer_charset(recuperer_page($url,'utf-8'))),$debut,$fin);
		
		
		$i == $chapitre_debut ? $texte.= "<strong>".$i.'</strong>'.$code : $texte.= "<br /><strong>".$i.'</strong>'.$code ;
		$i++;
	}
	
	$texte = str_replace("</sup>"," </sup>",$texte); //important pour l'option de suppression de numero
	return $texte;
}

function lire_traiter_code($code){
	$code = supprimer_interitre($code);
	$tableau = explode('<div class="styletxt">',$code);
	$tableau = explode('</div>',$tableau[1]);
	
	$code = $tableau[0];
	
	$code = eregi_replace('<span class="reference">[0-9]*</span>','*spip*',$code);
	$code = strip_tags($code);
	$tableau = explode("*spip*",$code);
	$total = count($tableau);
	$tableau = array_slice($tableau,1,$total-1);
	
	$code = ''; 
	
	$i = 1;
	foreach ($tableau as $verset){
		$i == 1 ? $code .= '<sup>1</sup> '.$verset : $code .= '<br /><sup>'.$i.'</sup> '.$verset;
		
		$i++;
	} 
	
	return $code;
	
}

function recuperer_versets($code,$vd,$vf){
	
	
	$tableau = explode('<sup>'.$vd.'</sup>',$code);
	
	$code = '<sup>'.$vd.'</sup>'.$tableau[1];
	
	$tableau = explode('<sup>'.$vf.'</sup>',$code);
	$code = $tableau[0];
	
	return $code;

}

function supprimer_interitre($texte){
   
    $texte = eregi_replace("<p></p>","",$texte); 
    if (eregi('p class="titre4"',$texte) == false){ // c'est ton jamais, des fois qu'il n'y auarit pas d'intertitre ce serait gentils
        return $texte;
    
    }
    
    $tableau = explode('<p class="titre4">',$texte);
   
    $texte = array_shift($tableau);
    foreach ($tableau as $chaine){
        $tableau2 = explode("</p>",$chaine);
        $i = array_shift($tableau2); // on peut avoir des paragraphes pour la poesie, et pas seulement pour les intertitres
        $texte .= implode($tableau2,'</p>');
       
    
    }
  
    return $texte;
}
?>
