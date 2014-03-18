<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');
include_spip('base/abstract_sql');


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
          $valeurs['message_ok'] = recuperer_fond('modeles/quickvote', array('id'=>$id_quickvote) );
     }
     // deja vote
     else if ($row = sql_fetsel("ip", "spip_quickvotes_votes", "id_quickvote = $id_quickvote AND ip='$ip'")){
          $valeurs['editable'] = false;
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
     //$ip = $GLOBALS['ip'];
     $ip = quickvote_get_ip_address();
     if ($ip=="")
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
          'message_ok' => recuperer_fond('modeles/quickvote', array('id'=>$id_quickvote) ),
          'editable' => false
     );
}

// (experimental) essayer de recuperer la bonne IP notamment via les CDN, Proxy, ...
// What is the most accurate way to retrieve a user's correct IP address in PHP?
// http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?rq=1
 
 /**
  * Retrieves the best guess of the client's actual IP address.
  * Takes into account numerous HTTP proxy headers due to variations
  * in how different ISPs handle IP addresses in headers between hops.
  */
function quickvote_get_ip_address() {
  // check for shared internet/ISP IP
  if (!empty($_SERVER['HTTP_CLIENT_IP']) && quickvote_validate_ip($_SERVER['HTTP_CLIENT_IP']))
   return $_SERVER['HTTP_CLIENT_IP'];

  // check for IPs passing through proxies
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
   // check if multiple ips exist in var
    $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    foreach ($iplist as $ip) {
     if (quickvote_validate_ip($ip))
      return $ip;
    }    
  }
    
  if (!empty($_SERVER['HTTP_X_FORWARDED']) && quickvote_validate_ip($_SERVER['HTTP_X_FORWARDED']))
   return $_SERVER['HTTP_X_FORWARDED'];
  if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && quickvote_validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
   return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
  if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && quickvote_validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
   return $_SERVER['HTTP_FORWARDED_FOR'];
  if (!empty($_SERVER['HTTP_FORWARDED']) && quickvote_validate_ip($_SERVER['HTTP_FORWARDED']))
   return $_SERVER['HTTP_FORWARDED'];

  // return unreliable ip since all else failed
  return $_SERVER['REMOTE_ADDR'];
}

 /**
  * Ensures an ip address is both a valid IP and does not fall within
  * a private network range.
  *
  * @access public
  * @param string $ip
  */
function quickvote_validate_ip($ip) {
     if (filter_var($ip, FILTER_VALIDATE_IP, 
                         FILTER_FLAG_IPV4 | 
                         FILTER_FLAG_IPV6 |
                         FILTER_FLAG_NO_PRIV_RANGE | 
                         FILTER_FLAG_NO_RES_RANGE) === false)
         return false;
     
     return true;
}



?>