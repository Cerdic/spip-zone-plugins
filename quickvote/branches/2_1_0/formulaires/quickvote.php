<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');
include_spip('base/abstract_sql');


// générer les résultats du vote sous forme d'une chaine HTML
function quickvote_resultat($id_quickvote) {
     include_spip('base/abstract_sql');

     $str_resultat = _T("quickvote:resultat_titre");
     $nb_vote = 0;
     $vote = array();
     
     // boucle sur les reponses disponibles du formulaires = non vide
     if ($res = sql_select('*', 'spip_quickvotes', "id_quickvote =".intval($id_quickvote))) {
         while ($row = sql_fetch($res)) { 
              for ($i=1;$i<11;$i++) {
                  if (trim($row["reponse$i"]))  {                        
                        // cherchons le nb de votes  pour chaque reponse
                        $res2 = sql_select('reponse', 'spip_quickvotes_votes', "id_quickvote = ".intval($id_quickvote). " AND reponse='reponse$i'");
                        $vote[$row["reponse$i"]] = sql_count($res2);
                        $nb_vote += sql_count($res2);                    
                  }
              }
         }
     }
     
     // bilan - calcul des pourcentages 
     $str_resultat .= "<table>";      
     foreach ($vote as $k=>$val) {
                   if ($nb_vote)
                          $resultat_pt = floor(($val/$nb_vote)*100);
                        else
                          $resultat_pt = 0;                    
                   $reponse_intitule = $k;                   
                   $str_resultat .= "<tr><td>$reponse_intitule</td><td>$resultat_pt %</li></td></tr>";
     }
     $str_resultat .= "</table>"; 
     
     if ($nb_vote ==0)
                    $str_resultat .= "<div class='nb_vote'>"._T("quickvote:resultat_0_vote")."</div>";
     else if ($nb_vote ==1)
                    $str_resultat .= "<div class='nb_vote'>"._T("quickvote:resultat_nb_vote")."</div>";
          else  
                    $str_resultat .= "<div class='nb_vote'>"._T("quickvote:resultat_nb_votes",array("nb"=>$nb_vote))."</div>";
     
     return $str_resultat;
     

}


//
//  CVT pour traiter le vote   
//
/**
 * Charger 
 *    
 *    
 */

function formulaires_quickvote_charger_dist($id_quickvote,$skip_vote='non',$masquer_question='non'){  
    $valeurs = "";
    $id_quickvote = intval($id_quickvote);    
    $valeurs['id'] = $id_quickvote;
    $ip	= $GLOBALS['ip'];
    
    // masque question ?
    if ($masquer_question=='oui') {
           $valeurs['masquer_question'] = 'oui'; 
    }
  
    // est ce que le sondage est cloturé ?
    if (($row = sql_fetsel("actif", "spip_quickvotes", "id_quickvote = $id_quickvote AND actif = 0")) || ($skip_vote=='oui')){
  	        $valeurs['editable'] = false;             
            $valeurs['message_ok'] = quickvote_resultat($id_quickvote);
    }     
    // deja vote
    else if ($row = sql_fetsel("ip", "spip_quickvotes_votes", "id_quickvote = $id_quickvote AND ip='$ip'")){
  	      $valeurs['editable'] = false; 
          $valeurs['message_ok'] = quickvote_resultat($id_quickvote);           
    } 
    
    $valeurs['time_invalidateur'] = time().'-'.rand();  // on passe une valeur pour invalider systematiquement le cache
    return $valeurs;
} 

 
/**
 * Verifer
 * 
 */

function formulaires_quickvote_verifier_dist($id_quickvote){
    	$erreurs = array();
      if (!_request('quickvote'))
         $erreurs['message_erreur'] = _T("quickvote:erreur_pasvote");
      
      return $erreurs;
}  

 
/**
 * Traiter
 * 
 */

function formulaires_quickvote_traiter_dist($id_quickvote){
	   // Effectuer des traitements
     
     	// invalider les caches
	   include_spip('inc/invalideur');
	   suivre_invalideur("id='id_quickvote/$id_quickvote'");
     
     // sql     
     $requete_sql = array();
     $requete_sql['id_quickvote']  = $id_quickvote;
     $requete_sql['reponse'] = _request('quickvote');
     $requete_sql['ip']	= $GLOBALS['ip'];
     $n = sql_insertq('spip_quickvotes_votes', $requete_sql);
 
    // Valeurs de retours
    return array(
       'message_ok' => quickvote_resultat($id_quickvote), 
       'editable' => false
    );   
}


  


?>