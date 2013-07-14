<?php
include_spip('inc/bible_tableau');
function generer_url_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	if (!$petit){
		return "http://lire.la-bible.net/index.php?reference=$livre+$chapitre_debut&versions[]=$lire";	
	}
	else {
		return "http://lire.la-bible.net/index.php?reference=$livre&versions[]=$lire";	
	}
}

function lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);
	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
		$petit		= true;
	
	} 
	return array($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit);

}
function recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	$param_cache = array(	'version'=>2,
				'livre'=>$livre,
				'chapitre_debut'=>$chapitre_debut,
				'verset_debut'=>$verset_debut,
				'chapitre_fin'=>$chapitre_fin,
				'verset_fin'=>$verset_fin,
				'lire'=>$lire);
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	$url_base="http://lire.la-bible.net/texte.php?versions[]=".$lire;
	
	
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	
	//determination de lu livre
	
	$tableau = bible_tableau('lire_la_bible');
	$livre =  $tableau[$livre];
	$tableau_resulat = array();
	
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
		
		$tableau_resultat[$i] = recuperer_versets(lire_traiter_code(importer_charset(recuperer_page($url,'utf-8'))),$debut,$fin);
		
		$i++;
	}
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$tableau_resultat);
	}
	return $tableau_resultat;
}

function lire_traiter_code($code){
	$code = lire_supprimer_interitre($code);
	$tableau = explode('<div class="styletxt">',$code);
	$tableau = explode('</div>',$tableau[1]);
	
	$code = $tableau[0];
	
	$code = preg_replace('#<span class="reference">[0-9]*</span>#i','*spip*',$code);
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
function supprimer_rupture_ligne($code){
	return preg_replace("#(\n|\r)#"," ",$code);
	}
function recuperer_versets($code,$vd,$vf){
	
	$resultat = array();
	$tableau = explode('<sup>'.$vd.'</sup>',$code);
	
	$code = '<sup>'.$vd.'</sup>'.$tableau[1];
	
	$tableau = explode('<sup>'.$vf.'</sup>',$code);

	$code = str_replace('<br />','',$tableau[0]);
	$versets = array();
	preg_match_all("#<sup>([0-9]*)</sup>#",$code,$versets);

		
	$texte_verset = preg_split('#<sup>([0-9]*)</sup>#',$code);
	if ($texte_verset[0] == ''){
		array_shift($texte_verset);	
	}

	$i = 0;
	foreach ($versets[1] as $verset){
		$resultat[$verset] = trim($texte_verset[$i]);
		$i++;	
	}
	
	return supprimer_rupture_ligne($resultat);

}

function lire_supprimer_interitre($texte){
   
    $texte = preg_replace("#<p></p>#","",$texte); 
    if (preg_match('#p class="titre4"#',$texte) == false){ // c'est ton jamais, des fois qu'il n'y auarit pas d'intertitre ce serait gentils
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