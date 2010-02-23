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
				ecrire_config('bible/traduction_pt','ol');
				ecrire_config('bible/traduction_hu','hk');
				ecrire_config('bible/traduction_da','dbpd');
				ecrire_config('bible/traduction_nl','hb');
				ecrire_config('bible/traduction_no','dnb30');
				ecrire_config('bible/traduction_sv','lb_sv');
				ecrire_config('bible/traduction_fi','pr92');
				ecrire_config('bible/traduction_ru','вж');
				ecrire_config('bible/traduction_bg','bb');
            include_spip('inc/plugin');
            $liste=liste_plugin_actifs();
			if ($liste['SPIP_BONUX']){
			     bible_initialise_pp();
			}
				
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
				effacer_config('bible/traduction_pt');
				effacer_config('bible/traduction_ht');
				effacer_config('bible/traduction_da');
				effacer_config('bible/traduction_nl');
				effacer_config('bible/traduction_no');
				effacer_config('bible/traduction_sv');
				effacer_config('bible/traduction_fi');
				effacer_config('bible/traduction_ru');
				effacer_config('bible/traduction_bg');
				effacer_config('bible_pp/trad_prop');
			}
			break;
			
	}
}
function bible_initialise_pp(){
    $tableau = array_keys(bible_tableau('traduction'));
    ecrire_config('bible_pp/trad_prop',$tableau);
    ecrire_config('bible_pp/numeros','oui');
    ecrire_config('bible_pp/retour','oui');
    ecrire_config('bible_pp/ref','oui');
    ecrire_config('bible_pp/lang_pas_art','oui');
    ecrire_config('bible_pp/lang_morte','oui');
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
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
	}
	
	else if ($unbound){
		include_spip('traduction/unbound');
		$texte = '<quote>'.recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang);
	}
		
		
	else if ($wissen){
		$isaie == true ? $passage = eregi_replace('Es','Is',$passage) : $passage = $passage;
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

function bible_generer_cfg($i){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$police = bible_tableau('police');
	
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
		


	$texte.='<li>
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
	';
	
	foreach ($police as $i=>$polices){
	$texte .= '<li>
				<label for="police_'.$i.'"><:bible:police_'.$i.':></label><select name="police_'.$i.'"><option value="" [selected="(#ENV{police_'.$i.'}|=={non})"]><:item_non:></option>';
		foreach ($polices as $j){
			$texte .= "<option value='".$j."' [selected='(#ENV{police_".$i."}|=={".$j."})']>".$j."</option>";
		
		
		}
	$texte .= "</li>";
	}
		

	$texte .='<p class="boutons"><input type="submit" name="_cfg_ok" value="<:OK:>" />
	<input type="submit" name="_cfg_delete" value="<:Supprimer:>" /></p>
</fieldset></li>
</form>
</div>';
	return $texte;
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
			$domaine_public = $traduction['domaine_public'];
			$deutero = $traduction['deutero'];
			$lire = $traduction['lire'];
			
			if ($gateway){
				$url = "http://www.biblegateway.com/versions/index.php?action=getVersionInfo&vid=".$gateway[0];
				
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
			$texte.='<br />- {valeur du paramètre traduction} : «'.$abrev.'»'; 		
			$at == true ? $texte.='<br />- {Ancien Testament} : oui ' : $texte.='<br />- {Ancien Testament} : non ';
			$deutero == true ? $texte.='<br />- {Deutérocanoniques} : oui ' : $texte.='<br />- {Deutérocanoniques} : non ';
			$nt == true ? $texte.='<br />- {Nouveau Testament} : oui' : $texte.='<br />- {Nouveau Testament} : non';
			$domaine_public == true ? $texte.= '<br />- {Domaine Public} : oui <br  />' :  $texte.= '<br />- {Domaine Public} : non <br  />' ;
		}
	
	
	}
	


	return $texte;
}	
?>