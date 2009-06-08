<?php

function formulaires_scrut_prop_charger_dist($liste){
    
    $liste = explode(';',$liste);
	
	return array('liste'=>$liste);
}

function formulaires_scrut_prop_verifier_dist(){
    
    $resultats = _request('resultat');
    
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
	   return $erreurs;
    }
    else
        
        return array();
}


function formulaires_scrut_prop_traiter_dist(){
    
    //resultat du formulaire
    
    $resultats  = _request('resultat');
    $sieges     = _request('siege');
    $quota      = nb_fr_to_en(_request('quota'));
    $repartition = _request('repartition');
    $prime =    _request('prime');
    settype($prime,'int');      //la prime est un entier pouvant être nul
    
    
    //participation
    $votants = array_sum($resultats) - $resultats['inscrits'];
    $return['votants_pc'] = nb_en_to_fr(round($votants * 100 /$resultats['inscrits'],2));
    $return['votants']    = $votants;
    $return['inscrits'] = $resultats['inscrits'];
    
    //exprimes
    $return['blancs']  = (int) $resultats['blancs'];
    $return['blancs_pc'] = nb_en_to_fr(round($resultats['blancs'] * 100 / $votants,2));
    $return['exprimes'] = $votants - $return['blancs'];
    
    //quota
    
    settype($quota,'float');
    
    $return['quota_pc'] = nb_en_to_fr($quota);
    $return['quota']    = floor($quota * $return['exprimes'] / 100) ;      // faut-il arrondir au dessus ou au dessous ?
    
    $return['sieges']   = $sieges;
    $return['prime']    = $prime;
    $sieges = $sieges - $prime ;    // on ne distribue pas à la prop la prime majoritaire, par déf !
    
    //on ne prend que les listes qui font plus du seuil
    $listes_repartis = array();
    $liste_sieges   = array();
    
    unset($resultats['blancs']);
    unset($resultats['inscrits']);
    
    foreach($resultats as $liste => $voix){
        $voix < $return['quota'] ? $liste_sieges[$liste] = 0 : $liste_reparti[$liste] = $voix;
        $listes_pc[$liste] =  nb_en_to_fr(round($voix * 100 /$return['exprimes'],2));
    
    }
    $return['voix_pc'] =  $listes_pc;
    //calcul du quotient
    $voix_utiles = array_sum($liste_reparti);
    $quotient    = $voix_utiles / $sieges;
    
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
    
    $liste_sieges[$return['liste_prime']] = $liste_sieges[$return['liste_prime']] + $prime;
    
    $return['sieges_par_liste'] = $liste_sieges;
    $return['voix_par_liste']   = $resultats;
    
    return array('message_ok'=>$return);
}


//fonction de conversion 

function nb_fr_to_en($nb){
    return str_replace(',', '.' ,$nb);
}

function nb_en_to_fr($nb){
    return str_replace('.', ',',$nb);
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
            
                $sieges_par_listes[$liste] == $sieges_par_listes[$liste]++;
            
            }
        }
    
    }
    

    return $sieges_par_listes;
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

?>