<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
*/
$livres_fr = array ('Gn','Ex','Lv','Nb','Dt',
	'Jg','1S','2S','1R','2R','1Ch','2Ch','Esd','Ne','1M','2M',
	'Is','Es','Jr','Ez','Os','Jl','Am','Ab','Jon','Mi','Na','So','Ag','Za','Ml','Dn',
	'Jb','Pr','Qo','Ct','Rt','Lm','Est','Tb','Jdt','Ba','Sg','Si','Ps',
	'Mt','Lc','Jn','Mc',
	'Ac',
	'Ro','1Co','2Co','Ga','Ep','Col','1Th','2Th','1Tm','2Tm','Tt','Phm','He',
	'Jc','1P','2P','1Jn','2Jn','3Jn','Jude','Ap','Ph','Jos'
	);

$livres_en = array (
	'1Chr','1Cor','1Jn','1Mc','1Pt','1Kgs','1Sm','1Thes','2Tm','1Chr','2Cor','2Jn','2Mac','2Pt','2Kgs','2Sm','2Thes','2Tm','3Jn','Hb','Ob','Hg','Am','Ap','Act','Bar','Sg','Col','Dn','Dt','Heb','Eccl','Eph','Esd','Est','Ex','Ez','Phlm','Phil','Ga','Jer','Jas','Jb','Jl','Jon','Jn','Jude','Jdt','Is','Jgs','Jo','Lam','Lk','Lv','Mal','Mk','Mt','Mi','Na','Neh','Nm','Hos','Prv','Rom','Ru','Ps','Ws','Sir','Zep','Ti','Tb','Zec'

	);



function bible_install($action){
	
	switch($action){
		case 'install':
			
			if (function_exists('ecrire_config')){
				
				ecrire_config('bible/numeros','oui');
				ecrire_config('bible/retour','oui');
				ecrire_config('bible/ref','oui');
				ecrire_config('bible/traduction','jerusalem');
				}
			break;
			
		case 'uninstall':
			
			if (function_exists('effacer_config')){
				effacer_config('bible/numeros');
				effacer_config('bible/retour');
				effacer_config('bible/ref');
				effacer_config('bible/traduction');
			}
			break;
			
	}
}


function bible($passage,$traduction='jerusalem',$retour='non',$numeros='non',$ref='non'){
	
	//liste de slivre sous gateway (à completer)
	$bible_gateway=array(
	
	
	);
	
	
	
	//choix des abréviations de livre
	if ($traduction=='jerusalem'){
		global $livre_fr;
		$livre = $livre_fr;
		}	
	if ($traduction=='rsv' or $traduction=='kg'){
		global $livre_en;
		$livre = $livre_en;
	
	}
	
	
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
	
	
	//on cherche le livre
	foreach ($livres as $livre){
		if (eregi($livre,$debut)){
			
			$livre = $livre;
			$debut = eregi_replace($livre,'',$debut);
			break;
		
		
		}
	};
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
	
	
	include_spip('traduction/'.$traduction);
	
	
	$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin);
	
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
		$texte .= afficher_references($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$traduction);
		}
	
	return ($texte.'</quote>');
	
}

function afficher_references($livre,$cd,$vd,$cf,$vf,$trad){
	$livre_long = _T('bible:'.$livre);
	$livre = str_replace('1','1 ',$livre);
	$livre = str_replace('2','2 ',$livre);
	$livre = str_replace('3','3 ',$livre);
	
	if ($cd==$cf and $vd=='' and $vf==''){
		return '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd;
	
	}
	
	if ($vd=='' and $vf==''){
		return '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd.'-'.$cf.' (<i>'._T('bible:'.$trad).'</i>)</p>';
	
	}
	
	
	
	$chaine = '<p><accronym title=\''.$livre_long."'>".$livre.'</accronym> '.$cd.', '.$vd;
	
	if ($cd!=$cf){
		$chaine .= '-'.$cf.', '.$vf;
	
	}
	elseif ($vd!=$vf) {
		$chaine .= '-'.$vf;
		
	}
	
	$chaine.= ' (<i>'._T('bible:'.$trad).'</i>)';
	
	return $chaine.'</p>';

}



?>