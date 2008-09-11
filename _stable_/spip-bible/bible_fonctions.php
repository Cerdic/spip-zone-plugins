<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
*/
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

function bible_install($action){
	
	switch($action){
		case 'install':
			
			if (function_exists('ecrire_config')){
				
				ecrire_config('bible/numeros','oui');
				ecrire_config('bible/retour','oui');
				ecrire_config('bible/ref','oui');
				ecrire_config('bible/traduction_fr','jerusalem');
				ecrire_config('bible/traduction_en','kj');
				ecrire_config('bible/traduction_de','luther1545');
				ecrire_config('bible/traduction_es','dhh');
				ecrire_config('bible/traduction_it','cei');
				ecrire_config('bible/traduction_pl','bty');
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
				effacer_config('bible/traduction_de');
				effacer_config('bible/traduction_es');
				effacer_config('bible/traduction_it');
				effacer_config('bible/traduction_pl');
				
			}
			break;
			
	}
}

include_spip('inc/bible_tableau');


function bible($passage,$traduction='jerusalem',$retour='non',$numeros='non',$ref='non'){
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
	
	if ($lire){
		include_spip('traduction/lire');
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
	}
	
	else if ($unbound){
		include_spip('traduction/unbound');
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang);
	}
		
		
	else if ($wissen){
		
		include_spip('traduction/wissen');
		$texte = '<quote>'.recuperer_passage($livre,$passage,$wissen,$lang);
		
		}
	
	else if ($gateway){
		
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
	$texte = str_replace('&nbsp;&nbsp;  <sup>','<sup>',$texte);
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

function bible_generer_cfg($i){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$texte = '<form action="#SELF" method="post">
[<div>(#ENV{_cfg_}|form_hidden)</div>]
	
	<ul>
	<div id="explication"><bible:cfg_explication:></div>';
	foreach ($tableau_separateur as $lang=>$j){
		$texte .= '<li>
					<label for="traduction_'.$lang.'"><:bible:cfg_traduction_'.$lang.':></label>
					<select name="traduction_'.$lang.'"  id="traduction_'.$lang.'">'
					;
		foreach ($tableau_traduction as $traduction=>$tableau){
			if ($lang==$tableau['lang']){
				$texte .='<option value="'.$traduction.'" [selected="(#ENV{traduction_'.$lang.'}|=={'.$traduction.'})"]>
				'.
				traduction_longue($traduction).
						'
						
						</option>
						';
			
			}
		
		
		} 
		$texte.= 	'</select>
				
			</li>';

		
	}
		


	return $texte.'<li>
					<label for="numeros"><:bible:cfg_numeros:></label>
					<input type="checkbox" name="numeros"  id="numeros" [checked="(#ENV{numeros})"]  value="oui" />
				
			</li>
			
			<li>
					<label for="retour"><:bible:cfg_retour:></label>
					<input type="checkbox" name="retour"  id="retour" [checked="(#ENV{retour})"]  value="oui" />
				
			</li>
			
			<li>
					<label for="ref"><:bible:cfg_ref:></label>
					<input type="checkbox" name="ref"  id="ref" [checked="(#ENV{ref})"]  value="oui" />
				
			</li>
		</ul>

	<p class="boutons"><input type="submit" name="_cfg_ok" value="<:OK:>" />
	<input type="submit" name="_cfg_delete" value="<:Supprimer:>" /></p>
</fieldset></li>
</form>
</div>';
}

function bible_generer_doc($lang){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$tableau_livres = bible_tableau('livres');
	
	$texte = "{{Séparateur chapitre/verset}} : «".$tableau_separateur[$lang]."»";
	$texte.="<br /><br />{{Abréviations des livres}}<br /><br/>";
	
	foreach ($tableau_livres as $lang_livre=>$tableau){
		if ($lang == $lang_livre){
			foreach ($tableau as $abrev=>$livre){
				$texte.='|'.$abrev.'|'.$livre.'|<br/>';
			
			}
		
		
		}
		
	
	}
	$texte .= '<br/>';
	
	foreach ($tableau_traduction as $abrev=>$traduction){
		if ($traduction['lang']==$lang){
			$texte .= '<br/>';
			$texte .= '{{'.$traduction['traduction'].'}}<br />-';
			
			$gateway = $traduction['gateway'];
			$wissen  = $traduction['wissen'];
			$unbound = $traduction['unbound'];
			$at = $traduction['at'];
			$nt = $traduction['nt'];
			$deutero = $traduction['deutero'];
			$lire = $traduction['lire'];
			
			if ($gateway){
				$url = "http://www.biblegateway.com/versions/index.php?action=getVersionInfo&vid=".$gateway;
				
			}
			else if ($wissen){
				$url = "http://www.bibelwissenschaft.de/online-bibeln/".$wissen;
			
			}
			
			else if ($unbound){
				$url = "http://www.unboundbible.org/";
			
			}
			
			else if($lire){
				$url = "http://lire.la-bible.net";
			
			}
			else {
				$url= "mettre ici l'url";
			
			}
			
			$texte.= ' {source} : '.$url;
			$texte.='<br>- {valeur du paramètre traduction} : «'.$abrev.'»'; 		
			$at == true ? $texte.='<br>- {Ancien Testament} : oui ' : $texte.='<br>- {Ancien Testament} : non ';
			$deutero == true ? $texte.='<br>- {Deutérocanoniques} : oui ' : $texte.='<br>- {Deutérocanoniques} : non ';
			$nt == true ? $texte.='<br>- {Nouveau Testament} : oui <br/>' : $texte.='<br>- {Nouveau Testament} : non <br/>';
		
		}
	
	
	}
	


	return $texte;
}	
?>