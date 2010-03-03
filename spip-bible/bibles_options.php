<?php
include_spip('inc/bible_tableau');
function balise_INFO_BIBLE_TRADUCTION($p){
	$trad = str_replace("'",'',interprete_argument_balise(1,$p));
	$info = str_replace("'",'',interprete_argument_balise(2,$p));
	$i = info_bible_version($trad,$info);
	$p->code = "info_bible_version($trad,$info)";
	
	return $p;
}

function info_bible_version($trad,$info){
	
	$tableau_trad  = bible_tableau('traduction');
	
	
	$tableau_trad = $tableau_trad[$trad];
	
	
	switch ($info){
		case 'lang':
			return traduire_nom_langue($tableau_trad['lang']);
		case 'lang_abrev':
			return $tableau_trad['lang'];
		case 'nt':
			$nt = $tableau_trad['nt'];
			$nt == true ? $i = _T('item_oui') : $i= _T('item_non');
			return $i;
		case 'at':
			$at = $tableau_trad['at'];
			$at == true ? $i = _T('item_oui') : $i= _T('item_non');
			return $i;
        case 'domaine_public':
            $tableau_trad['domaine_public'] ? $i = _T('item_oui') :  $i= _T('item_non');
			return $i;
		case 'deutero':
			$deutero = $tableau_trad['deutero'];
			$deutero == true ? $i = _T('item_oui') : $i= _T('item_non');
			return $i;
		case 'traduction':
			return $tableau_trad['traduction'];
		case 'historique':
			return propre($tableau_trad['historique']);	
			
		}
	
	
	
	
}
function balise_BIBLE_TRADUCTIONS($p){
	
	$lang = interprete_argument_balise(1,$p);
	$domaine_public = interprete_argument_balise(2,$p);

    gettype($lang) == 'NULL' ? $lang = 'tous' : $lang = $lang;
    gettype($domaine_public) == 'NULL' ? $domaine_public = 'non' : $domaine_public = true;
    
    
	$p->code = "bible_traductions($lang,$domaine_public)";

	$p->interdire_scripts=true;
	return $p;

}

function bible_traductions($lang,$domaine_public=false){
    $domaine_public == 'non' ? $domaine_public = false : $domaine_public=$domaine_public;
    
    $tableau_trad  = bible_tableau('traduction');
	$tableau_lang = bible_tableau('langues');;
    
    
    gettype($lang) == 'string' ? $lang = array($lang) : $lang = $lang;

	foreach ($tableau_lang as $lang1){
		
		foreach ($tableau_trad as $trad=>$inf){
			if  ((in_array($inf['lang'],$lang) or $lang[0]=='tous') and $inf['lang']==$lang1){
			     
			     if ($inf["domaine_public"] or $domaine_public){             //test si dans le domaine public
				    $_code[] = "$trad";
			     }
			}
		
		}
	}

	return $_code;

}

function afficher_livres($trad,$modele='standard'){
	$tableau_trad  = bible_tableau('traduction');
	$tableau_livre = bible_tableau('livres');
	$livres_deutero = bible_tableau('deutero');
	$trad2=$trad;
	$trad = $tableau_trad[$trad];
	
	if (gettype($trad)!='array'){
		
		return _T('traduction_pas_dispo');
	
	}
	
	//les infos sur la trad
	$lang = $trad['lang'];
	$trad_long = $trad['traduction'];
	$deutero  = $trad['deutero'];
	$nt  = $trad['nt'];
	$at  = $trad['at'];
	
	$tableau_langue_original = bible_tableau('original');
	global $spip_lang;
	array_key_exists($lang,$tableau_langue_original) == false ? $lang = $lang: $lang =  $spip_lang;
	
	
	
	$tableau_livre_gateway = bible_tableau('gateway');
	$tableau_livre_gateway = array_flip($tableau_livre_gateway[$lang]);
	
	
	
	
	//les fonds
	include_spip('inc/utils');
	$url = 'fonds/livres_bibliques_'.$modele ;
	$url_entete = 'fonds/livres_bibliques_entete_'.$modele ;
	$url_pied = 'fonds/livres_bibliques_pied_'.$modele ;
	
	
	$nt == false ? $max = 47 : $max = 74;
	$at == false ? $i = 47 : $i = 1;
	
	
	$j=0;
	
	$texte = recuperer_fond($url_entete,array('caption'=>_T('bible:livres_bibles',array('trad'=>$trad_long))));
	
	while ($i < $max){
		if (in_array($i,$livres_deutero)==false or ($deutero==true)){
		
			$abreviation = $tableau_livre_gateway[$i];
			
			modulo($j,2) == 0 ? $class='row_even' : $class='row_odd';
			$livre = $tableau_livre[$lang][$abreviation];
			$texte.= recuperer_fond($url,array('class'=>$class,'livre'=>$livre,'abreviation'=>$abreviation,'trad'=>$trad2))."\n";
			$j++;}
			
		
		$i++;
	}
	
	return $texte.recuperer_fond($url_pied);;

}


function balise_LIVRES_BIBLIQUES($p) {
	$trad  = interprete_argument_balise(1,$p);
	$modele = interprete_argument_balise(2,$p);
	
	gettype($modele) == 'NULL' ?  $modele = 'standard' : $modele = $modele;
	
	$p->code = "afficher_livres($trad,$modele)";
	
	$p->interdire_scripts = true;
	
	return $p;
	
}

function balise_LIVRE_LIENS_CHAPITRES($p){
	
	
	//les paramètres
	$livre = interprete_argument_balise(1,$p);
	$modele = interprete_argument_balise(3,$p);
	$trad = interprete_argument_balise(2,$p);
		
	
	gettype($trad) == 'NULL' ? $trad = lire_config('bible/traduction_'.$lang) : $trad = $trad;
	gettype($livre) == 'NULL' ?  $livre = 'standard' : $livre = $livre;
	gettype($modele) == 'NULL' ?  $modele = 'standard' : $modele = $modele;
	
	
	
	
	
	$p->code = "liens_chapitres($livre,$modele,$trad)";
	return $p;

}

function liens_chapitres($livre,$modele,$trad){
	
	$tableau_trad  = bible_tableau('traduction');
	$original = bible_tableau('original');
	
	
	$lang = $tableau_trad[$trad]['lang']  ;

	if (array_key_exists($lang,$original)){
		
		
		global $spip_lang;
		$lang = $spip_lang;
	}
	
	
	
	
	$livre = livre_seul($livre);
	$tableau_livre_gateway = bible_tableau('gateway');
	$tableau_chapitre = bible_tableau('nombres_chapitre');
	$nlivre = $tableau_livre_gateway[$lang][$livre];
	$nombre = $tableau_chapitre[$nlivre-1];
	
	$url = 'fonds/livres_liens_chapitres_'.$modele ;
	
	$i = 1;
	$texte = '';
	while ($i<$nombre){
		$texte.= recuperer_fond($url,array('n'=>$i,'livre'=> $livre,'trad'=>$trad))." | ";
		$i++;
		
	
	}
	
	$texte .= recuperer_fond($url,array('n'=>$i,'livre'=> $livre,'trad'=>$trad));
	return $texte;
}


?>
