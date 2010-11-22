<?php
// ---------------------------------------------------------
//  Ma lettre
// ---------------------------------------------------------


include(dirname(__FILE__).'/../inc_malettre.php');

include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/filtres');
include_spip('inc/lang');

// -------------------------------
// Main: Ma Lettre
// -------------------------------

function exec_malettre(){ 
  global $connect_statut;
	global $connect_toutes_rubriques;
 
  include_spip("inc/charsets"); 
  include_spip("inc_presentation");
  
  // chemin
  $path = _DIR_IMG;
  $path_archive = "lettre";
  $path_archive_full = $path.$path_archive;
  $path_url = lire_meta("adresse_site");
  $path_url_archive = $path_url."/IMG";

  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__file__))));
  define('_DIR_PLUGIN_MALETTRE',(_DIR_PLUGINS.end($p)));
  $path_plugin = dirname(__file__)."/../";
  
  // parametre par defaut (editable a la main)
  $id_article_edito  = 1;  // edito lettre
  $adresses = array(
        "Pierre Durand" => "pdurand@domaine.tld",
        "John Doe" => "jdoe@domaine.tld",
  ); 
   
  $expediteurs = array(
         "Liste A" => "liste-a@domaine.tld",
         "Liste B" => "liste-a@domaine.tld",
  );   

  // si cfg dispo, on charge les valeurs
  if (function_exists(lire_config))  {
      $id_article_edito = lire_config('malettre/id_article_edito');
      $expediteurs = array();
      for ($i=1;$i<4;$i++) {
        if (trim(lire_config("malettre/expediteur_email$i"))!="")
              $expediteurs[lire_config("malettre/expediteur_nom$i")] = lire_config("malettre/expediteur_email$i");
      }
      $adresses = array();
      for ($i=1;$i<8;$i++) {
        if (trim(lire_config("malettre/adresse_email$i"))!="")
              $adresses[lire_config("malettre/adresse_nom$i")] = lire_config("malettre/adresse_email$i");
      }
  } 
    
  

  // main ------------------------------------------------------  
	$commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('malettre:ma_lettre'),_T('malettre:ma_lettre'),_T('malettre:ma_lettre'));	  
 
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {	  // admin restreint (connect_toutes_rubriques si admin)
		$page = "malettre";	

    echo debut_gauche('', true);   
    debut_boite_info(true);
    echo "<p>"._T('malettre:info')."</p>";
    echo "<p><a href='?exec=malettre'>"._T('malettre:ecrire_nouvelle')."</a></p>";
    echo "<p><a href='?exec=malettre&amp;agir=letter_compose&amp;option=load'>"._T('malettre:charger_derniere')."</a></p>";
    echo "<p><a href='?exec=malettre_archive'>"._T('malettre:archives_gerer')."</a></p>";
    if (function_exists(lire_config)) echo "<p><a href='?exec=cfg&cfg=malettre'>"._T('malettre:config')."</a></p>";  
    fin_boite_info(true);
    
    echo debut_droite('', true);
 
		$agir = _request('agir');
		if ($agir == "letter_compose") {      // compose la lettre
		        $errorFlag = false;		
      
						$option = _request('option');
						if ($option=="load") {         // on charge la derniere lettre
						
						    // recup contenu HTML
                $texte = $path_archive_full."/.malettre.html";
                $fr=fopen($texte,"r");
                while(!feof($fr)){
                      $sourceHTML  = '';
                      while(!feof($fr))  
                              $sourceHTML  .= fgets($fr,1024);
                }
                fclose($fr);
                
                // recup contenu TXT
                $texte = $path_archive_full."/.malettre_txt.html";
                $fr=fopen($texte,"r");
                while(!feof($fr)){
                      $sourceTXT  = '';
                      while(!feof($fr))
                            $sourceTXT .= fgets($fr,1024);
                }
                fclose($fr);
            
            } else {                       // on cree la lettre avec la requete web
            
            		include_spip('public/assembler');
                include_spip('inc/charsets');
            
    						$lettre_title = trim(strip_tags(_request('lettre_title'))); 
                $lettre_title = str_replace("\"","'", $lettre_title);
                
                $lang = _request('lang_select');
                if ($lang=="")  
                          $lang = $GLOBALS['meta']['langue_site'];
                
                            
      					// VERSION HTML & TXT
      					$sourceHTML = "";
      					$sourceTXT  = "";
      					$selection = ""; // stocke les id des articles retenus separee par une virgule
                
    						    
                // radio button
                $add = _request('add');
                if (is_array($add))   
                    $selection = implode(",", $add);
      							
                // csv							
      					$art_csv = _request('art_csv'); 
      					$csv = explode(",", $art_csv);
      					if (is_array($csv)) {							  
      							foreach ($csv as $value2) {								
        					   	$selection .= ",".trim($value2);
        						}
      					}	
                
                // calcul du patron				
    						$sourceHTML .= malettre_get_contents("malettre",$id_article_edito,$selection,$lang);                 
    						$sourceTXT  .= malettre_get_contents("malettre_txt",$id_article_edito,$selection,$lang); 
      							
      					// ecriture fichier       											
    						if ($handle = fopen($path_archive_full."/.malettre.html", w)) { 						    
      							fwrite($handle, $sourceHTML);					
      							fclose($handle); 
                    
                    if ($handle = fopen($path_archive_full."/.malettre_txt.html", w)) { 			
        							fwrite($handle, $sourceTXT);					
        							fclose($handle);
      						  } else {
                      $errorFlag = true;
                      echo _T('malettre:erreur_ecriture')."($path.$path_archive)";
                    }	                     							
                } else {
                    $errorFlag = true;
                    echo _T('malettre:erreur_ecriture')."($path.$path_archive)";                    
    						}							
        				

    						
    				}  // fin examen requete		
    				
							
					  // affichage ?
					  if (!$errorFlag) {
						  $str = "<form method='post' action='?exec=malettre'><fieldset>\n"; 
              $str .= "<input type='hidden' name='lang_select' value='$lang' />"; 
						  $str .= "<input type='hidden' name='agir' value='letter_send' />\n";
              $str .= "<h4>"._T('malettre:expediteur')."</h4>\n";
              $str .= "<select name='expediteur'>\n";
							foreach ($expediteurs as $expediteur=>$val){
                  $str .= "<option value=\"$expediteur\" />".htmlentities($expediteur)." &lt;".htmlentities($val)."&gt;</option>";
              } 
              $str .= "</select>\n";   
              $str .= "<br />"._T('malettre:autre')." <input type='text' name='expediteur_more' />("._T('malettre:email_seulement').")\n" ;  
               
              // destinataires                
             	$str .= "<h4>"._T('malettre:destinataires')."</h4>\n";
             	// connection plugin abonnes s'il existe
              if (isset($GLOBALS['meta']['mesabonnes_base_version'])) {
                  $inscrits =  sql_countsel("spip_mesabonnes");
                  $str .= "<input type='checkbox' value='oui' name='mes_abonnes' /><strong>"._T('malettre:mes_abonnes',array('inscrits'=>"$inscrits"))."</strong><br />";
              }
							foreach ($adresses as $adresse=>$val){
                  $str .= "<input type='checkbox' value=\"$val\"' name='desti[]' />$adresse &lt;$val&gt;<br />";
              }              
              $str .= _T('malettre:autre')." <input type='text' name='desti_more' /> ("._T('malettre:email_seulement').")<br /><br />\n";
              
              // fin formulaire
              $str .= "<input type='submit' name='sub' value='Envoyer la lettre' /> \n";
              $str .= "<input type='button' name='sub' value='Ecrire une nouvelle lettre' onclick=\"javascript:document.location.href='?exec=malettre'\"  /> ";
              
							$str .= "<h4>"._T('malettre:apercu')."</h4>\n";
							$str .= "Sujet: <input type='text' size='55' name='lettre_title' value=\"".$lettre_title."\" /><br />\n";
              $str .= "<iframe width=\"750\" height=\"500\" src=\"$path_archive_full/.malettre.html?nocache=".time()."\"></iframe>\n";
							$str .= "<h4>"._T('malettre:version_html')."</h4>\n";
							$str .= "<textarea cols='70' rows='20'>$sourceHTML</textarea>";
							$str .= "<h4>"._T('malettre:version_txt')."</h4>\n";
							$str .= "<textarea cols='70' rows='20'>$sourceTXT</textarea>";
							$str .= "</fieldset></form>\n";
							
							echo $str;
						} 					
						
   } else if ($agir=='letter_send') {
		        //
	          // envoi de la lettre
	          //
	          include(dirname(__FILE__)."/../class.phpmailer.php");
		        
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
            $url_lettre_archive = "$path_url_archive/$path_archive/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".html";
            $url_lettre_archive_txt = "$path_url_archive/$path_archive/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".txt";
            
            // recup contenu HTML
            $texte = $path_archive_full."/.malettre.html";
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
            $texte = $path_archive_full."/.malettre_txt.html";
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
                $expediteur = _request('expediteur');                
                if (isset($expediteurs[$expediteur])) {
                   $exp_name = $expediteur;
                   $exp_email = $expediteurs[$expediteur];
                }  else  die("expediteur inconnu");
            } else {
              $exp_name = $exp_mail;
            }  
                 
            // recup destinataire
            $desti = _request('desti');
            $desti_more = _request('desti_more'); 
            if ($desti_more!="") $desti[] = $desti_more;
            
            if (_request('mes_abonnes')=='oui') {
                if ($resultats = sql_select('email', 'spip_mesabonnes')) {
                	while ($res = sql_fetch($resultats)) 
                		            $desti[] = $res['email'];                	
                }
            } 
            
            
            echo "<h3>"._T('malettre:envoi')." <i style='color:#999;'>$sujet</i></h3>\n";
            echo "<div style='border:1px solid;background:#eee;margin:10px 0;padding:10px;font-family:arial,sans-serif;font-size:0.9em;'>";
            
            // envoi lettre
            // a ameliorer grandement flood
            // utiliser une methode ajax pour temporiser l'envoi par flot
            // ou tout simple deleger a facteur ? 
            $i = 0;
            $j = 0;          
            if (is_array($desti)) {
              foreach ($desti as $k=>$adresse) { // envoi a tous les destinataires
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
                             
                if(!$mail->Send()) {
                    $msg = "<div style='color:red'><strong>$adresse</strong> - "._T('malettre:erreur_envoi')."</div>";  
                    //$msg .="Mailer Error: " . $mail->ErrorInfo; 
                    $success_flag = false;
                    $j++;
                } else {  
                    $msg = "<div style='color:green'><strong>$adresse</strong> - <span style='color:green'>"._T('malettre:succes_envoi')."</span></div>";         
                }
                echo $msg;
              }
            } else {
              echo "<div style='color:red'>"._T('malettre:erreur_no_dest')."</div>";
            }
            echo "</div>";
            
            echo "<div> $i / $j </div>";
            
            // archivage de la lettre en dur
            echo "<div style=\"margin:15px 0;\">"._T('malettre:archives_placer');
            
            $lettre_archive = "$path_archive_full/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".html";
            $f_archive=fopen($lettre_archive,"w");
            fwrite($f_archive,$recup); 
            fclose($f_archive);
            echo " <a href='$url_lettre_archive' target='_blank'>html</a> - ";
           
            $lettre_archive = "$path_archive_full/lettre_".date("Ymd")."_".$lettre_hash."_"._request('lang_select').".txt";
            $f_archive=fopen($lettre_archive,"w");
            fwrite($f_archive,$recup_txt); 
            fclose($f_archive);
            echo "<a href='$url_lettre_archive_txt' target='_blank'>txt</a></div>";
                                
                       
            echo "<p><a href='?exec=malettre'>"._T('malettre:ecrire_nouvelle2')."</a></p>\n";
            
    } else {	//
	            // pas d'agir: affichage des articles pour composition de la lettre
	            //
	            
	            // verif si repertoire stockage dispo
	            if (!is_dir($path_archive_full)) {                                     
                   if (!mkdir ($path_archive_full, 0777)) // on essaie de le creer  
                        echo "<div style='color:red;padding:5px;border:1px solid red;>"._T('malettre:erreur_ecrire_stockage')."($path_archive_full)</div>"; 
              }
                            
              $lang_select = _request('lang_select');
              if ($lang_select!="") $cond_lang_sql = "AND lang='$lang_select'";
                              else  $cond_lang_sql = "";
              
              $result = sql_select(
               "id_article,titre","spip_articles",
               "statut = 'publie' $cond_lang_sql","", 
               "id_article DESC", "0,50" 
              ); 
                                      
	                							
              echo "<form method='post' agir='?exec=malettre'>"; 
              echo "<input type='hidden' name='agir' value='letter_compose' />";
              echo "<input type='hidden' name='lang_select' value='$lang_select' />";
              echo "<fieldset>\n";              
              
              if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui")
      				      $active_langs = explode(",",$GLOBALS['meta']['langues_multilingue']);
      			  else	$active_langs = "";
      				
      				 if (is_array($active_langs)) {
      				       echo _T('malettre:choix_lang');
                     foreach($active_langs as $k=>$active_lang) {
                        if ($lang_select==$active_lang) echo "<strong>[$active_lang]</strong> ";  
                                         else echo "<a href='?exec=malettre&amp;lang_select=$active_lang'>[$active_lang]</a> ";
                        
                     }
                     if ($lang_select=="") echo "<strong>["._T('malettre:lang_toute')."]</strong> ";             
                                     else  echo "<a href='?exec=malettre'>["._T('malettre:lang_toute')."]</a>";
               }
      				
                
								
								echo "<br /><br />"._T('malettre:compose_sujet')." :<i>("._T('malettre:compose_non_spip').")</i><br />\n";
								echo "<input type='text' size='55' name='lettre_title' /><br />\n";
								echo "<br />"._T('malettre:compose_contenu')." - <a href='?exec=articles&amp;id_article=$id_article_edito'>"._T('malettre:compose_edito')."</a><br />\n";								
								echo "<iframe width='600' height='500' src='$path_url/spip.php?page=malettre_edito&amp;id_article=$id_article_edito'></iframe>\n";
								echo $stro;								
							
								echo "<br />"._T('malettre:compose_cochant');
                echo "<table class='spip' style='width:100%;border:0;'>";
                        
                                //affichage des 50 documents 
                                while($row = sql_fetch($result)){
                                        $id_document=$row['id_article'];                                        
                                        $titre=charset2unicode($row['titre']);  // BUG pb de charset  filtrer_entites ?
                                        
                                        if ($compteur%2) $couleur="#FFFFFF";
                                        else $couleur="#EEEEEE";
                                        $compteur++;
                                        
                                        echo "<tr width=\"100%\"><td bgcolor='$couleur'>";
                                        echo "<a href='?exec=articles&amp;id_article=$id_document'>";
                                        echo typo("n&deg;".$id_document." - ".$titre);
                                        echo "</a>";                
                                        echo "</td>";										
                                        echo "<td align='center' bgcolor='$couleur'><input type=checkbox name=add[] value=\"$id_document\"></TD>";
                                        echo "</tr>\n";
                                }
								
								echo "<tr><td>"._T('malettre:compose_liste')."<br />";
								echo "<textarea rows='15' cols='50' id='art_csv' name='art_csv'></textarea></td></tr>";
								
                                
                echo "</table><br /><input type='submit' value='"._T('malettre:compose_submit')."' />\n";
								echo "</fieldset>\n";
								echo "</form>\n\n";

		}
		//--	

		
	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
	
	echo fin_page();
}

?>