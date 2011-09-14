<?php
/**
 * genie / cron
 *
 */
function rssarticle_taches_generales_cron($taches_generales){
  $delai =  60*10; // valeur defaut: ts les 10 min 
  
  // si cfg dispo, on charge les valeurs
  if (function_exists(lire_config))  {
        if (lire_config('rssarticle/cron_interval_value')!="") {    // verifier si champs CFG a ete renseigne sur ce plugin (retro-compat)       
            $delai = intval(lire_config('rssarticle/cron_interval_value')); 
            if ($delai<10)    
                      $delai=10;        // securite pour les valeurs absurdes             
        }      
  } 
	$taches_generales['rssarticle_copie'] = $delai;
  
	return $taches_generales;
}

/**
 * Insertion au centre des pages d'articles dans le privé
 * Affiche un formulaire d'édition de la licence de l'article
 *
 * @param array $flux Le contexte du pipeline
 */
function rssarticle_affiche_milieu($flux) {
  
	if ($flux['args']['exec'] == 'sites'){	
    // si cfg dispo, on charge les valeurs
      if (function_exists(lire_config))  {
        if (lire_config('rssarticle/mode')=="auto")       $mode_auto=true; else  $mode_auto=false;  
      } else { // sinon valeur par defaut
        $mode_auto=false;                // mode: manuel  
    }	
	
	  if (!$mode_auto) {
  		$contexte['id_syndic'] = $flux["args"]["id_syndic"];
  		$flux['data'] .= debut_cadre_relief(_DIR_PLUGIN_RSSARTICLE."/img/rss20.png", true, '',_T("rssarticle:activer_recopie_intro"));
  		$flux['data'] .= "<div id='bloc_rssarticle'>";
  		$flux['data'] .= recuperer_fond('prive/contenu/rssarticle',$contexte,array('ajax'=>false));
  		$flux['data'] .= "</div>";
  		$flux['data'] .= fin_cadre_relief(true);
		}
	}
	return $flux;
}



?>