<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
*/
include_spip('inc/bible_tableau');
function bible_supprimer_retour($texte){
   
    $texte = ereg_replace("\t",'',$texte);
    $texte = ereg_replace("[\s]*<br />",'<br />',$texte);
    $texte = ereg_replace("[\s]*<sup>",'<sup>',$texte);
    $texte = ereg_replace("</sup>[\s]*",'</sup>',$texte);
    $texte = ereg_replace("[\t]*",'',$texte);
    $texte = ereg_replace("[\t]*",'',$texte);
    
  while(eregi('  ',$texte)){
    $texte = ereg_replace("  ",'',$texte);
  }
  
  while (eregi("[\r|\n][\r|\n]",$texte)){
    $texte = eregi_replace("[\r|\n][\r|\n]",'',$texte);
  
  }
    $texte = eregi_replace("\n<br />","<br />",$texte);
    $texte = eregi_replace("<quote>","<quote>\n",$texte);
    $texte = eregi_replace("<br />","<br />\n",$texte);
    $texte = eregi_replace("</quote>","\n\n</quote>",$texte);
    $texte = eregi_replace("<p>","\n\n",$texte);
    $texte = eregi_replace("</p>","\n\n",$texte);
 return $texte;
}
function traduire_abreviation($abrev,$lang_original,$lang_traduction){
	$tableau_gateway = bible_tableau("gateway");
	$livre = eregi_replace('[0-9|,|-]+$','',$abrev);
	
	$numero = $tableau_gateway[$lang_original][$livre];
	
	$tableau_inverse = array_flip($tableau_gateway[$lang_traduction]);
	$livre_traduit = $tableau_inverse[$numero];
	
	return str_replace($livre,$livre_traduit,$abrev);


}

function bible_test_livre_seul($i){
	if (eregi('[0-9|,|-]+$',$i)){ return 'non';}
	else {return 'oui';}

}
function livre_seul($i){
	return eregi_replace('[0-9|,|-]+$','',$i);

}


function bible($passage,$traduction='jerusalem',$retour='non',$numeros='non',$ref='non',$mode_test=false){
	$verset_debut = '';
	
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$tableau_livres = bible_tableau('livres');
	$langues_originales = bible_tableau('original');
	global $spip_lang;
	
	$traduction = strtolower($traduction);
	
	$erreur = true;
	
	if (array_key_exists($traduction,$tableau_traduction)){$erreur = false;};
		
	if ($erreur) { 
		return _T('bible:traduction_pas_dispo');
	}

		
	
	$gateway = $tableau_traduction[$traduction]['gateway'];
	$wissen  = $tableau_traduction[$traduction]['wissen'];
	$unbound = $tableau_traduction[$traduction]['unbound'];
	$lire = $tableau_traduction[$traduction]['lire'];
	$lang = $tableau_traduction[$traduction]['lang'];
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
	
	$livre = eregi_replace('[0-9|,|-]+$','',$debut);
	
	if (array_key_exists($livre,$livres) == false){
		return _T('bible:pas_livre');
	
	}
    if ($mode_test){
	   return;
	}
	
	
	$debut = eregi_replace($livre,'',$debut);
	
	
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
	
	if ($lire){
		include_spip('traduction/lire');
		$texte = '<quote>'.recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
	}
	
	else if ($unbound){
		include_spip('traduction/unbound');
		$texte = '<quote>'.recuperer_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang);
	}
		
		
	else if ($wissen){
		$isaie == true ? $passage = eregi_replace('Es','Is',$passage) : $passage = $passage;
		include_spip('traduction/wissen');
		$texte = '<quote>'.recuperer_passage_wissen($livre,$passage,$wissen,$lang);
		
		}
	
	else if ($gateway){
		
		include_spip('traduction/gateway');
		$texte = '<quote>'.recuperer_passage_gateway($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$gateway,$lang);
		
	}
	
	else{
	
		include_spip('traduction/'.$traduction);
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin);
	}
	
	//les options du modèles
	if ($numeros=='non'){
		$texte = eregi_replace('<sup>[0-9]+ </sup>','',$texte);
		$texte = eregi_replace('<strong>[0-9]+</strong>','',$texte);
	}
	
	
	if ($retour=='non'){
		$texte = eregi_replace('<br />','',$texte);
	}
	
	if ($ref!='non'){
		if ($original){
			$texte .= '<div lang="'.$lang.'" dir="'.lang_dir($lang).'">'.afficher_references($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$traduction,$separateur,$lang).'</div>';

			}
		
		else{
			$texte .= afficher_references($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$traduction,$separateur,$lang);
			}
		}
	$texte = bible_supprimer_retour(str_replace('&nbsp;&nbsp;  <sup>','<sup>',$texte));
	if ($spip_lang == $lang_original) {
		return $texte.'</quote>';
		}
	else
		{return '<div lang="'.$lang_original.'" dir="'.$dir.'">'.$texte.'</quote></div>';
	}
}
function livre_long($i,$lang=''){
	global $spip_lang;
	$lang =='' ? $lang = $spip_lang : $lang=$lang;
	
	$i = eregi_replace('[0-9|,|-]+$','',$i);
	
	$tableau_livres = bible_tableau('livres');
	
	return $tableau_livres[$lang][$i];

}
function filtre_ref($i){
	global $spip_lang;
	$tableau_livres = bible_tableau('livres');
	
	$livre = eregi_replace('[0-9|,|-]+$','',$i);
	$trad = $tableau_livres[$spip_lang][$livre];
	
	$c = eregi_replace($livre,'',$i);
	
	return $trad.' '.$c; 
	
	
}

function afficher_references($livre,$cd,$vd,$cf,$vf,$trad,$separateur,$lang){
	
	$tableau_traduction = bible_tableau('traduction');
	$tableau_livres = bible_tableau('livres');
	$trad = $tableau_traduction[$trad]['traduction'];
	
	$livre_long = $tableau_livres[$lang][$livre] ;
	
	$livre = str_replace('1','1 ',$livre);
	$livre = str_replace('2','2 ',$livre);
	$livre = str_replace('3','3 ',$livre);
	
	
	if ($cd==$cf and $vd=='' and $vf==''){
		
		return '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd.' (<i>'.$trad.'</i>)';
	
	}
	
	if ($vd=='' and $vf==''){
		
		return '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd.'-'.$cf.' (<i>'.$trad.'</i>)</p>';
	
	}
	
	
	
	$chaine = '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd.$separateur.$vd;
	
	if ($cd!=$cf){
			
		$chaine .= '-'.$cf.', '.$vf;
	
	}
	elseif ($vd!=$vf) {
		
		$chaine .= '-'.$vf;
		
	}
	
	$chaine.= ' (<i>'.$trad.'</i>)';
	
	return $chaine.'</p>';

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