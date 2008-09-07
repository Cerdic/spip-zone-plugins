<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
*/

function bible_install($action){
	
	switch($action){
		case 'install':
			
			if (function_exists('ecrire_config')){
				
				ecrire_config('bible/numeros','oui');
				ecrire_config('bible/retour','oui');
				ecrire_config('bible/ref','oui');
				ecrire_config('bible/traduction_fr','jerusalem');
				ecrire_config('bible/traduction_en','kj');
				}
			break;
			
		case 'uninstall':
			
			if (function_exists('effacer_config')){
				effacer_config('bible/numeros');
				effacer_config('bible/retour');
				effacer_config('bible/ref');
				effacer_config('bible/traduction');
				effacer_config('bible/traduction_fr');
				effacer_config('bible/traduction_en');
			}
			break;
			
	}
}

include_spip('inc/bible_tableau');


function bible($passage,$traduction='jerusalem',$retour='non',$numeros='non',$ref='non'){
	$verset_debut = '';
	global $tableau_traduction;
	global $tableau_separateur;
	global $tableau_livres;
	global $spip_lang;
	$traduction = strtolower($traduction);
	
	$erreur = true;
	
	if (array_key_exists($traduction,$tableau_traduction)){$erreur = false;};
		
	if ($erreur) { 
		return _T('bible:traduction_pas_dispo');
	}
	
	
	
	$gateway = $tableau_traduction[$traduction]['gateway'];
	$lang = $tableau_traduction[$traduction]['lang'];
	$livres =  $tableau_livres[$lang];
	$separateur = $tableau_separateur[$lang];
	
	
	
	
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
	
	$livre = eregi_replace('[0-9|,|-]+','',$debut);
	
	if (array_key_exists($livre,$livres) == false){
		return _T('bible:pas_livre');
	
	};
	
	$debut = eregi_replace($livre,'',$debut);
	
	
	$livre=='Es' and $traduction=='jerusalem' ? $livre = 'Is' : $livre = 'Es'; // gestion Isaïe/Esaïe
	
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
	
	
	//}
	
	if ($gateway){
		
		include_spip('traduction/gateway');
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$gateway,$lang);
		
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
	//$texte = preg_replace('/[\r\n]/m', '', $texte);
	//$texte = str_replace('. <sup> ','.</sup>',$texte);
	//$texte = str_replace(', <sup>',',<sup>',$texte);
	if ($retour=='non'){
		$texte = eregi_replace('<br />','',$texte);
	}
	
	if ($ref!='non'){
		$texte .= afficher_references($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$traduction,$separateur);
		}
	if ($spip_lang == $lang) {
		return $texte.'</quote>';
		}
	else
		{return '<div lang="'.$lang.'">'.$texte.'</quote></div>';
	}
}

function afficher_references($livre,$cd,$vd,$cf,$vf,$trad,$separateur){
	global $tableau_traduction;
	global $tableau_livres;
	$trad = $tableau_traduction[$trad]['traduction'];
	
	$livre_long = $tableau_livres[$tableau_traduction[$traduction]['lang']] ;
	
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
function traduction_longue($fictif,$i){
	//$fictif ne sert à rien, mais c'est pour ne aps à avoir a faire appel à #VAL (car existe pas <2.0)
	global  $tableau_traduction;
	return $tableau_traduction[$i]['traduction'];
	}
function traduction_defaut($lang){
	$normal =  lire_config('bible/traduction_'.$lang);
	//pour compatibilite
	$normal ='' ? $lire_config =lire_config('bible/traduction') : $normal = $normal;
	return $normal;
}	
?>