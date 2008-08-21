<?
function recuperer_passage($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin='',$id_trad,$lang){
	$verset_debut=='' ? $verset_debut = 1 : $verset_debut = $verset_debut;
	//reperer le numero de livre
	include_spip('inc/bible_tableau');
	global $livre_gateways;
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
		
		//supprerssion des balises
		$code = str_replace('<p />','<br />',$code);
		$code = str_replace(' class="sup">',"><sup>",$code);
		$code = str_replace('</span>',' </sup>',$code);
		$code = strip_tags($code,'<sup><br>');
		if ($verset_fin!=''){
		//selection des verset
		
			$tableau 	= explode('<sup>'.$verset_debut.' </sup>',$code);
			$code  		=  '<sup>'.$verset_debut.' </sup>'.$tableau[1];
			
			if ($i == $chapitre_fin){
				$v = $verset_fin+1;
				$tableau 	= explode('<sup>'.$v.' </sup>',$code);
				$code  		=$tableau[0];
				}
			
			
			
		}
		$texte .= '<strong>'.$i.'</strong>'.$code;
			
		$i++;
		}
		
	return str_replace('<br />&nbsp;','<br />',str_replace('</strong> <br />&nbsp;','</strong>',$texte));
	
}

?>