<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
*/
include_spip('inc/bible_tableau');


function bible_supprimer_retour($texte){
   
    $texte = preg_replace("#\t#",'',$texte);
    
    $texte = preg_replace("# {2,}#",'',$texte);
    $texte = preg_replace("#[\r|\n][\r|\n]#",'',$texte);
    
 return $texte;
}
function bible_traduire_abreviation($abrev,$lang_original,$lang_traduction){
	$tableau_gateway = bible_tableau("gateway");
	$tableau_originales = bible_tableau('original');
	if (array_key_exists($lang_traduction,$tableau_originales)){
		return $abrev;	
		
	}

	$livre = livre_seul($abrev);
	
	$numero = $tableau_gateway[$lang_original][$livre];
	
	$tableau_inverse = array_flip($tableau_gateway[$lang_traduction]);
	$livre_traduit = $tableau_inverse[$numero];
	
	return str_replace($livre,$livre_traduit,$abrev);


}

function bible_test_livre_seul($i){
	if (preg_match('#[0-9|,|-]+$#',$i)){ return 'non';}
	else {return 'oui';}

}
function livre_seul($i){
	return preg_replace('#[0-9|,|-]+$#','',$i);

}
function bible_analyser_ref($passage,$traduction){
    $tableau_traduction = bible_tableau('traduction');
    $tableau_separateur = bible_tableau('separateur');
	$tableau_livres = bible_tableau('livres');
	global $spip_lang;
    $verset_debut = '';

	$lang = $tableau_traduction[$traduction]['lang'];
    $langues_originales = bible_tableau('original');
    //var_dump($langues_originales);
    array_key_exists($lang,$langues_originales) ? $lang = $spip_lang : $lang = $lang;
	
	$separateur = $tableau_separateur[$lang];
    
	$livres=$tableau_livres[$lang];
	
	// phase d'anaylse
	
	$livre = strtolower($livre);
	$tableau = explode('-',$passage);
	if (count($tableau)==2){
		$fin=$tableau[1];
		//chercher chapitre et verset de fin
			$tableau2 = explode(',',$fin);
			if (count($tableau2)==1){
				
				$verset_fin = $tableau2[0];}
			else{
				$chapitre_fin   = $tableau2[0];
				$verset_fin = $tableau2[1];}			
		
		}
	
	$debut = $tableau[0];
	
	$livre = livre_seul($debut);
	
	if (!array_key_exists($livre,$livres)){
		return _T('bible:pas_livre');
	
	}

	$debut = str_replace($livre,'',$debut);
	
	
	//problème Isaïe / Esaïe => on converti dans la bonne confession
	if ($lang=='fr' and ($livre == 'Is' or $livre =='Es' )){
	   $livre = $tableau_traduction[$traduction]['isaie'];
	   $isaie=true;
	
	
	}
	
	//chercher chapitre et verset du début
	
	$tableau = explode(',',$debut);
	if (count($tableau)==2){
		$verset_debut = $tableau[1];}
	else{
		if (count($tableau2)==1){
			$chapitre_fin=$tableau2[0];
			$verset_fin='';
		}
		
	
		}
	$chapitre_debut  = $tableau[0];	
		
	
	// si reference courte
	if ($chapitre_fin==''){$chapitre_fin=$chapitre_debut;};
	
	if ($verset_debut=='' and count($tableau2)==2){$verset_debut=1;
	$verset_fin=='';
	$chapitre_fin=$chapitre_debut;};
	if ($verset_fin=='' and (count($tableau)==2)){$verset_fin=$verset_debut;}
    return  array($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin);
}

function bible($passage,$traduction='jerusalem',$mode_test=false){

	
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	
    global $spip_lang;
	
	$traduction = strtolower($traduction);
	
	$erreur = true;
	
	if (array_key_exists($traduction,$tableau_traduction)){$erreur = false;};
		
	if ($erreur) { 
		return _T('bible:traduction_pas_dispo');
	}
    $lang = $tableau_traduction[$traduction]['lang'];
    $langues_originales = bible_tableau('original');
    $lang_original = $lang;
		
	
	//si langue originel
	foreach ($langues_originales as $i=>$dir){
		if ($i ==$lang){
		$original = true;
		$lang	  = $spip_lang;
		$lang_original = $i;
		$dir = $dir;
		include_spip('inc/lang');
		break;
		}
	
	}
	$separateur = $tableau_separateur[$lang];

    $tableau_analyse = bible_analyser_ref($passage,$traduction);
    if (!is_array($tableau_analyse)){
        return $tableau_analyse;
    }
    if ($mode_test){
	   return ;
	}
	
	

    $livre = $tableau_analyse[0];
    $chapitre_debut = $tableau_analyse[1];
    $verset_debut = $tableau_analyse[2];
    $chapitre_fin = $tableau_analyse[3];
    $verset_fin = $tableau_analyse[4];
    
    
    $gateway = $tableau_traduction[$traduction]['gateway'];
	$wissen  = $tableau_traduction[$traduction]['wissen'];
	$unbound = $tableau_traduction[$traduction]['unbound'];	
    $lire = $tableau_traduction[$traduction]['lire'];
	
	if ($lire){
		include_spip('traduction/lire');
		$tableau = recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
	}
	
	else if ($unbound){
		include_spip('traduction/unbound');
		$tableau = recuperer_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang);
	}
		
		
	else if ($wissen){
		$isaie == true ? $livre = str_replace('Es','Is',$livre) : $passage = $passage;
		include_spip('traduction/wissen');
		$tableau = recuperer_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang);
		
		}
	
	else if ($gateway){
		include_spip('traduction/gateway');
		$tableau = recuperer_passage_gateway($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$gateway,$lang);

		
	}
	
	else{
	
		include_spip('traduction/'.$traduction);
		$fonction= 'recuperer_passage_'.$traduction;
		$tableau = $fonction($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	}
	include_spip('inc/utils');
	
	return (array('passage_texte'=>$tableau,'passage'=>$tableau_analyse,'lang_original'=>$lang_original,'spip_lang'=>$spip_lang,'lang'=>$lang,'separateur'=>$separateur));
	
	}
function livre_long($i,$lang=''){
	global $spip_lang;
	$lang =='' ? $lang = $spip_lang : $lang=$lang;
	
	$i = livre_seul($i);
	
	$tableau_livres = bible_tableau('livres');
	
	return $tableau_livres[$lang][$i];

}
function filtre_ref($i){
	global $spip_lang;
	$tableau_livres = bible_tableau('livres');
	
	$livre =livre_seul($i);
	$trad = $tableau_livres[$spip_lang][$livre];
	
	$c = str_replace($livre,'',$i);
	
	return $trad.' '.$c; 
	
	
}
function bible_afficher_references_direct($ref,$traduction,$lang,$nommer_trad=true){
	$t = bible_analyser_ref($ref,$traduction);
	$tableau_separateur = bible_tableau('separateur');
	$lang_version 		= info_bible_version($traduction,'lang_abrev');
	$separateur = $tableau_separateur[$lang_version];
	return afficher_references_archive($t[0],$t[1],$t[2],$t[3],$t[4],$traduction,$separateur,$lang,$nommer_trad);
}
function afficher_references_archive($livre,$cd,$vd,$cf,$vf,$trad,$separateur,$lang,$nommer_trad='true',$propre='oui',$url='oui',$forme_livre='abbr'){
	// fonction archivistique, pour compat ascendante, mais déconseillé d'emploi
	$tab = array(
	    "livre" 	=> $livre,
	    "cd"	=> $cd,
	    "vd"	=> $vd,
	    "vf"	=> $vf,
	    "trad"	=> $trad,
	    "separateur"=> $separateur,
	    "lang"	=> $nommer_trad == true ? "oui" : $nommer_trad,
	    "url"	=> $url,
	    "forme_livre" =>$forme_livre
	);
	return afficher_references($tab);
	}
function afficher_references($param){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_livres = bible_tableau('livres');
	$param["trad"] = strtolower($param["trad"]);
	$param["trad"]uction = $tableau_traduction[$param["trad"]]['traduction'];
	
	$livre_long = $tableau_livres[$param["lang"]][$param["livre"]] ;
	
	$param["livre"] = str_replace('1','1 ',$param["livre"]);
	$param["livre"] = str_replace('2','2 ',$param["livre"]);
	$param["livre"] = str_replace('3','3 ',$param["livre"]);
	
	$affichage_livre = recuperer_fond("forme_livre/$param["forme_livre"]",
		array(
		    "livre_long"	=>$livre_long,
		    "livre"		=>$param["livre"],
		))
	    ." ";
    if ($param["nommer_trad"]=='oui'){
        if ($param["url"]=='oui'){
             $param["url"] = bible_url_passage($param["livre"],$param["cd"],$param["vd"],$param["cf"],$param["vf"],$param["trad"],$param["lang"]);
	         $bloc_fin = " ({[$param["trad"]uction->$param["url"]]})";  
	    }
	    else{
	          $bloc_fin = " ({$param["trad"]uction})" ;
	    }
    }
	else {
	    $bloc_fin='';
	}
	if ($param["cd"]==$param["cf"] and $param["vd"]=='' and $param["vf"]==''){
		
		$chaine = $affichage_livre.$param["cd"];
	
	}
	
	else if ($param["vd"]=='' and $param["vf"]==''){
		
		$chaine = $affichage_livre.$param["cd"].'-'.$param["cf"];
	
	}
    else{
    	$chaine = $affichage_livre.$param["cd"].$param["separateur"]." ".$param["vd"];
    	
    	if ($param["cd"]!=$param["cf"]){
    			
    		$chaine .= '-'.$param["cf"].$param["separateur"].' '.$param["vf"];
    	
    	}
    	elseif ($param["vd"]!=$param["vf"]) {
    		
    		$chaine .= '-'.$param["vf"];
    		
    	}
    }
	$chaine.= $bloc_fin;
    
	return $chaine;

}
function bible_url_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$traduction,$lang){
    $tableau_traduction = bible_tableau('traduction');
    
    $gateway = $tableau_traduction[$traduction]['gateway'];
	$wissen  = $tableau_traduction[$traduction]['wissen'];
	$unbound = $tableau_traduction[$traduction]['unbound'];	
    $lire = $tableau_traduction[$traduction]['lire'];

	if ($lire){
		include_spip('traduction/lire');
		$url = generer_url_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
	}
	else if ($unbound){
		include_spip('traduction/unbound');
		$url = generer_url_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang);
	}	
	else if ($wissen){
		$isaie == true ? $livre = str_replace('Es','Is',$livre) : $passage = $passage;
		include_spip('traduction/wissen');
		$url = generer_url_passage_wissen($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang);
		
		}
	
	else if ($gateway){
		include_spip('traduction/gateway');
		$url = generer_url_passage_gateway($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$gateway,$lang);	
	}
	else{
		include_spip('traduction/'.$traduction);
		$fonction= 'generer_url_passage_'.$traduction;
		$url = $fonction($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	}

    return $url;
}
function traduction_longue($i){
	
	$tableau_traduction = bible_tableau('traduction');
	return $tableau_traduction[$i]['traduction'];
	}
	
function traduction_defaut($lang){
	$normal =  lire_config('bible/traduction_'.$lang);
	//pour compatibilite
	$normal ='' ? $lire_config =lire_config('bible/traduction') : $normal = $normal;
	return $normal;
}



?>