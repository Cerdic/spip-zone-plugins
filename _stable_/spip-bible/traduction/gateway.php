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
		if(eregi('<h5>',$code)){
			$tableau = explode('<h5>',$code);
			
			$d = 0;
			$tableau2 = array();
			foreach ($tableau as $j){
				if (eregi('</h5>&nbsp;',$j)){
					
					$tableau3 = explode('</h5>&nbsp;',$j);
					$tableau2[$d]=$tableau3[1];
					
				
				}
				$d++;
			
			}
			
			$code = implode('',$tableau2);
		}
		//supprerssion des balises
		$code = str_replace('<p />','<br />',$code);
		$code = str_replace(' class="sup">',"><sup>",$code);
		$code = str_replace('</span>',' </sup>',$code);
		$code = strip_tags($code,'<sup><br>');
		
		if ($verset_fin!=''){
		//selection des verset
		    $sup = '<sup id="'.$lang.'-'.$nom_trad.'-'.$verset_debut.'" class="versenum" value=\''.$verset_debut."'>".$verset_debut.'</sup>';
		   
           // $code = str_replace ($sup,'|',$code);
            
            $tableau 	= explode($sup,$code);
			
			
			$code  		=  '<sup>'.$verset_debut.' </sup>'.$tableau[1];
			
			if ($i == $chapitre_fin){
				$v = $verset_fin+1;
				 $sup = '<sup id="'.$lang.'-'.$nom_trad.'-'.$v.'" class="versenum" value=\''.$v."'>".$v.'</sup>';
				$tableau 	= explode($sup,$code);
				
				$code  		= trim($tableau[0]);
				
				}
			
			
			
		}
		
		$texte .= '<strong>'.$i.'</strong>'.$code;

		$i++;
		}
	$texte = vider_attribut(vider_attribut($texte,'class'),'value');
	 return eregi_replace('<sup>\[[a-z]*\]</sup>','',str_replace(' <br />&nbsp;&nbsp;  <br />','<br />',str_replace('<br /><br /><strong>','<br /><strong>',str_replace('<br />&nbsp;','<br />',str_replace('</strong> <br />&nbsp;','</strong>',$texte)))));
	
}

?>
