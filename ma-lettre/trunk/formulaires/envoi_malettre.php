<?php
/**
 * Formulaire pour envoi la lettre (avec ou sa facteur)
 */

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/distant');


/**
 * Chargement des valeurs par défaut du formulaire  
 */
function formulaires_envoi_malettre_charger_dist(){
  if (_request('lettre_title'))
                         $lettre_title = strip_tags(_request('lettre_title'));
                  else   $lettre_title = "";


  $sourcehtml = recuperer_page(lire_meta("adresse_site")."/IMG/lettre/_malettre.html");
  $sourcetxt = recuperer_page(lire_meta("adresse_site")."/IMG/lettre/_malettre_txt.html");

  $contexte = array(
    'lettre_title'=>$lettre_title,
    'expediteur' => '',
    'expediteur_more' => '',
    'desti' => '',
    'desti_more' => '',
    'sourcehtml'=>$sourcehtml,
    'sourcetxt'=>$sourcetxt   
    );
    
	return $contexte; 
 
}

/**
 * Vérification des valeurs du formulaire
 */
function formulaires_envoi_malettre_verifier_dist(){
	$erreurs = array();
  
  if (_request('lettre_title')=="")
         $erreurs['lettre_title'] = _T("malettre:obligatoire");
  if ((_request('expediteur')=="") AND (_request('expediteur_more')==""))
         $erreurs['expediteur'] = _T("malettre:obligatoire");       
  if ((_request('desti')=="") AND (_request('desti_more')==""))
         $erreurs['desti'] = _T("malettre:obligatoire");        
         
	return $erreurs;
}

/**
 * Traitement des valeurs du formulaire
 */
function formulaires_envoi_malettre_traiter_dist(){

  include_spip("inc/charsets"); 
  include_spip("inc_presentation");
  include_spip('inc/config');

  
  $message = "";
  
  // --------------------------------
            // chemin
            $path = _DIR_IMG;
            $path_archive = "lettre";
            $path_archive_full = $path.$path_archive;
            $path_url = lire_meta("adresse_site");
            $path_url_archive = $path_url."/IMG";
          
            $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__file__))));
            define('_DIR_PLUGIN_MALETTRE',(_DIR_PLUGINS.end($p)));
            $path_plugin = dirname(__file__)."/../";
  
  
  		      //
	          // envoi de la lettre
	          //
            
            // lang
            $lang = _request('lang_select');
            if ($lang=="")  
                          $lang = $GLOBALS['meta']['langue_site'];
		        
		        // titre 
		        $lettre_title = trim(strip_tags(_request('lettre_title'))); 
            $lettre_title = str_replace("\"","'", $lettre_title);  
            if ($lettre_title == "") {  // à supprimer ou integrer ou multilingue ?
              $months=array(1=>'Janvier', 2=>'Fevrier', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Aout', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Decembre');
              $today = getdate(mktime()-(24*3600));
              $sujet = "Les nouveautes de ".$months[$today[mon]]." ".date("Y");
            } else {
              $sujet = $lettre_title;
            }
            
            
            // hash            
            $lettre_hash = substr(md5(time()),0,5);
            $url_lettre_archive_short = "lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".html";
            $url_lettre_archive_txt_short = "lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".txt";
            $url_lettre_archive = "$path_url_archive/$path_archive/$url_lettre_archive_short";
            $url_lettre_archive_txt = "$path_url_archive/$path_archive/$url_lettre_archive_txt_short";
            
            // recup contenu HTML
            $texte = $path_archive_full."/_malettre.html";
            $fr=fopen($texte,"r");
            while(!feof($fr)){
                  $recup = '';
                  while(!feof($fr))  
                          $recup .= fgets($fr,1024);
            }
            fclose($fr);
            $recup = str_replace("{URL_MALETTRE}",$url_lettre_archive,$recup);
            $recup = str_replace("{TITRE_MALETTRE}",$sujet,$recup);
           
            // recup contenu TXT
            $texte = $path_archive_full."/_malettre_txt.html";
            $fr=fopen($texte,"r");
            while(!feof($fr)){
                  $recup_txt = '';
                  while(!feof($fr))
                        $recup_txt .= fgets($fr,1024);
            }
            fclose($fr);
            $recup_txt = str_replace("{URL_MALETTRE}",$url_lettre_archive,$recup_txt);
            
            // recup  expediteur
            $exp_email = _request('expediteur_more');
            if ($exp_email=="") {
               $id_expediteur = intval(substr(_request('expediteur'),1)); 
                $exp_name  = lire_config("malettre/expediteur_nom$id_expediteur"); 
                $exp_email = lire_config("malettre/expediteur_email$id_expediteur");
                if ($exp_email=="") 
                   die("expediteur inconnu");               
            } else {
              $exp_name = $exp_email;               
            } 
            
               
            // recup destinataire
            $destinataire = array();
            $desti = _request('desti');
            foreach ($desti as $desti_item) {     // on lit la config pour retrouver l'email
                $id_desti = intval(substr($desti_item,1)); 
                $desti_email = lire_config("malettre/adresse_email$id_desti"); 
                if ($desti_email !="") 
                      $destinataire[] = $desti_email;            
            }
            
            $desti_more = _request('desti_more'); 
            if ($desti_more!="") $destinataire[] = $desti_more;
             /*    FIXME:   a finaliser : if (!defined('_DIR_PLUGIN_MESABONNES ...
            if (_request('mes_abonnes')=='oui') {
                if ($resultats = sql_select('email', 'spip_mesabonnes')) {
                	while ($res = sql_fetch($resultats)) 
                		            $desti[] = $res['email'];                	
                }
            } 
             */
           
            
            $message = "<h3>"._T('malettre:envoi')." : <span style='font-weight:normal;font-size:12px;'>$sujet</span></h3>\n";
            $message .= "<div style='border:1px solid;background:#eee;margin:10px 0;padding:10px;font-family:arial,sans-serif;font-size:0.9em;'>";
            
           
            // envoi lettre
            // a ameliorer grandement flood
            // utiliser une methode ajax pour temporiser l'envoi par flot
            // ou tout simple deleger a facteur ? 
            $i = 0;
            $j = 0;          
            if (is_array($destinataire)) {
              foreach ($destinataire as $k=>$adresse) { // envoi a tous les destinataires
	              if (!defined('_DIR_PLUGIN_FACTEUR')){                    
	                include_spip("lib/class.phpmailer");	// mettre à jour http://code.google.com/a/apache-extras.org/p/phpmailer/ ou necessite facteur ?			 
                  $mail = new PHPMailer();

	                $mail->From     = "$exp_email";
	                $mail->FromName = "$exp_name";
	                $mail->AddReplyTo("$exp_email");
	                $mail->AddAddress($adresse,$adresse);
	                $i++;

	                $mail->WordWrap = 50;           // set word wrap
	                $mail->IsHTML(true);            // send as HTML
	                $mail->CharSet = "utf-8";

	                $mail->Subject  =  "$lettre_title";
	                $mail->Body     =  $recup;
	                $mail->AltBody  =  $recup_txt;
		              $res = $mail->Send();
	              } else {    // envoi via facteur
		              $envoyer_mail = charger_fonction('envoyer_mail','inc');
		              $corps = array(
			              "html" => $recup,
			              "texte" => $recup_txt,
			              "nom_envoyeur" => $exp_name,
			              "from" => $exp_email			              
		              );
		              $res = $envoyer_mail($destinataire,$lettre_title,$corps);
	              }

                if (!$res) {
                    $message.= "<div style='color:red'><strong>$adresse</strong> - "._T('malettre:erreur_envoi')."</div>";  
                    //$message.= "Mailer Error: " . $mail->ErrorInfo; 
                    $success_flag = false;
                    $j++;
                } else {  
                    $message.= "<div style='color:green'><strong>$adresse</strong> - <span style='color:green'>"._T('malettre:succes_envoi')."</span></div>";         
                }
                echo $msg;
              }
            } else {
              $message.= "<div style='color:red'>"._T('malettre:erreur_no_dest')."</div>";
            }
            $message.= "</div>";
            
            // $message.= "<div> $i / $j </div>";
            
            // archivage de la lettre en dur    
            // FIXME: utiliser les methodes natives pour ecrire les fichiers   
            $no_archive = _request('no_archive');            
            if (!is_array($no_archive)) { 
                   
                  $message.= "<div style=\"margin:15px 0;\">"._T('malettre:archives_placer');
                  
                  $lettre_archive = "$path_archive_full/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".html";
                  $f_archive=fopen($lettre_archive,"w");
                  fwrite($f_archive,$recup); 
                  fclose($f_archive);
                  $message.= " <a href='$lettre_archive' target='_blank'>html</a> - ";
                 
                  $lettre_archive = "$path_archive_full/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".txt";
                  $f_archive=fopen($lettre_archive,"w");
                  fwrite($f_archive,$recup_txt); 
                  fclose($f_archive);
                  $message.= "<a href='$lettre_archive' target='_blank'>txt</a></div>";
                  
                  // stockage en base
                  include_spip('base/abstract_sql');
                  
                  sql_insertq('spip_meslettres',array(
                              'titre' => $lettre_title,
                              'lang' => $lang,
                              'url_html' => "lettre/$url_lettre_archive_short",
                              'url_txt' => "lettre/$url_lettre_archive_txt_short",
                              'date' => date('Y-m-d H:i:s')
                              ));
                                      
                             
                  $message.= "<p><a href='".generer_url_ecrire("malettre_archive")."'>"._T('malettre:archives_gerer')."</a></p>\n";
            
            }
  
  // --------------------------------

  
  
  
  
  $redirect = "";

	// message
	return array(
		"editable" => false,
		"message_ok" => "$message",
    'redirect' => $redirect
	);
}

?>
