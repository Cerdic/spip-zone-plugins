<?php
/**
 * Utilisations de pipelines par Monitoring du Facteur
 *
 * @plugin     Monitoring du Facteur
 * @copyright  2015
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Facteurmonitoring
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;


//
// envoie et verfie les emails du facteur
function genie_facteurmonitoring_dist() {
  include_spip('inc/config');
  include_spip('inc/mail');


  // chargement de la configuration
  $email = lire_config("facteurmonitoring/email");
  $email_pwd = lire_config("facteurmonitoring/email_pwd");
  $hote_imap = lire_config("facteurmonitoring/hote_imap");
  $hote_port = lire_config("facteurmonitoring/hote_port");
  $hote_inbox = lire_config("facteurmonitoring/hote_inbox");
  
  //lire_metas();
  $adresse_site = $GLOBALS['meta']['adresse_site']; 
  
  // etape 1: verifier la bonne reception de l'email precedent
  ecrire_meta('facteurmonitoring_etat', 'NOTOK');
  
  if (isset($GLOBALS['meta']['facteurmonitoring_hash'])) {
    $email_hash = trim($GLOBALS['meta']['facteurmonitoring_hash']);
    
    if ($email_hash) {
         // on se connecte en IMAP pour rechercher cet email
        include_spip("lib/PhpImap/MailBox");
    
        $connection = '{'.$hote_imap.':'.$hote_port.'}'.$hote_inbox;
        $mailbox = new PhpImap\Mailbox($connection, $email, $email_pwd, __DIR__);
        try {
              $mailsIds = $mailbox->searchMailBox('SUBJECT "'.$email_hash.'"');
              if($mailsIds) {
                  // on efface les emails
                  foreach($mailsIds as $mailsId) 
                       $mailbox->deleteMail($mailsId);
                  
                  ecrire_meta('facteurmonitoring_etat', 'OK'); 
                  spip_log("[reception] OK, email lu $email_hash","facteurmonitoring");
                  
              } else {                  
                  spip_log("[reception] NOTOK, erreur: email introuvable $email_hash","facteurmonitoring");        
              }
        
        } catch(Exception $e){
              spip_log("[reception] NOTOK, erreur: boite inaccessible en lecture","facteurmonitoring"); 
        }
    } else {       
      spip_log("[reception] NOTOK, erreur: email hash vide","facteurmonitoring"); 
    }     
    
  } else {
      spip_log("[reception] NOTOK, erreur: email hash inconnu","facteurmonitoring"); 
  }
    
  // etape 2: envoie d'un nouvel email test
  $envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
  $email_hash = md5($adresse_site.time());
  $email_sujet = "[facteur-monitoring] $email_hash";
  $email_body = _T('facteurmonitoring:no-reply',array('site'=>$adresse_site));
  
  if ($ok = $envoyer_mail($email,$email_sujet,$email_body)) {
      ecrire_meta('facteurmonitoring_hash', $email_hash); 
      spip_log("[envoi] OK, envoi email  $email_hash","facteurmonitoring");
  } else {
      ecrire_meta('facteurmonitoring_etat', 'NOTOK');
      spip_log("[envoi] NOTOK, erreur: envoi email $email_hash","facteurmonitoring");
  }    
                

  return 1;
}
?>