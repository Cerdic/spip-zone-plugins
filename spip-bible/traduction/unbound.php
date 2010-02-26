<?php

function recuperer_passage_unbound($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$unbound,$lang){
	
	if ($verset_debut=='' ){
		$verset_debut=1;
		$verset_fin = 9999;
	}
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
    include_spip("inc/distant");
	include_spip("inc/charsets");
	
	$texte = '';
	
	
	
	
	
	// on procède cahpitre par cahpitre, c'est plus long mais moins casse-c** au niveau de la sélèction du texte
	
	$i = $chapitre_debut;
	
	while ($i <=$chapitre_fin){
	       
	       $i != $chapitre_fin ? $vf = 99999 : $vf = $verset_fin; //test préalable pour savoir où on se trouve dans le texte
	       $i != $chapitre_debut ? $vd = 1 : $vd = $verset_debut;
	       
	       $url = "http://www.unboundbible.org/index.cfm?method=searchResults.doSearch&parallel_1=".$unbound."&book=".$id_livre."&from_chap=".$i."&from_verse=".$vd."&to_chap=".$i."&to_verse=".$vf;
	       $code = importer_charset(recuperer_page($url,'utf-8'));
	       $code = selectionner_passage($code);
	       
	       $texte = $texte."<strong>".$i."</strong>".$code;
	       $i == $chapitre_fin ? $texte = $texte : $texte = $texte."<br />";
	       
	       $i++;
    
    
    }
	//fignolage cosmètique
	$texte = str_replace('</strong><br />','</strong>',$texte);
	
	return $texte;
}

function selectionner_passage($code){
   
    /* desormais on se fit au balise bdo pour selectionner le texte : il s'arret au 1er </tr> après le deuxième </bdo>*/
    $tableau = explode("</bdo>",$code);
    $post_bdo = array_pop($tableau);
    $code = implode("</bdo>",$tableau);
    
    
    
    // traitement de ce qu'il y après le </bdo>
    
    $tableau = explode("</tr>",$post_bdo);
    $code = $code."</bdo>".$tableau[0];
    
    //on ne prend qu'après le 2nd <bdo dir='ltr'> (pas celui du chapitre)
    
    $tableau = explode("<bdo dir='ltr'>",$code);
    $bidon = array_shift($tableau); //on n'a pas besoins de cela, mais je sais pas manipuler bien les tableau, faudrait que je me plonge dans de la doc
    $bidon = array_shift($tableau);
    
    
    $code = "<bdo dir='ltr'>".implode("<bdo dir='ltr'>",$tableau);
    
    $code = strip_tags($code,"<bdo>");
    $code = str_replace("</bdo>.&nbsp;"," </sup>",$code);
    $code = str_replace("<bdo dir='ltr'>","<br /><sup>",$code); 
    
 
    return $code;
}


?>