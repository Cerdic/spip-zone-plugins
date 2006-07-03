<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_import_export()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
 
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
 
 // generation du fichier export ?
if (isset($_POST['export_txt']) && isset($_POST['export_id']) && $connect_statut == "0minirezo" ) {
    $export_id =  $_POST['export_id'];   
    if (intval($export_id)>0) {
        $query="SELECT id_auteur FROM spip_auteurs_articles WHERE id_article='$export_id'";        
				$abonnes = spip_query($query);
				$str_export  = "# spip-listes\r\n"; 
        $str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
				$str_export .= "# liste id: $export_id\r\n";
				$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";				
				while($row = spip_fetch_array($abonnes)) {
					 $abonne = $row['id_auteur'];					 
					 $extras = get_extra($abonne,"auteur");					 
					 if ($extras["abo"]=="html" || $extras["abo"]=="texte") {					    
					     $subquery = "SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur='$abonne' LIMIT 1";
					     $subresult = spip_query($subquery);
					     while ($subrow = spip_fetch_array($subresult)) {
					       $str_export .= $subrow['email']."\r\n";					       
              			 }					     
           			}             
        		}
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
        echo $str_export;   
        exit;               
         
    }
    
    else{
           
           if($export_id == "abo_sans_liste"){
	
	$abonnes = spip_query("select a.id_auteur, count(d.id_article) from spip_auteurs a 
               left join spip_auteurs_articles d on a.id_auteur = 
                d.id_auteur group by a.id_auteur having count(d.id_article) = 0;");
	  			
		$str_export  = "# spip-listes\r\n"; 
        $str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
				$str_export .= "# liste id: $export_id\r\n";
				$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";				
				while($row = spip_fetch_array($abonnes)) {
					 $abonne = $row['id_auteur'];					 
					 $extras = get_extra($abonne,"auteur");					 
					 if ($extras["abo"]=="html" || $extras["abo"]=="texte") {					    
					     $subquery = "SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur='$abonne' LIMIT 1";
					     $subresult = spip_query($subquery);
					     while ($subrow = spip_fetch_array($subresult)) {
					       $str_export .= $subrow['email']."\r\n";					       
           	    		}					     
           			}             
        		}
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
        echo $str_export;   
        exit; 
     
        }
           if($export_id == "desabo"){
           
           
$query = "SELECT id_auteur, nom, extra FROM spip_auteurs";
$result = spip_query($query);
$nb_inscrits = spip_num_rows($result);
	  			
		$str_export  = "# spip-listes\r\n"; 
        $str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
				$str_export .= "# liste id: $export_id\r\n";
				$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";				
				while($row = spip_fetch_array($result)) {
					 $abonne = $row['id_auteur'];					 
					 $extras = get_extra($abonne,"auteur");					 
					 if ($extras["abo"]=="non" || !$extras["abo"]) {					    
					     $subquery = "SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur='$abonne' LIMIT 1";
					     $subresult = spip_query($subquery);
					     while ($subrow = spip_fetch_array($subresult)) {
					       $str_export .= $subrow['email']."\r\n";					       
           	    		}					     
           			}             
        		}
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
        echo $str_export;   
        exit; 

           
           
           
           }
           
}

    
}  	
// generation du fichier export fin
 
 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>"); 
    echo("<p>Vérifier les étapes d'installation,notamment si vous avez bien renommé <i>mes_options.txt</i> en <i>mes_options.php3</i>.</p>");    
    fin_page();
	  exit;
}

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}


debut_gauche();

spip_listes_raccourcis();

creer_colonne_droite();


debut_droite("messagerie");

// import //  
   function test_login2($mail) {
      	if (strpos($mail, "@") > 0) $login_base = substr($mail, 0, strpos($mail, "@"));
      	else $login_base = $mail;
      
      	$login_base = strtolower($login_base);
      	$login_base = ereg_replace("[^a-zA-Z0-9]", "", $login_base);
      	if (!$login_base) $login_base = "user";
      
      	for ($i = 0; ; $i++) {
      		if ($i) $login = $login_base.$i;
      		else $login = $login_base;
      		$query = "SELECT id_auteur FROM spip_auteurs WHERE login='$login'";
      		$result = spip_query($query);
      		if (!spip_num_rows($result)) break;
	     }

	      return $login;
  }

   $format = $GLOBALS['suppl_abo'];

   
   /// import form
   debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:importer'));
   
   switch ($etape) {
    	    case "2" :
            {
    						
    			if (!$insert_file) $insert_file = $_FILES["insert_file"]["tmp_name"] ;
    			if ($insert_file && $insert_file != "none") {
    			if(!file_exists("./temp"))	mkdir("./temp",0777);
    		                      	else 	chmod("./temp",0777);
    			  $import_file = "./temp/import.txt";
    				if(move_uploaded_file($insert_file,$import_file ))
    				{
    				//	   if(ereg("^php[0-9A-Za-z_.-]+$", basename($insert_file)))
    				if(!empty($insert_file) && $insert_file != "none" && ereg("^php[0-9A-Za-z_.-]+$", basename($insert_file)))
    				$liste = fread(fopen($import_file, "r"), filesize($import_file)); //pour NS et IE
    		 
    				$liste=ereg_replace("\n|\r|\n\r|\r\n|\n\n","\n",$liste);
    				$liste = explode( "\n",$liste);
    				$new_abonne = 0;
    				$sub_report = "";
    		 
    					for($i=0;$i<sizeof($liste); $i++) {
    
    					/* Ajouter un nouvel enregistrement dans la table */
    					$liste[$i]=trim($liste[$i]);
    					  $ligne_nb = ($i+1);
    						if(!empty($liste[$i])){
    		       		     
    						            // Inscription
    						            // Ajouter un code pour retrouver l'abonné                            
                            $mail_inscription = $liste[$i] ;    						
    
                            if(email_valide_bloog($mail_inscription)){
                               
                               $pass = creer_pass_aleatoire(8, $mail_inscription);
    						               $nom_inscription = test_login2($mail_inscription);                                  
                               $login = test_login2($mail_inscription);
          		                 $mdpass = md5($pass);
          		                 $htpass = generer_htpass($pass);
                               $statut = "6forum" ;
                               $cookie = creer_uniqid();
          
                               $extras = bloog_extra_recup_saisie('auteurs');
    
                               $query = "SELECT * FROM spip_auteurs WHERE email='$mail_inscription'";
                						   $resulta = spip_query($query);
                                                
                			 if ($row = spip_fetch_array($resulta)) {
                                     $nom = $row['nom'] ;
                                     $mail = $row['email'] ; 
                                     $id   = $row['id_auteur'] ;                           
                					 echo _T('spiplistes:adresse_deja_inclus').": ";
                					echo "<span style='color:#999;margin-bottom:5px'>".$mail_inscription."</span><br />\n" ; 
                              spip_query("UPDATE spip_auteurs SET extra='$extras' WHERE id_auteur='$id'");
                               }
                                else {                                                
                				 $sub_report .= "<span style='color:#090;margin-bottom:5px'>$mail_inscription</span> ($format)<br />\n";
                               	$query = "INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, extra, cookie_oubli) ".
                                        		"VALUES ('$nom_inscription', '$mail_inscription', '$login', '$mdpass', '$statut', '$htpass', '$extras', '$cookie')";
                          		spip_query($query);                        		
                				}
                                                           
                              // Inscription aux listes
                              // abonnement aux listes http://www.phpfrance.com/tutorials/index.php?page=2&id=13
                              $query = "SELECT * FROM spip_auteurs WHERE email='$mail_inscription'";
                            	$resu = spip_query($query);
                            
                									// l'abonne existe deja.
                									if ($row = spip_fetch_array($resu)) {
                    									$id_auteur = $row['id_auteur'];
                    									$statut = $row['statut'];
                    									$nom = $row['nom'];
                    									$mel = $row['email'];
                	
    									// on abonne l'auteur aux listes
    									
    										if(is_array($list_abo)){
    											reset($list_abo);
    											while( list(,$val) = each($list_abo) ){
    												 //echo "<h2>$nom :liste $val </h2>" ;
    												 $query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$id_auteur' AND id_article='$val'";
    												 $result=spip_query($query);
    												 
    												 
    												 if($GLOBALS['suppl_abo'] !='non'){
    												 $sub_report .= "<span style='color:#090;margin-bottom:5px'>".$mel."</span><br />\n" ;
    												 $query="INSERT INTO spip_auteurs_articles (id_auteur,id_article) VALUES ('$id_auteur','$val')";
    												 }
    												 $result=spip_query($query);
    												 $new_abonne++;
    											}
    								  
    										 }else{
    										 if($GLOBALS['suppl_abo'] =='non'){
	    										$query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$id_auteur'";
    											$result=spip_query($query); 
    										}
    										 }
    									}
                            
    								//
                              
    							} else {
                                 echo " "._T('spiplistes:erreur_import').$ligne_nb.": ";
                                 echo "<span style='color:red;margin-bottom:5px'>".$liste[$i]." : </span><br />\n";
                                }//email valide
    
                            
                            }  //listei
                                              
                         }  // for
    
    
      		 unlink($import_file);
      		  echo "<br />".$sub_report;
    		    echo "<div style='margin:10px 0'><strong>"._T('spiplistes:adresses_importees').": </strong> $new_abonne / $i</div>\n";
    	       }// move et file
    
    	   } // insert
    	   else echo "<br /><br /><center><strong>"._T('spiplistes:erreur')."</strong></center>";
    
    
        echo  "<a href='spip_listes.php3?mode=inout'>["._T('spiplistes:retour_link')."]</a>";
    
        }
        break ;
                
                default :
             if($spip_version < 1.8 ){
                    echo "<h3>"._T('spiplistes:importer')."</h3>" ;
              }
             echo _T('spiplistes:importer_fichier_txt')."<center><div>";
    
      $list = spip_query ("SELECT * FROM spip_articles WHERE statut = 'liste' OR statut = 'inact' ");
      $nb_listes = spip_num_rows($list);
      if($nb_listes == 0){
    	     echo "<fieldset> ";
    	     echo "<legend>"._T('spiplistes:abonnement_newsletter')."</legend>";
           echo _T('spiplistes:importer_preciser');
    	     echo "<form action='$PHP_SELF?etape=2' method='post'  enctype='multipart/form-data' name='importform'> ";        
          bloog_extra_saisie('', 'auteurs', 'inscription');
      } else {  
      echo "<fieldset> ";
      echo "<legend>"._T('spiplistes:abonnement_newsletter')."</legend>";
      echo _T('spiplistes:importer_preciser');
      echo "<div style='text-align:left'>" ;
      echo "<form action='$PHP_SELF?etape=2' method='post' enctype='multipart/form-data'  name='importform'> ";
      while($row = spip_fetch_array($list)) {					
    			$id_article = $row['id_article'] ;
    			$titre = $row['titre'] ;
    			if ($nb_listes = 1) $ischecked = "";
    			               else $ischecked = "checked='checked'";
    			echo "<input type=\"checkbox\" name=\"list_abo[]\" $ischecked value=\"".$id_article."\">\n";
          echo "<a href='?liste=$id_article' title='informations sur cette liste'>$titre</a><br />" ;
    		 
      }
      echo "<br />";
      bloog_extra_saisie('', 'auteurs', 'inscription');
      echo "</div>";
    
    } // fin du test nb listes
    
    
       echo '<!--
      <script language=\"javascript\">
      function Soumettre()
    			{
    			//if  (document.importform.insert_file.value==\"\")
    	        //alert(\"Tous les champs doivent ére remplis\");  
    			//else
      document.importform.fich.value=document.importform.insert_file.value;
      document.importform.submit();
    			 }
      </script>
       --> ';
    
      echo "<h5>"._T('spiplistes:importer_fichier')."</h5>";
      echo "<input type=file name=\"insert_file\"><br /><br />";
      echo "<input type=\"hidden\" name=\"mode\" value=\"inout\">";
      echo "<input type=\"hidden\" name=\"import\" value=\"oui\">";
      echo "</div>" ;
      echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' onclick='Soumettre()'>";
      echo "</form>" ;
    
    
       } // switch
    
      /**************/
    
      echo  "</fieldset></div>";           
    	fin_cadre_relief();
   /// import form end
   
   
   
   // import end //
  
  
  /// export //(added by erational.org)
	// formulaire d'export
  $list = spip_query ("SELECT * FROM spip_articles WHERE statut = 'liste' OR statut = 'inact' ");
  $nb_listes = spip_num_rows($list);
  if ($nb_listes > 0) {	
	   debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:exporter'));
	   echo "<form action='$PHP_SELF?exec=import_export' method='post'>\n";	   
	   while($row = spip_fetch_array($list)) {
					$id_article = $row['id_article'] ;
			     $titre = $row['titre'];
			     if ($nb_listes==1) $checked = " checked='checked'";
			                  else $checked = "";
			    echo "<input type=\"radio\" name=\"export_id\"   value=\"".$id_article."\"$checked>$titre <br />\n"; 
      }      
	   echo "<input type=\"radio\" name=\"export_id\"  value=\"abo_sans_liste\"$checked>Abonnés à aucune liste <br />\n"; 
	   echo "<input type=\"radio\" name=\"export_id\"  value=\"desabo\"$checked>Désabonnés <br />\n"; 
	   echo "<input type='submit' name='export_txt' class='fondo' value='"._T('bouton_valider')."' />\n";
	   echo "</form>\n";
	   fin_cadre_relief();	
	}
	// export end //


// MODE INOUT FIN --------------------------------------------------------------



$spiplistes_version = "SPIP-listes b1.9";
echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$spiplistes_version."<p>" ;

fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>
