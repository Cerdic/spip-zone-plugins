<?php

function formulaires_scrut_prop_charger_dist($liste,$sieges='',$quota='',$prime='',$repartition='',$inscrits='',$couleurs=''){
    $return = array();
    $tab_des_obligatoires = array();
    include_spip('scrut_prop_fonctions');
    $return['liste'] = array_map("scrut_prop_mettre_underscore",explode(';',$liste));
    $couleurs = explode(';',$couleurs);
    
    $return['couleurs'] = array();
    $i = 0; 
    foreach($return['liste'] as $liste){
        $return['couleurs'][$liste] = $couleurs[$i];
        $i++;
    }
    
    //verifier que les sièges soient bien un entier
    $sieges2 = $sieges;
    settype($sieges,'int');
    
    if ($sieges2 != '' and !$sieges2==$sieges){
        return array('editable'=>false);
    }
    else{
        $return['sieges'] = $sieges2;
        $table_des_obligatoires[] = 'sieges';
    }
    //verifier que les quota soient bien un entier
    $quota2 = $quota;
    settype($quota,'int');

    if ($quota2 != '' and !$quota2==$quota){
        return array('editable'=>false);

    }
    else{
        $return['quota'] = $quota2;
        $table_des_obligatoires[] = 'quota';
    }
	//verifier que les prime soient bien un flottant
    $prime2 = $prime;
    settype($prime,'float');
    
    if ($prime2 != '' and !$prime2==$prime){
        return array('editable'=>false);
    }
    else{
        $return['prime'] = $prime2;
        $table_des_obligatoires[] = 'prime';
    }
	//verifier que les inscrits soient bien un entier
    $inscrits2 = $inscrits;
    settype($inscrits,'int');

    if ($inscrits2 != '' and !$inscrits2==$inscrits){
        return array('editable'=>false);
    }
    else{
        $return['inscrits'] = $inscrits2;
        $table_des_obligatoires[] = 'inscrits';
    }
	//verifier que la repartition est bien reste ou moyenne
	if($repartition != '' and $repartition!='reste' and $repartition!='moyenne'){
        return array('editable'=>false);
	}
	else{
	   $return['repartition'] = $repartition;
	   $table_des_obligatoires[] = 'repartition';
	}
	$return['obligatoires'] = serialize($table_des_obligatoires);
	return $return;
}

function formulaires_scrut_prop_verifier_dist(){
    
    $resultats = array_map('supprime_espaces',_request('resultat'));
    $return['couleurs']  = _request($couleurs);
    $erreurs = array();
    $liste_fausse = array();
    //verification qu'on a bien affaire à des entiers
    
    foreach($resultats as $liste=>$resultat){
       
        settype($resultat,'integer');
        
        if(!$resultats[$liste]==$resultat){
            $liste_fausse[$liste] = true ;
            
        
        }
    
    } 
    
    if (count($liste_fausse)){
        $erreurs['pas_entiers'] = 'Tout les résultats ne sont pas des entiers !';
        $erreurs['liste_fausse'] = $liste_fausse;
        $il_y_a_erreur = true;
    }
    
    //verification de la somme 
        
    $inscrits = $resultats['inscrits'];
   
    $total = array_sum($resultats) - $inscrits;
    if ($total>$inscrits){
        $erreurs['voix_plus_inscrits'] = "Il y a plus de votants que d'inscrits";
        $il_y_a_erreur = true;
    }
    
    //verification du fait que le nombre d'inscrit est >0
    if($inscrits<=0){
        $erreurs['inscrits_nuls']   = "Le nombre d'inscrits est nul ou inférieur  à 0";
        $il_y_a_erreur = true;
    }
    
    //vérification du seuil
    $quota = nb_fr_to_en(_request('quota'));
    
    $quota2 = $quota;
    settype($quota,'float');
    
    if (!$quota2==$quota){
        $erreurs['quota_pas_nombre'] = "Le seuil minimum fourni n'est pas un nombre";
        $il_y_a_erreur = true;
        
        
    }
    $quota = nb_en_to_fr($quota);
    
    
    //vérification du nombre de siège
    $siege = nb_fr_to_en(_request('siege'));
    $siege2 = $siege;
    settype($siege,'int');

    if (!($siege == $siege2) or $siege==0){
        $il_y_a_erreur = true;
        $erreurs['siege_pas_entier'] = 'Le nombre de siège est nul ou non entier';
        
    
    }
    
    //vérification de la prime
    $prime = nb_fr_to_en(_request('prime'));
    $prime2 = $prime;
    settype($prime,'int');

    if (!($prime==$prime2) or $prime>$siege){
        $il_y_a_erreur = true;
        $erreurs['prime_pas_entier'] = "La prime majoritaire n'est pas un nombre entier de siège, ou bien elle est supérieure au nombre de sièges à pourvoir";
    }
    //renvoi des résultats
    $erreurs['resultats'] = $resultats;
    
    
    if ($il_y_a_erreur){
        $erreurs['repartition'] = _request('repartition');
        $erreurs['sieges'] = nb_en_to_fr($siege2);
        $erreurs['quota'] = $quota2;
        $erreurs['prime'] = $prime2;
        
        //les obligatoires
        $obligatoires = unserialize(_request('obligatoires'));
        $valeur_champs_obligatoire = array();
        foreach ($obligatoires as $champ){
            if ($champ == 'inscrits'){
                $valeur_champs_obligatoire['inscrits'] = $resultats['inscrits'];
            }
            else{
                $valeur_champs_obligatoire[$champ] = str_replace(' ','',$erreurs[$champ]);
            }
    
        }
       $erreurs['valeurs_champs_obligatoire'] =  $valeur_champs_obligatoire;
       $erreurs['graph']                      = _request('graph');
	   return $erreurs;
    }
    else
        
        return array();
}


function formulaires_scrut_prop_traiter_dist(){
    
    //resultat du formulaire
    
    $resultats = array_map('supprime_espaces',_request('resultat'));
    $sieges     = _request('siege');
    $quota      = nb_fr_to_en(_request('quota'));
    $repartition = _request('repartition');
    $prime =    _request('prime');
    settype($prime,'int');      //la prime est un entier pouvant être nul
    
    
    //participation
    $votants = array_sum($resultats) - $resultats['inscrits'];
    $return['votants_pc'] = nb_en_to_fr(round($votants * 100 /$resultats['inscrits'],2));
    $return['votants']    = nb_en_to_fr($votants);
    $return['inscrits'] = nb_en_to_fr($resultats['inscrits']);
    
    //exprimes
    $return['blancs']  = (int) $resultats['blancs'];
    $return['blancs_pc'] = nb_en_to_fr($resultats['blancs'] * 100 / $votants,2);
    $return['exprimes'] = $votants - $return['blancs'];
    
    //quota
    
    settype($quota,'float');
    
    $return['quota_pc'] = nb_en_to_fr($quota);
    $return['quota']    = floor($quota * $return['exprimes'] / 100) ;      // faut-il arrondir au dessus ou au dessous ?
    
    $return['sieges']   = nb_en_to_fr($sieges);
    $return['prime']    = nb_en_to_fr($prime);
    $sieges = $sieges - $prime ;    // on ne distribue pas à la prop la prime majoritaire, par déf !
    
    //on ne prend que les listes qui font plus du seuil
    $listes_repartis = array();
    $liste_sieges   = array();
    
    unset($resultats['blancs']);
    unset($resultats['inscrits']);
    
    foreach($resultats as $liste => $voix){
        $voix < $return['quota'] ? $liste_sieges[$liste] = 0 : $liste_reparti[$liste] = $voix;
        $listes_pc[$liste] =  nb_en_to_fr($voix * 100 /$return['exprimes'],2);
    
    }
    $return['voix_pc'] =  $listes_pc;
    //calcul du quotient
    $voix_utiles = array_sum($liste_reparti);
    $quotient    = $voix_utiles / $sieges;
    $return['quotient'] = nb_en_to_fr($quotient,2);
    
    $siege_par_listes = array();

    //repartition des premiers sièges
    foreach($liste_reparti as $liste=>$voix){

        $sieges_par_listes[$liste] = floor($voix/$quotient);
        $restes[$liste] = $voix % $quotient;
        
        
    }
    
    //sièges restants : à la plus fort moyen ou au plus fort rest
    
    if ($repartition == 'moyenne'){
        $sieges_par_listes = sieges_restants_moyenne($sieges_par_listes,$sieges,$liste_reparti);
        $return['restants']= "Scrutin à la plus forte moyenne";
        }
    else{
        $sieges_par_listes = sieges_restants_reste($sieges_par_listes,$restes,$sieges);
        $return['restants']= "Scrutin au plus fort reste";
    }
    
    
    $liste_sieges = array_merge($liste_sieges,$sieges_par_listes);
    
    
    //prime majoritaire
    $max = max($resultats);
    $return['liste_primee'] = array_search($max,$resultats);
    
    $liste_sieges[$return['liste_primee']] = $liste_sieges[$return['liste_primee']] + $prime;
    
    $return['sieges_par_liste'] = $liste_sieges;
    //appliquer un peu de typo
    foreach ($resultats as $liste=>$voix){
        $resultats[$liste] = nb_en_to_fr($voix);
    
    }
    $return['quota'] = nb_en_to_fr($return['quota']);
    $return['exprimes'] = nb_en_to_fr($return['exprimes']);
    //returner ce qu'il faut
    $return['voix_par_liste']   = $resultats;
    if(_request('graph')) {
        $return['url_graph']        = generer_url_graph($liste_sieges,_request('couleurs'));
    }
    return array('message_ok'=>$return);
}


//fonction de conversion 

function nb_fr_to_en($nb){
    return str_replace(',', '.' ,$nb);
}

function nb_en_to_fr($nb,$dec=0){
    return number_format($nb,$dec,','," ");
}

//repartition des sièges non attribués

function sieges_restants_moyenne($sieges_par_listes,$sieges,$voix){
    $moyenne = array();
    while (array_sum($sieges_par_listes) < $sieges){
        
        foreach($sieges_par_listes as $liste => $siege){
            $moyenne[$liste] = $voix[$liste] / ($siege+1) ;
        
        }
        $plus_forte = max($moyenne);
        
        foreach ($moyenne as $liste => $moy){
            
            if ($moy == $plus_forte){
            
                $sieges_par_listes[$liste] = $sieges_par_listes[$liste]+1;
                break;
            }
        }
    
    }
    

    return $sieges_par_listes;
}

function supprime_espaces($i){
    return str_replace(' ','',$i);
}
function sieges_restants_reste($sieges_par_listes,$restes,$sieges){
    arsort($restes);
    
    while(array_sum($sieges_par_listes) < $sieges){
        
        $clef = key($restes);
        $sieges_par_listes[$clef] =  $sieges_par_listes[$clef]+1;
        
        next($restes);

    
    }
    
    return $sieges_par_listes;
}

function generer_url_graph($sieges,$couleurs){
    $url_base ='http://chart.apis.google.com/chart?cht=p&chs=600x400&chtt=Répartition des sièges';
    
    unset($sieges['']);
    
    $total  = array_sum($sieges);

    $donnes = array();
    
    foreach ($sieges as $i=>$siege){
        $donnes[] = ramener_sous_cent($siege,$total);
    
    }
    $legende = '&chl=|'.implode($sieges,'|');

    
    
    $sieges = '&chd=t:1,'.implode($donnes,',');; 
    
    $couleurs = '&chco=FFFFFF,'.implode($couleurs,',');
    
    return $url_base.$legende.$sieges.$couleurs;


}
function ramener_sous_cent($i,$total){

    return $i/$total;
}
?>