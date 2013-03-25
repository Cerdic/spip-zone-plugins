<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


//
// tester la connection imap
//                          
function emailtospip_test_imap() {
   include_spip('inc/config');

   $email = lire_config('emailtospip/email'); 
   $email_pwd = lire_config('emailtospip/email_pwd');
   $hote_imap = lire_config('emailtospip/hote_imap'); 
   $hote_port = lire_config('emailtospip/hote_port'); 
   $hote_inbox = lire_config('emailtospip/inbox'); 

   if ($hote_imap!="") {
          // test connection
          $connection = '{'.$hote_imap.':'.$hote_port.'}'.$hote_inbox;
          $mbox = @imap_open($connection, $email, $email_pwd);
          
            if (FALSE === $mbox) {
                return _T('emailtospip:test_connection_notok',array('connection'=>$connection));                
            } else {
                return _T('emailtospip:test_connection_ok');  
            }
   }
   
   return;
}

?>