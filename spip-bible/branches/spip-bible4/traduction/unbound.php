<?php
function generer_url_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang){
	list($id_livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin) = unbound_parametre_url($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	$url = "http://unbound.biola.edu/index.cfm?method=searchResults.doSearch&parallel_1=".$unbound."&book=".$id_livre."&from_chap=".$chapitre_debut."&from_verse=".$verset_debut."&to_chap=".$chapitre_fin."&to_verse=".$verset_fin;
	return $url;
	
}
function unbound_parametre_url($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	include_spip('inc/bible_tableau');
	$livre_gateways = bible_tableau('gateway');
	$gateway_to_bound = bible_tableau('unbound');
	
	$id_livre = $gateway_to_bound[$livre_gateways[$lang][$livre]];
	
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);

	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
	
	} 
	return array($id_livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin);
}	

function recuperer_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang){
	$param_cache = array('livre'=>$livre,'chapitre_debut'=>$chapitre_debut,'verset_debut'=>$verset_debut,'chapitre_fin'=>$chapitre_fin,'verset_fin'=>$verset_fin,'unbound'=>$unbound,$url='unbound.biola');
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	
	if ($verset_debut=='' ){
		$verset_debut=1;
		$verset_fin = 9999;
	}
    include_spip("inc/distant");
	include_spip("inc/charsets");
	
	list($id_livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin) = unbound_parametre_url($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	$tableau = array();
	
	// on procède cahpitre par cahpitre, c'est plus long mais moins casse-c** au niveau de la sélèction du texte
	
	$i = $chapitre_debut;
	
	while ($i <=$chapitre_fin){
	       
	       $i != $chapitre_fin ? $vf = 99999 : $vf = $verset_fin; //test préalable pour savoir où on se trouve dans le texte
	       $i != $chapitre_debut ? $vd = 1 : $vd = $verset_debut;
	       
	       $url = "http://unbound.biola.edu/index.cfm?method=searchResults.doSearch&parallel_1=".$unbound."&book=".$id_livre."&from_chap=".$i."&from_verse=".$vd."&to_chap=".$i."&to_verse=".$vf;
	       $code = importer_charset(recuperer_page($url,'utf-8'));
	       
	       $code = selectionner_passage($code);
	       $tableau[$i] = $code;  
	       $i++;
	       
    
    
    }
    //mettons en cache
    if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$tableau);
	}
	return $tableau;
}

function selectionner_passage($code){
    $code = preg_replace('/<bdo dir=\'ltr\'>([0123456789]+):([0123456789]+)<\/bdo>/','',$code);
    /* desormais on se fit au balise bdo pour selectionner le texte : il s'arret au 1er </tr> après le deuxième </bdo>*/
    $tableau = explode("</bdo>",$code);
    $post_bdo = array_pop($tableau);
    $code = implode("</bdo>",$tableau);
    
 
    // traitement de ce qu'il y après le </bdo>
    
    $tableau = explode("</tr>",$post_bdo);
    $code = $code."</bdo>".$tableau[0];
    
    //on ne prend qu'après le 2nd <bdo dir='ltr'> (pas celui du chapitre)
    
    $tableau = explode("<bdo dir='ltr'>",$code);
    
    array_shift($tableau); //on n'a pas besoins de cela, mais je sais pas manipuler bien les tableau, faudrait que je me plonge dans de la doc
    array_shift($tableau);
    
    
    
    $code = "<bdo dir='ltr'>".implode("<bdo dir='ltr'>",$tableau);

    $code = strip_tags($code,"<bdo>");
    $code = str_replace('</bdo>.&nbsp;','</bdo>',$code);
	preg_match_all("!<bdo dir='ltr'>([0-9]*)</bdo>!",$code,$numeros_verset); 
	$tableau_verset = preg_split("!<bdo dir='ltr'>([0-9]*)</bdo>!",$code);
	array_shift($tableau_verset);
 	//var_dump($numeros_verset);
 	$tableau = array();
 	$i = 0;
 	foreach ($numeros_verset[1] as $numero){
 		$tableau[$numero]= trim($tableau_verset[$i]);
 		$i++;
 	}

    //var_dump($tableau);
    return $tableau;
}


?>