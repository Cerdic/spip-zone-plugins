<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

//
// tester la fonction imap_open est dispo
// 
function facteurmonitoring_test_imap_exist() { 
    if (function_exists('imap_open')) {
        return _T('facteurmonitoring:test_imap_exist_true');  
    } else {
        return _T('facteurmonitoring:test_imap_exist_false'); 
    }
    
    
} 


//
// tester la connection imap
//  
// la parametre time ne sert uniquement à eviter la mise en cache                        
function facteurmonitoring_test_imap($time) {
   include_spip('inc/config');

   $email = lire_config('facteurmonitoring/email'); 
   $email_pwd = lire_config('facteurmonitoring/email_pwd');
   $hote_imap = lire_config('facteurmonitoring/hote_imap'); 
   $hote_port = lire_config('facteurmonitoring/hote_port'); 
   $hote_inbox = lire_config('facteurmonitoring/inbox'); 
   
   

   if ($hote_imap!="" && function_exists('imap_open')) {
          
          // on se connecte en IMAP pour tester la connection
          include_spip("lib/PhpImap/MailBox");
      
          $connection = '{'.$hote_imap.':'.$hote_port.'}'.$hote_inbox;
          $mailbox = new PhpImap\Mailbox($connection, $email, $email_pwd);
          
          try {
              $mailsIds = $mailbox->searchMailBox('NEW');
              return _T('facteurmonitoring:test_connection_ok');   
          } catch(Exception $e){
              return _T('facteurmonitoring:test_connection_notok');   
          }

           
   }
   
   return;
}

?>