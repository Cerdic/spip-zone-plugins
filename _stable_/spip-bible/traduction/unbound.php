<?

function recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang){
	
	if ($verset_debut=='' ){
		$verset_debut=1;
		$verset_fin = 9999;
	}
	include_spip('inc/bible_tableau');
	$livre_gateways = bible_tableau('gateway');
	$gateway_to_bound = bible_tableau('unbound');
	
	$id_livre = $gateway_to_bound[$livre_gateways[$lang][$livre]];
	
	$url = "http://www.unboundbible.org/index.cfm?method=searchResults.doSearch&parallel_1=".$unbound."&book=".$id_livre."&from_chap=".$chapitre_debut."&from_verse=".$verset_debut."&to_chap=".$chapitre_fin."&to_verse=".$verset_fin;
	
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url,'utf-8'));
	$code =explode("Made available in electronic",$code);
	$code= $code[0];
	$tableau = explode('<br />'
,$code);
	
	
	$i = 1;
	
	$nb_chapitre =   $chapitre_fin - $chapitre_debut +1 ;
	
	$code = '';
	while ($i<= $nb_chapitre){
	
		
		if ($i!=$nb_chapitre){
			
			$temp= $tableau[$i];
			$tableau2 = explode("<td align='left' colspan='1'>",$temp);
			$code.= $tableau2[0];
			
		}
		else{
			$temp = $tableau[$i];
			
			$tableau2=explode("<tr><td align='center' colspan='2' class='altThinlineWhite'>
",$temp);
			
			$code.= $temp;
		}
		
		$i=$i+1;
	}
	
	$tableau = explode("<tr><td align='center' colspan='2' class='altThinlineWhite'>",$code);
	$code=$tableau[0];
	$code = strip_tags($code,'<bdo>');
	$code = str_replace("<bdo dir='rtl'>",'<br /><sup>',$code);
	$code = str_replace("<bdo dir='ltr'>",'<br /><sup>',$code);
	$code = str_replace('</bdo>.&nbsp;','</sup>',$code);
		
	
	
	//ajout des numerso de chapitre
	$tableau = explode ("<sup>1</sup>",trim($code));
	
	
	$i = $chapitre_debut;
	$j = 1;
	$code ='';
	while ($i<=$chapitre_fin){
		$code .= '<br /><strong>'.$i.'</strong>';
		if (($verset_debut==1) or ($i!=$chapitre_debut)){
			$code.='<sup>1</sup>';
		}
		
		$code .= $tableau[$j];
		$i++;
		$j++;
		
		}
	$code = str_replace('</strong><br /><sup>','</strong><sup>',$code);
	return str_replace('<br /><br />','<br />',$code);
	}


?>