<?
include_spip('inc/bible_tableau.php');

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
	
	
	global $spip_lang;
	array_key_exists($lang,$tableau_langue_original) == true ? $lang = $lang: $lang =  $spip_lang;
	
	
	
	$tableau_livre_gateway = bible_tableau('gateway');
	$tableau_livre_gateway = array_flip($tableau_livre_gateway[$lang]);
	$tableau_langue_original = bible_tableau('original');
	
	
	
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
			$livre = $tableau_livre['fr'][$abreviation];
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
	
	#$p->interdire_scripts = true;
	
	return $p;
	
}

function balise_LIVRE_LIENS_CHAPITRES($p){
	global $spip_lang;
	//les paramètres
	$livre = interprete_argument_balise(1,$p);
	$modele = interprete_argument_balise(3,$p);
	$lang = interprete_argument_balise(4,$p);
	$trad = interprete_argument_balise(2,$p);
	
	gettype($modele) == 'NULL' ?  $modele = 'standard' : $modele = $modele;
	gettype($lang) == 'NULL' ?  $lang = $spip_lang : $lang = $lang;
	gettype($trad) == 'NULL' ? $trad = lire_config('bible/traduction_'.$lang) : $trad = $trad;
	
	$p->code = "liens_chapitres($livre,$modele,$lang,$trad)";
	return $p;

}

function liens_chapitres($livre,$modele,$lang,$trad){
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