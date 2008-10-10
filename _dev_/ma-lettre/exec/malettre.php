<?php
// ---------------------------------------------------------
//  Ma lettre
//
//  version:  0.90 
//  author:   erational <http://www.erational.org>
//  licence:  GPL
// ---------------------------------------------------------

//  TODO:
//  - multilinguisme


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
  include_ecrire("inc_presentation");
  
  // chemin
  $path = _DIR_RACINE;  
  $path_archive = "IMG/lettre";
  $path_archive_full = $path.$path_archive;
  $path_url = lire_meta("adresse_site");


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
      for ($i=1;$i<6;$i++) {
        if (trim(lire_config("malettre/adresse_email$i"))!="")
              $adresses[lire_config("malettre/adresse_nom$i")] = lire_config("malettre/adresse_email$i");
      }
  } 
    
  

  // main ------------------------------------------------------
  
	echo debut_page(_T('malettre:ma_lettre'));	  
 
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {	  // admin restreint (connect_toutes_rubriques si admin)
		$page = "malettre";	

    debut_gauche();
    echo debut_boite_info();
    echo "Cette page permet de cr&eacute;er une lettre sur mesure en choisissant vos articles. <br/><br/>";
    echo "<p><a href='?exec=malettre'>Ecrire une lettre</a></p>";
    echo "<p><a href='?exec=malettre&amp;action=letter_compose&amp;option=load'>Charger la derni&egrave;re lettre</a></p>";
    echo "<p><a href='?exec=malettre_archive'>G&eacute;rer les archives</a></p>";
    if (function_exists(lire_config)) echo "<p><a href='?exec=cfg&cfg=malettre'>Configurer</a></p>";  
    echo fin_boite_info();
    
    echo debut_droite();
    
    // list last articles
		$titre = "titre";
		$table = "spip_articles"; 
		$id = "id_article";
		$statut = "publie"; 
		$temps = "id_article"; 
		$page_voir = "?exec=articles&id_article=";
    $page_edit = "?exec=articles_edit&id_article="; 
		//--		

		$action = _request('action');
		if ($action == "letter_compose") {      // compose la lettre
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
            
                $add = _request('add');
    						$lettre_title = trim(strip_tags(_request('lettre_title'))); 
                $lettre_title = str_replace("\"","'", $lettre_title);
                
                            
      					// VERSION HTML
      					$sourceHTML = "";
								$sourceHTML .= malettre_get_contents("malettre_header");                   // head
                $sourceHTML .= malettre_get_contents("malettre_edito", $id_article_edito); // edito
    						    
                // radio button
                if (strlen($add)>0) {   
      							foreach ($add as $value)
      								    $sourceHTML .= malettre_get_contents("malettre_item", $value);
      					}  							
      							
                // csv							
      					$art_csv = _request('art_csv'); 
      					$csv = explode(",", $art_csv);
      					if (strlen($csv)>0) {							  
      							foreach ($csv as $value2) {								
        						$value = trim($value2);								
          						if ($value!="") 	
              								$sourceHTML .= malettre_get_contents("malettre_item",$value);
        						}
      					}					
    						$sourceHTML .= malettre_get_contents("malettre_footer");                             // foot
      							
      					// ecriture fichier       											
    						if ($handle = fopen($path_archive_full."/.malettre.html", w)) {						    
      							fwrite($handle, $sourceHTML);					
      							fclose($handle);  							
                } else {
                    $errorFlag = true;
                    echo "<strong>erreur:</strong> impossible de cr&eacute;er la lettre au format HTML, v&eacute;rifier le param&egrave;tre chemin d'acc&egrave;s ($path.$path_archive) et les droits en &eacute;criture (chmod 777)";
    						}							
      							
      					// VERSION TXT		
    						if (!$errorFlag) {               
                    $sourceTXT = "";
                                 
        						// head	     			
        						$sourceTXT = malettre_get_contents("malettre_txt_header");               			
                    $sourceTXT .= malettre_get_contents("malettre_txt_edito",$id_article_edito);     // edito         			
        							
        						// radio button
        						if (strlen($add)>0) {
        								foreach ($add as $value) 
        								  $sourceTXT .=  malettre_get_contents("malettre_txt_item",$value); 
        						}
      							
        						// csv							
        						if (strlen($csv)>0) {							  
        								foreach ($csv as $value2) {								
        								$value = trim($value2);								
        								if ($value!="") 	
            								$sourceTXT .=  malettre_get_contents("malettre_txt_item",$value);                 
                        }
        						}
        						
                    // foot
        						$sourceTXT .= malettre_get_contents("malettre_txt_footer"); 
        				
                    if ($handle = fopen($path_archive_full."/.malettre_txt.html", w)) { 			
        							fwrite($handle, $sourceTXT);					
        							fclose($handle);
      						  } else {
                      $errorFlag = true;
                      echo "<strong>erreur:</strong> impossible de cr&eacute;er la lettre au format texte, v&eacute;rifier le param&egrave;tre chemin d'acc&egrave;s ($path.$path_archive) et les droits en &eacute;criture (chmod 777)";
    						    }	
    						}
    				}  // fin examen requete		
    				
							
					  // affichage ?
					  if (!$errorFlag) {
						  echo "<form method='post'><fieldset>\n";  
						  echo "<input type='hidden' name='action' value='letter_send' />\n";
              echo "<h4>Exp&eacute;diteur</h4>\n";
              echo "<select name='expediteur'>\n";
							foreach ($expediteurs as $expediteur=>$val){
                  echo "<option value=\"$expediteur\" />".htmlentities($expediteur)." &lt;".htmlentities($val)."&gt;</option>";
              } 
              echo "</select>\n";   
              echo "<br />autre: <input type='text' name='expediteur_more' /> (email seulement)\n";  
                   
             	echo "<h4>Destinataires</h4>\n";
							foreach ($adresses as $adresse=>$val){
                  echo "<input type='checkbox' value=\"$val\"' name='desti[]' />$adresse &lt;$val&gt;<br />";
              }
              echo "autre: <input type='text' name='desti_more' /> (email seulement)<br /><br />\n";

              echo "<input type='submit' name='sub' value='Envoyer la lettre' /> \n";
              echo "<input type='button' name='sub' value='Ecrire une nouvelle lettre' onclick=\"javascript:document.location.href='?exec=malettre'\"  /> ";
              
							echo "<h4>Apercu</h4>\n";
							echo "Sujet: <input type='text' size='55' name='lettre_title' value=\"".$lettre_title."\" /><br />\n";
              echo "<iframe width=\"600\" height=\"500\" src=\"$path_archive_full/.malettre.html?nocache=".time()."\"></iframe>\n";
							echo "<h4>Version HTML</h4>\n";
							echo "<textarea cols='70' rows='20'>$sourceHTML</textarea>";
							echo "<h4>Version Texte</h4>\n";
							echo "<textarea cols='70' rows='20'>$sourceTXT</textarea>";
							echo "</fieldset></form>\n";
						} 					
						
   } else if ($action=='letter_send') {
		        //
	          // envoi de la lettre
	          //
	          include(dirname(__FILE__)."/../class.phpmailer.php");
		        
		        // titre 
		        $lettre_title = trim(strip_tags(_request('lettre_title'))); 
            $lettre_title = str_replace("\"","'", $lettre_title);  
            if ($lettre_title == "") {
              $months=array(1=>'Janvier', 2=>'Fevrier', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Aout', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Decembre');
              $today = getdate(mktime()-(24*3600));
              $sujet = "Les nouveautes de ".$months[$today[mon]]." ".date("Y");
            } else {
              $sujet = $lettre_title;
            }
            
            // recup contenu HTML
            $texte = $path_archive_full."/.malettre.html";
            $fr=fopen($texte,"r");
            while(!feof($fr)){
                  $recup = '';
                  while(!feof($fr))  
                          $recup .= fgets($fr,1024);
            }
            fclose($fr);
            
            // recup contenu TXT
            $texte = $path_archive_full."/.malettre_txt.html";
            $fr=fopen($texte,"r");
            while(!feof($fr)){
                  $recup_txt = '';
                  while(!feof($fr))
                        $recup_txt .= fgets($fr,1024);
            }
            fclose($fr);
            
            // envoi lettre
            $exp_email = _request('expediteur_more');
            if ($exp=="") {
                $expediteur = _request('expediteur');                
                if (isset($expediteurs[$expediteur])) {
                   $exp_name = $expediteur;
                   $exp_email = $expediteurs[$expediteur];
                }  else  die("expediteur inconnu");
            } else {
              $exp_name = $exp_mail;
            }            
           
            $desti = _request('desti');
            $desti_more = _request('desti_more'); 
            if ($desti_more!="") $desti[] = $desti_more;
                       
            if (is_array($desti)) {
              foreach ($desti as $k=>$adresse) { // envoi a tous les destinataires
                $mail = new PHPMailer();
                         
                $mail->From     = "$exp_email";
                $mail->FromName = "$exp_name";
                $mail->AddReplyTo("$exp_email");
                $mail->AddAddress($adresse,$adresse);
                                
                $mail->WordWrap = 50;           // set word wrap
                $mail->IsHTML(true);            // send as HTML
                $mail->CharSet = "utf-8"; 
                
                $mail->Subject  =  "$lettre_title";
                $mail->Body     =  $recup;
                $mail->AltBody  =  $recup_txt;
                             
                if(!$mail->Send()) {
                    $msg = "<div style='color:red'><strong>$adresse</strong> - Erreur lors de l'envoi du mail</div>";  
                    $msg .="Mailer Error: " . $mail->ErrorInfo; 
                    $success_flag = false;
                } else {  
                    $msg = "<div style='color:green'><strong>$adresse</strong> - $sujet : <span style='color:green'>Lettre bien envoy&eacute;e !</span></div>";         
                }
                 
                echo $msg;
              }
            } else {
              echo "<div style='color:red'>Erreur: aucun destinataire</div>";
            }
            
            // archivage de la lettre en dur
            $lettre_archive = "$path_archive_full/lettre_".date("Ymd").".html";
            $f_archive=fopen($lettre_archive,"w");
            fwrite($f_archive,$recup); 
            fclose($f_archive);
            echo "<div style=\"margin:15px 0;\">Lettre plac&eacute;e en archive (<a href='$path_url/IMG/lettre/lettre_".date("Ymd").".html' target='_blank'>consulter</a>)</div>";
                      
           
            
            echo "<p><a href='?exec=malettre'>Ecrire une nouvelle lettre</a></p>\n";
            
    } else {	//
	            // pas d'action: affichage des articles pour composition de la lettre
	            //
	            
	            // verif si repertoire stockage dispo
	            if (!is_dir($path_archive_full)) {                                     
                   if (!mkdir ($path_archive_full, 0777)) // on essaie de le creer  
                        echo "<div style='color:red;padding:5px;border:1px solid red;>R&eacutepertoire de stockage de la lettre ($path_archive_full) impossible &agrave; cr&eacute;er</div>"; 
              }
                
              $requete = "SELECT $id, $titre FROM $table WHERE statut like '$statut' ORDER BY $temps DESC LIMIT 0,50";
              $result=spip_query($requete);
                                      
              if (spip_num_rows($result) == 0) {
							                    echo "aucun";
              } else {	                							
                echo "<form method='post'>";
                echo "<input type='hidden' name='exec' value='$page' />";
                echo "<input type='hidden' name='page' value='$page' />";
                echo "<input type='hidden' name='action' value='letter_compose' />";
								echo "<fieldset>\n";
								echo "Sujet du mail :<i>(format spip non support&eacute;)</i><br />\n";
								echo "<input type='text' size='55' name='lettre_title' /><br />\n";
								echo "<br />Texte d'introduction  - <a href='$page_edit$id_article_edito'>&eacute;diter ce texte</a><br />\n";
								echo "<iframe width='600' height='500' src='../?page=malettre_edito&amp;id_article=$id_article_edito'></iframe>\n";
								echo $stro;								
							
								echo "<br />Choisissez les articles que vous vous publier dans la lettre\n";
								echo "<br />en cochant ...\n";
                echo "<table class='spip' style='width:100%;border:0;'>";
                        
                                //affichage des 50 documents 
                                while($row=spip_fetch_array($result)) {
                                        $id_document=$row['id_article'];                                        
                                        $titre=charset2unicode($row['titre']);  // BUG pb de charset  filtrer_entites ?
                                        
                                        if ($compteur%2) $couleur="#FFFFFF";
                                        else $couleur="#EEEEEE";
                                        $compteur++;
                                        
                                        echo "<tr width=\"100%\">";
                                        echo "<td bgcolor='$couleur'>";
                                        if (! empty($page_voir)) echo "<a href='$page_voir$id_document'$page_voir_fin>";
                                        echo typo("n&deg;".$id_document." - ".$titre);
                                        if (! empty($page_voir)) echo "</a>";                
                                        echo "</td>";										
                                        echo "<td align='center' bgcolor='$couleur'><input type=checkbox name=add[] value=\"$id_document\"></TD>";
                                        echo "</tr>\n";
                                }
								
								echo "<tr><td>ET / OU <br />indiquer les num&eacute;ros des articles &agrave; publier s&eacute;par&eacute;s par une virgule<br />";
								echo "<textarea rows='15' cols='50' id='art_csv' name='art_csv'></textarea></td></tr>";
								
                                
                echo "</table><br /><input type=submit value='Ajouter &agrave; la lettre' />\n";
								echo "</fieldset>\n";
								echo "</form>\n\n";
              }
		}
		//--	

		
	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
	
	echo fin_page();
}

?>