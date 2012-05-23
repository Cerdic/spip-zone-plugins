<?php
function recuperer_passage_gateway($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin='',$gateway,$lang){
	$param_cache=array('livre'=>$livre,'chapitre_debut'=>$chapitre_debut,'verset_debut'=>$verset_debut,'chapitre_fin'=>$chapitre_fin,'$verset_fin'=>$verset_fin,'gateway'=>$gateway);
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	$id_trad = $gateway[0];
	$nom_trad = $gateway[1];
	
	
	
	$verset_debut=='' ? $verset_debut = 1 : $verset_debut = $verset_debut;
	//reperer le numero de livre
	include_spip('inc/bible_tableau');
	
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);

	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
	
	} 

	
	$livre_gateways = bible_tableau('gateway');
	$livre_gateway =$livre_gateways[$lang];	
	
	foreach ($livre_gateway as $li=>$id){	
		if (strtolower($li)==strtolower($livre)){
			$livre=$id;
			break;
		}
		
	}
	
	
	
	include_spip("inc/distant");
	include_spip("inc/charsets");
	
	$texte = '';
	$i = $chapitre_debut;
	
	$resultat = array();
	while ($i<=$chapitre_fin){
		// recuperer le fichier
		
		
		$url = 'http://www.biblegateway.com/passage/?book_id='.$livre.'&version='.$id_trad.'&chapter='.$i;
        	
		$i == $chapitre_debut ? $verset_debut = $verset_debut : $verset_debut = 1;        
		
		
		$code = importer_charset(recuperer_page($url,'utf-8'));
		
		$tableau = explode('<div class="result-text-style-normal">',$code);
		$code=$tableau[1];
		$tableau = explode('</div',$code);
		$code=$tableau[0];
		
		
		
		$tableau=explode('</h4>',$code);
		$code=$tableau[1];
		if(preg_match('#<strong>Footnotes:</strong>#',$code)){
			$tableau = explode('<strong>Footnotes:</strong>',$code);
			$code = $tableau[0];
		}
		
		//suppression des intertitres
		$code = gateway_supprimer_intertitre($code);
		
		//supprerssion des balises
		$code = str_replace('<p />','<br />',$code);
		$code = str_replace(' class="sup">',"><sup>",$code);
		$code = str_replace('</span>',' </sup>',$code);
		$code = strip_tags($code,'<sup><br>');
		
		$code = supprimer_note($code);
		
		//suprresion des attributs html dans les sup
           
         $code = preg_replace('#class="versenum"#','',$code);
         $code = preg_replace("#value='[0-9]*'#",'',$code);
         $code = preg_replace('#  id="'.$lang.'-'.$nom_trad.'-[0-9]*"#','',$code);
		
		if ($verset_fin!=''){
		//selection des verset
		   $sup = '<sup>'.$verset_debut.'</sup>';
		   
           

           
           
           
             
            $tableau = explode($sup,$code);
			
			
			$code	=  '<sup>'.$verset_debut.'</sup>'.$tableau[1];
			
			if ($i == $chapitre_fin){
				$v = $verset_fin+1;
				$sup = '<sup>'.$v.'</sup>';
				$tableau 	= explode($sup,$code);
				
				$code  		= trim($tableau[0]);
				
				}
			
			
			
		}
		
		$versets = array();
		$array = array();
		$code = preg_replace("#<br />#","",$code);
		$code = preg_replace("*&nbsp;*"," ",$code);
		
		preg_match_all("#<sup>([0-9]*)</sup>#",$code,$versets);
		$texte_verset = preg_split('#<sup>([0-9]*)</sup>#',$code);
	
		array_shift($texte_verset);	

		//var_dump($texte_verset);
		$j  = 0;
		foreach ($versets[1] as $verset){
			$array[$verset] = trim($texte_verset[$j]);
			$j++;	
		}
		$resultat[$i] = $array; 
		

		$i++;
		}
    if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$resultat);
	}
    return $resultat;
	
}

function supprimer_note($texte){
   
    //on boucle tant qu'on trouve des value
    while(preg_match("#value='#",$texte)){
        
        $texte = vider_attribut($texte,'value');
   }
    
    $texte = str_replace(" class='footnote'",'',$texte);
    $texte = preg_replace("#\[[a-z]*\]#i",'',$texte);
  
    $texte = str_replace("<sup></sup>",'',$texte);
    
    return $texte;

}

function gateway_supprimer_intertitre($code){
    
    $tableau = explode('<h5 class="passage-header">',$code); // on fait un tableau
    $i = 0;
    
    foreach($tableau as $chaine){   // on parcour le tableau, et on supprimer ce qu'il y a avant le </h5>
        $tableau2 = explode('</h5>',$chaine);
        
        if (count ($tableau2)==2){  //important de tester que le tableau contient bien deux entrées, pour le cas où on est avant l'intertitre
            $tableau[$i]=$tableau2[1];
  
        }
        else{
            $tableau[$i]=$tableau2[0];        
        
        }
        
        $i++;
    
    }
    
    $code = implode('',$tableau);
    
    return $code;
}
function traiter_sup($code,$abreviation,$lang){
  
    $code = preg_replace(" #class=\"versenum\"#i","",$code);
    $code = preg_replace('# id="'.$lang.'-'.$abreviation.'-[0-9]*"#',"",$code);
    $code = str_replace("</sup>"," </sup>",$code);
    
    return $code;
}


?>
