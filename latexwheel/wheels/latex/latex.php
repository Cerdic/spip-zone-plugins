<?php

function tx_latex_traiter_glossaire($glossaire){
	
	$glossaire_spip = traiter_raccourci_glossaire($glossaire[0]);
	preg_match_all("#href=\"(.*)\"#",$glossaire_spip,$tableau);
	$url = $tableau[1][0];
	preg_match_all(",>(.*)</a>,",$glossaire_spip,$tableau);
	$texte = $tableau[1][0];
	return "\href/debut$url/fin/debut$texte/fin";
	
}
function tx_latex_traiter_liens($lien){
	
	// uniformisation des différents raccouris de liens interne
			$lien[4] = preg_replace('#^rubrique#','rub',$lien[4]);
			$lien[4] = preg_replace('#^article#','art',$lien[4]);
			if (is_numeric($lien[4])){
				$lien[4] = 'art'.$lien[4];	
			}
	
	
	if ($lien[1]==''){ 	// si pas de texte correspondant au liens

		if (stripos($lien[4],'://')!=0){ // liens externes
			$texte = "\url/debut$lien[4]/fin";
		}	
		else{
			//sinon objet SPIP pour le moment seul rubrique et article, on verra pour la suite
						
			$texte	= "\nameref/debut$lien[4]/fin (p. \pageref/debut$lien[4]/fin)";
		}
	}
	
	else{
		
		if (stripos($lien[4],'://')!=0){ // liens externes
			$texte = "\href/debut$lien[4]/fin/debut$lien[1]/fin";
		}
		else {
			$texte = "$lien[1]  (p. \pageref/debut$lien[4]/fin)";
		}
	}
	
	return $texte;	
}
function supprimer_verb($code){
	$texte = $code[0];
	$array = array();
	preg_match_all("#<span class=\"base64\" title=\"(.*)\"></span>#",$texte,$array);
	
	foreach ($array[1] as $i){
		$texte = str_replace("<span class=\"base64\" title=\"$i\"></span>",base64_decode($i),$texte);
		
	}
	$array = array();
	
	preg_match_all('#verb¡(.*)\¡#',$texte,$array,PREG_SET_ORDER);
	foreach ($array as $i){
		$texte = str_replace("\\".$i[0],$i[1],$texte);	
	}

	return $texte;
}
?>