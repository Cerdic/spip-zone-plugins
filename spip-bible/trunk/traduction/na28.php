<?php

function generer_url_passage_na28($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	// passage en int
	settype($chapitre_debut,'int');
	settype($chapitre_fin,'int');
	settype($verset_fin,'int');
	settype($verset_debut,'int');
	// Correction pour correspondre au systeme de NA
	if ($verset_fin == 0){
		$verset_fin= 9999;	
	}
	if ($verset_debut == 0){
		$verset_debut=1;
	}
	
	$livres = 	bible_tableau('gateway');
	$base 	= 'http://www.nestle-aland.com/en/online-lesen/text/bibeltext/lesen/stelle';
	$id		= ($livres[$lang][$livre]) -46; // numéro du livre
	
	$debut  = $chapitre_debut * 10000 + $verset_debut;
	$fin	= $chapitre_fin * 10000 + $verset_fin;
	return ($base."/$id/$debut/$fin");
}
function recuperer_passage_na28($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	$param_cache = array($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang,'na28');
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	$tab = array();// endroit où l'on stocke le passage
	
	if ($chapitre_debut == $chapitre_fin){ // cas le plus simple
        $url = generer_url_passage_na28($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
        $tab[$chapitre_debut] = extraire_passage($url,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin);
	}
	else {
	    $chap = $chapitre_debut;
	    
	    while ($chap <= $chapitre_fin){ // On reconstruit chapitre par chapitre
	    	if ($chap == $chapitre_debut){// pour le premier chapitre du lot	
	    		$url = generer_url_passage_na28($livre,$chapitre_debut,$verset_debut,$chapitre_debut,0,$lang);
	    		$tab[$chap] = extraire_passage($url,$verset_debut,0); 
	    	}
	    	else if ($chap == $chapitre_fin){// pour le dernier chapitre du lot
	    		$url = generer_url_passage_na28($livre,$chapitre_fin,1,$chapitre_fin,$verset_fin,$lang);
	    		$tab[$chap] = extraire_passage($url,1,$verset_fin); 
	    	}
	    	else {// pour les autres chapitre
	    		$url = generer_url_passage_na28($livre,$chap,0,$chap,0,$lang);
	    		$tab[$chap] = extraire_passage($url,1,$chap,1000000);	
	    	}
	    	$chap++;	
	    }
	}
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$tab);
	}
	return $tab;
}

function extraire_passage($url,$verset_debut,$verset_fin){
	include_spip('inc/querypath');
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url),'utf-8');
	$tab = explode('<body>',$code);
	$code = $tab[2];
	$tab = explode('</body>',$code);
	$code = $tab[0];
	$code = str_replace("</blockquote>",'',$code);
	$code = str_replace("<blockquote>",'',$code);
	$code = str_replace('<div class="lineBreak"></div>','',$code);
	
	$qp = spip_query_path($code,'.markdown',array('ignore_parser_warnings'=>true,'omit_xml_declaration'=>true,'encoding'=>'UTF-8','use_parser'=>'xml'));
    $tab_verset=array();
    
    $versets = $qp->children(); // chaque p
    foreach ($versets as $verset){ // le contenu de chaque <p>
        $id    = qp($verset,'.verse')->text();
        $texte = qp($verset,'.greek')->text();
        if ($texte!='') {// pb des retour à la ligne dans les verset
        	if ($id){
        		$tab_verset[$id]=$texte;
        	}
        	else {
        		$tab_verset[$oldid]=$texte;
        	}
        }
        $oldid=$id;
    }
    return($tab_verset);
    
}
?>