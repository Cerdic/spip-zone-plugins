<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');
include_spip('base/abstract_sql');


// générer les résultats du vote sous forme d'un tableau HTML
// alternative: // modele/quickvote
function quickvote_resultat($id_quickvote) {
     include_spip('base/abstract_sql');
     include_spip('inc/texte'); // pour typo

     $nb_vote = 0;
     $vote = array();

     // boucle sur les reponses disponibles du formulaires = non vide
     if ($res = sql_select("b.reponse AS pos, COUNT(b.reponse) AS nbr, CASE b.reponse WHEN 'reponse1' THEN a.reponse1 
                                                                                      WHEN 'reponse2' THEN a.reponse2 
                                                                                      WHEN 'reponse3' THEN a.reponse3 
                                                                                      WHEN 'reponse4' THEN a.reponse4 
                                                                                      WHEN 'reponse5' THEN a.reponse5 
                                                                                      WHEN 'reponse6' THEN a.reponse6 
                                                                                      WHEN 'reponse7' THEN a.reponse7 
                                                                                      WHEN 'reponse8' THEN a.reponse8 
                                                                                      WHEN 'reponse9' THEN a.reponse9 
                                                                                      WHEN 'reponse10' THEN a.reponse10 END AS rep", 'spip_quickvotes a INNER JOIN spip_quickvotes_votes b ON a.id_quickvote = b.id_quickvote', "id_quickvote =".intval($id_quickvote), 'reponse', 'nbr') ) {
          // cherchons le nb de votes  pour chaque reponse
          while ($row = sql_fetch($res)) {
              $vote[$row['pos']] = array($row['rep'], $row['nbr']);
              $nb_vote += $row['nbr'];
          }
     }

     if ($nb_vote==0)
          $str_resultat = "<div class='nb_vote'>"._T("quickvote:resultat_0_vote")."</div>";
     else {
          $str_resultat = '<table class="spip">';
          $str_resultat .= '<caption>'. _T("quickvote:resultat_titre") .'</caption>';
          // bilan - calcul des pourcentages
          $i = 0;
          foreach ($vote as $k=>$val) {
               $str_resultat .= '<tr id="'.$k.'" class="row_'. ($i%2?'odd':'even') .'">';
               $i++;
               $str_resultat .= '<td>'.$val[0].'</td>';
               $str_resultat .= '<td>'.$val[1].'&times; : '. round(($val[1]/$nb_vote)*100) .'%</td>';
               $str_resultat .= '</tr>';
          }
          $str_resultat .= '<tr id="reponse0" class="row_first"><td colspan="2" class="nb_vote">';
          if ($nb_vote==1)
               $str_resultat .= _T('quickvote:resultat_nb_vote');
          else
               $str_resultat .= _T('quickvote:resultat_nb_votes', array('nb'=>$nb_vote));
          $str_resultat .= '</td></tr>';
          $str_resultat .= '</table>';
     }

     return $str_resultat;
}


//
//  CVT pour traiter le vote
//


/**
 * Charger
 *
 */
function formulaires_quickvote_charger_dist($id_quickvote,$skip_vote='non',$masquer_question='non'){
     $valeurs = "";
     $id_quickvote = intval($id_quickvote);
     $valeurs['id'] = $id_quickvote;
     $ip = $GLOBALS['ip'];

     // masque question ?
     if ($masquer_question=='oui') {
          $valeurs['masquer_question'] = 'oui';
     }

     // est ce que le sondage est cloturé ?
     if (($row = sql_fetsel("actif", "spip_quickvotes", "id_quickvote = $id_quickvote AND actif = 0")) || ($skip_vote=='oui')) {
          $valeurs['editable'] = false;
//          $valeurs['message_ok'] = quickvote_resultat($id_quickvote);
          $valeurs['message_ok'] = recuperer_fond('modeles/quickvote', array('id'=>$id_quickvote) );
     }
     // deja vote
     else if ($row = sql_fetsel("ip", "spip_quickvotes_votes", "id_quickvote = $id_quickvote AND ip='$ip'")){
          $valeurs['editable'] = false;
//          $valeurs['message_ok'] = quickvote_resultat($id_quickvote);
          $valeurs['message_ok'] = recuperer_fond('modeles/quickvote', array('id'=>$id_quickvote) );
     }

     $valeurs['time_invalidateur'] = time().'-'.rand();// on passe une valeur pour invalider systematiquement le cache

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
     // invalider les caches  (utile ?)
     include_spip('inc/invalideur');
     suivre_invalideur("id='id_quickvote/$id_quickvote'");

     // SQL
     $ip = $GLOBALS['ip'];
     // sécurité le formulaire peut etre chargé à plusieurs endroits à la fois: on n'enregistre que le 1er vote de cette IP, les autres votes sont ignorés
     if (!$row = sql_fetsel("ip", "spip_quickvotes_votes", "id_quickvote = $id_quickvote AND ip='$ip'"))  {
          $requete_sql = array();
          $requete_sql['id_quickvote']  = $id_quickvote;
          $requete_sql['reponse'] = _request('quickvote');
          $requete_sql['ip'] = $ip;
          $n = sql_insertq('spip_quickvotes_votes', $requete_sql);
     }

     // Valeurs de retours
     return array(
//          'message_ok' => quickvote_resultat($id_quickvote),
          'message_ok' => recuperer_fond('modeles/quickvote', array('id'=>$id_quickvote) ),
          'editable' => false
     );
}


?>