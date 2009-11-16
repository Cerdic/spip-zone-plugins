<?php
function recuperer_passage($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin='',$gateway,$lang){
	
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
		if(eregi('<strong>Footnotes:</strong>',$code)){
			$tableau = explode('<strong>Footnotes:</strong>',$code);
			$code = $tableau[0];
		}
		
		//suppression des intertitres
		$code = supprimer_intertitre($code);
		
		//supprerssion des balises
		$code = str_replace('<p />','<br />',$code);
		$code = str_replace(' class="sup">',"><sup>",$code);
		$code = str_replace('</span>',' </sup>',$code);
		$code = strip_tags($code,'<sup><br>');
		
		
		if ($verset_fin!=''){
		//selection des verset
		    $sup = '<sup>'.$verset_debut.'</sup>';
		   
           
            //suprresion des attributs html dans les sup
           
           $code = eregi_replace('class="versenum"','',$code);
           $code = eregi_replace("value='[0-9]*'",'',$code);
           $code = eregi_replace('  id="'.$lang.'-'.$nom_trad.'-[0-9]*"','',$code);
           
            
           
             
            $tableau 	= explode($sup,$code);
			
			
			$code  		=  '<sup>'.$verset_debut.'</sup>'.$tableau[1];
			
			if ($i == $chapitre_fin){
				$v = $verset_fin+1;
				 $sup = '<sup>'.$v.'</sup>';
				$tableau 	= explode($sup,$code);
				
				$code  		= trim($tableau[0]);
				
				}
			
			
			
		}
		
		$texte .= '<strong>'.$i.'</strong>'.$code;

		$i++;
		}
    
    /*dernier fignolage cosmétique*/
    
    $texte = str_replace('&nbsp;','',$texte);      //suppresion des espaces insécables, spip les remettra
    
    $texte = supprimer_note($texte);
    $texte = str_replace("  <br />",'',$texte);
    
    $texte = traiter_sup($texte,$nom_trad,$lang);
    while(ereg("<br /><br />",$texte)){
        $texte = str_replace("<br /><br />","<br />",$texte);
    
        }
    
    return $texte;
	
}

function supprimer_note($texte){
   
    //on boucle tant qu'on trouve des value
    while(eregi("value='",$texte)){
        
        $texte = vider_attribut($texte,'value');
   }
  
    $texte = str_replace(" class='footnote'",'',$texte);
    $texte = eregi_replace("\[[a-z]*\]",'',$texte);
  
    $texte = str_replace("<sup></sup>",'',$texte);
    
    return $texte;

}

function supprimer_intertitre($code){
    
    $tableau = explode('<h5>',$code); // on fait un tableau
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
    
    $code = eregi_replace(" class=\"versenum\"","",$code);
    $code = eregi_replace(' id="'.$lang.'-'.$abreviation.'-[0-9]*"',"",$code);
    $code = str_replace("</sup>"," </sup>",$code);
    
    return $code;
}
?>
