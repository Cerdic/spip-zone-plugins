<?php

function generer_url_passage_gateway($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$gateway,$lang){
	return "http://www.biblegateway.com/passage/?search=$livre+$chapitre_debut:$verset_debut:$chapitre_fin:$verset_fin&version=$gateway";
}

function recuperer_passage_gateway($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin='',$gateway,$lang){
	$param_cache=array('livre'=>$livre,'chapitre_debut'=>$chapitre_debut,'verset_debut'=>$verset_debut,'chapitre_fin'=>$chapitre_fin,'$verset_fin'=>$verset_fin,'gateway'=>$gateway);
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	$verset_debut=='' ? $verset_debut = 1 : $verset_debut = $verset_debut;
	//reperer le numero de livre
	include_spip('inc/bible_tableau');
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);
	if (in_array(strtolower($livre),$petit_livre)) {
		$verset_debut=$chapitre_debut;		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
	
	} 
    settype($verset_fin,'int');
	settype($verset_debut,'int');
	settype($chapitre_debut,'int');
	settype($chapitre_fin,'int');
	$livre_gateways = bible_tableau('gateway');
	$livre_gateway =$livre_gateways[$lang];	
	
	foreach ($livre_gateway as $li=>$id){	
		if (strtolower($li)==strtolower($livre)){
			$livre=$li;
			break;
		}
		
	}	
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$texte = '';
	$i = $chapitre_debut;
	while ($i<=$chapitre_fin){
		// recuperer le fichier, par chapitre, plus simple (mais plus long, on reconnait)
		$url = 'http://mobile.biblegateway.com/passage/?search='.$livre.'+'.$i.'&version='.$gateway;
		$i == $chapitre_debut ? $verset_debut = $verset_debut : $verset_debut = 1;        
		$i == $chapitre_fin ? $verset_fin = $verset_fin : $verset_fin = 9999999;
		if ($verset_fin ==0){
		     $verset_fin=99999;   
		}
		// preparer pour querypath,  nettoyage
		$code = importer_charset(recuperer_page($url,'utf-8'));
		$tab = explode('<body class="with-alert">',$code);
		$code = $tab[1];
		$tab = explode('<div class="result-text-style-normal text-html ">',$code);
		$code = $tab[1];
		$tab = explode('<div class="foo',$code);
		$code = $tab[0];
		$tab = explode('<div class="passage',$code);
		$code = $tab[0];
		$code = '<div>'.strip_tags($code,'<span><sup><h3><h2>').'</div>';
	    $versets = array();
	    include_spip('inc/querypath');
	    
	    // Analyse proprement dite
		$qp = spip_query_path($code,'',array('ignore_parser_warnings'=>true,'omit_xml_declaration'=>true,'encoding'=>'UTF-8','use_parser'=>'xml'));
		$qp->remove('sup,h3,h2,.chapternum,.indent-1-breaks');// on filtre de tout ce qui est inutile
		$spans  = $qp->children();
		$id = 1; // id par défaut du verse
		foreach ($spans as $span){ // chaque 'span' de premier niveau
		    $text = $span->text();
		    $class = extraire_attribut($span->xml(),'class');
		    $infos = explode('-',$class);
		    if (count($infos) ==3){ // si on change de verset
		        $id = $infos[2];
		        settype($id,'int');
		    }
		    if ($id > $verset_debut-1 and $id < $verset_fin+1) {
		        $versets[$id] = trim($versets[$id].' '.$text);
		    }
		    if ($id > $verset_fin){
		        break;
		    }
		}
        $chapitres[$i] = $versets;
        
        $i++;
	}
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$chapitre);
	}
	return $chapitres;
	}
?>