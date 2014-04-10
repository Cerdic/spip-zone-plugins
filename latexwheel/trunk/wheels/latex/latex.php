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
						
			$texte	= '\nameref/debut'.$lien[4]."/fin ("._T("latexwheel:abr_page")."~\pageref/debut$lien[4]/fin)"; #nameref=renvoi au nom de l'endroit pointé par un label
		}
	}
	
	else{
		
		if (stripos($lien[4],'://')!=0){ // liens externes
			$texte = "\href/debut$lien[4]/fin/debut$lien[1]/fin";
		}
		else {
			$texte = "$lien[1]  ("._T("latexwheel:abr_page")."~\pageref/debut$lien[4]/fin)";
		}
	}
	
	return $texte;	
}

function caracteres_latex($texte){
	// function qui traite les caractère latex
	return preg_replace(',(\$|%|&|_|#),',"\\\\$1",$texte);	
}
	
function supprimer_verb($code){
	
	$texte = $code[0];
	$texte_a_traiter = $code[2];
	$array = array();
	
	preg_match_all('#verb¡(.*)\¡#',$texte_a_traiter,$array,PREG_SET_ORDER);
	
	foreach ($array as $i){
		$texte = str_replace("\\".$i[0],caracteres_latex($i[1]),$texte);	
		$texte = str_replace("\begin{english}","",$texte);
		$texte = str_replace("\\end{english}","",$texte);
	}

	return $texte;
}
?>