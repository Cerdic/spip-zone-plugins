<?php
/**
 * Gestion du génie Publication par email
 *
 * @plugin Publication par email
 * @license GPL
 * @package SPIP\Emailtospip\Genie
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_emailtospip_dist($t){
   // chargement configuration
   include_spip('inc/config');

   $email = lire_config('emailtospip/email'); 
   $email_pwd = lire_config('emailtospip/email_pwd');
   $hote_imap = lire_config('emailtospip/hote_imap'); 
   $hote_port = lire_config('emailtospip/hote_port'); 
   $hote_inbox = lire_config('emailtospip/inbox'); 
   $pwd = lire_config('emailtospip/pwd');     
   
   if (lire_config('emailtospip/import_statut')=="publie") $import_statut = "publie";  else $import_statut = "prop";
   $id_rubrique = intval(lire_config('emailtospip/id_rubrique'));
   $id_secteur  = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
   $lang = lire_meta("langue_site");
   
   
   $limit = 20; // max d'emails à traiter en une passe (pour éviter le timeout)
      
   if ($hote_imap!="") {  
          // connection or die          
          $connection = '{'.$hote_imap.':'.$hote_port.'}'.$hote_inbox;
          $mbox = @imap_open($connection, $email, $email_pwd);
          
          if (FALSE === $mbox) {
                return false;                
          } else {
                // lecture message 
                //      // listing boite
                $info = imap_check($mbox);
                if (FALSE === $info) {
                    return false; // Impossible de lire le contenu de la boite mail                     
                } else {
                    $nbMessages = min(50, $info->Nmsgs);
                    $mails = imap_fetch_overview($mbox, '1:'.$nbMessages, 0);  
                   
                    $i=0;
                    foreach ($mails as $mail) {           
                        $sujet = imap_utf8_fix($mail->subject);
                        $uid = $mail->uid;                        
                        if (preg_match_all("#<(.*?)>#ims",$mail->from, $matches,PREG_SET_ORDER))    // buzz <buzz@buzz.org> ->  buzz@buzz.org
                                  $email_from = $matches[0][1];
                            else  $email_from = $mail->to;
                        
                        // en mode mot de passe, ne selectionner que les emails avec le mot titre                         
                        if ($pwd!="") {
                            if (substr($sujet,0,strlen($pwd)) == $pwd)  {
                                $sujet = substr($sujet,strlen($pwd));
                                $import = true;
                            } else  {
                                $import = false;
                            }
                        } else {
                            $import = true;
                        }
                        
                        if ($import && $i++<$limit) {                              
                            emailtospip_mail($uid,$mbox,$sujet,$email_from,$import_statut,$id_rubrique,$id_secteur,$lang); 
                         }
                       
                        
                    }  #foreach                                        
                    imap_close($mbox,CL_EXPUNGE);
                    return true; 
                    
                }  
          }
   }
   
  return 1;
}


// bug de casse  
// http://docs.php.net/manual/fr/function.imap-utf8.php
function imap_utf8_fix($string) {
    return iconv_mime_decode($string,0,"UTF-8");
} 

//
// import un email en tant qu'article spip 
//        puis efface l'email de la boite
// 
// @uid   uid de l'email
// @mbox  connection imap
// @sujet sujet de l'email
// @email email de l'expediteur
// @.... 
function emailtospip_mail($uid,$mbox,$sujet,$email,$import_statut,$id_rubrique,$id_secteur,$lang) {
    include_spip('inc/texte'); // pour safehtml
   
    // lecture de l'email    
    $headerText = imap_fetchHeader($mbox, $uid, FT_UID);
    $header = imap_rfc822_parse_headers($headerText);
    
    // REM: Attention s'il y a plusieurs sections
    $structure = imap_fetchstructure($mbox, $uid, FT_UID); 
        
    //$corps = imap_fetchbody($mbox, $uid, 2, FT_UID);  // 1: plain text 2: html
    $corps = imap_body($mbox, $uid, FT_UID);
    $corps = quoted_printable_decode($corps); 
    
    // si on est sur HTML, extrait le body
    if ($structure->subtype != "PLAIN") {
          $pattern = "#<body[^>]*>(.*?)<\/body>#ims";
          if (preg_match_all($pattern, $corps, $matches,PREG_SET_ORDER))  {
               $corps = $matches[0][1];
          } else {
              // mmmm ... rien de recupere ... on quitte le navire
              return false;
          }
    
    }

    // ....dans la table articles 
    $date =  date('Y-m-d H:i:s',time());                               	
    $id_nouvel_article = sql_insertq("spip_articles",array(
                                              'lang' => $lang,
                                              'titre' => safehtml($sujet),
                                              'id_rubrique' => $id_rubrique,
                                              'id_secteur' => $id_secteur,                                              
                                              'texte' => safehtml($corps),       // utiliser une filtrage genre sale ?                                         
                                              'statut' => $import_statut,
                                              'accepter_forum' => 'non',
                                              'date' => $date
                                              )); 
    // ... l'auteur est connu ?
    if ($id_auteur  = sql_getfetsel("id_auteur", "spip_auteurs", "email='$email'")) {
           sql_insertq("spip_auteurs_liens",array(
                                              'id_auteur' => $id_auteur,
                                              'id_objet' => $id_nouvel_article,
                                              'objet' => 'article',
                                              'vu' => 'non'
                                              ));
    }  
    
    // on supprime l'email  
    imap_delete($mbox, $uid, FT_UID);                                                                         
    
    return true;
} 





?>