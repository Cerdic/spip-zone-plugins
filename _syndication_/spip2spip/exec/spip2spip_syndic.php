<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include(dirname(__FILE__).'/../inc-spip2spip.php');
include_spip('inc/presentation');
/*
include ("inc.php");
include_ecrire ("inc_sites.php");   
include_ecrire ("inc_mail.php"); 
include_ecrire ("inc_getdocument.php"); 
include_ecrire ("inc-spip2spip.php");
*/

//------------------------------- 
// Main
//-------------------------------
function exec_spip2spip_syndic(){
  include_spip("inc/distant"); 
  include_spip("inc/syndic"); 
  include_spip("inc/mail"); 
  
  // Recupere la config
  global $table_prefix;
  // groupe mot cle "licence" installe ? (contrib: http://www.spip-contrib.net/Filtre-Licence )
  if (get_id_groupemot("licence"))  $isLicenceInstalled = true; 
                              else  $isLicenceInstalled = false;
                              
   // HTML go
  debut_page(_T('spiptospip:copy_spip2spip'), "administration", "configuration","contenu");
  echo "<br / ><br />";
  gros_titre(_T('spiptospip:copy_spip2spip'));
  debut_gauche();
  insert_shortcut();
  debut_boite_info();
  echo _T('spiptospip:intro_spip2spip');
  fin_boite_info();
  
  
  debut_droite();
  debut_cadre_relief();
  
  // recuperer les sites à syndiquer
  if (isset($_GET['id_site'])) {         
    $id_site = (int) $_GET['id_site'];
    $sql = "SELECT * FROM ".$table_prefix."_spip2spip WHERE id=$id_site";
  } else {
    $sql = "SELECT * FROM ".$table_prefix."_spip2spip"; // on syndique tous les sites
  }
  $sites = spip_query($sql);  
  while($row_site  = spip_fetch_array($sites)) {
      $current_id   = $row_site["id"];
      $current_site = $row_site["site_titre"];
      $url_syndic   = $row_site["site_rss"];
      $date_syndic  = $row_site["last_syndic"];
      echo "<h4>$current_site (<a href='$url_syndic'>flux</a>)</h4>"; 
      $mailLog = "";  
         
      // Aller chercher les donnees du flux RSS et les analyser
      $rss = recuperer_page($url_syndic, true);
      if (!$rss)  $articles = _T('avis_echec_syndication_02');
          else		$articles = analyser_backend_spip2spip($rss);
      
      // Des articles dispo pour ce site ?
      if (is_array($articles)) {
        foreach ($articles as $article) {
          echo "<ul>\n";        
          // Est que l'article n'a pas été déjà importée ?
          if (isset($article['titre'])) {
            $current_titre = $article['titre'];
            $sql2 = "SELECT COUNT(titre) as c FROM ".$table_prefix."_articles WHERE titre='".addslashes($current_titre)."'";
            $nb_article = spip_fetch_array(spip_query($sql2));
            if ($nb_article['c']!=0) { 
              // article déjà connu
              echo "<li>[<span style='color:#999'>"._T('spiptospip:imported_already')."</span>] $current_titre</li>\n";
            } else {  
              // nouvel article à importer
              echo "<li>[<span style='color:#090'>"._T('spiptospip:imported_new')."</span>] $current_titre<br />\n";
              
              // on cherche la rubrique destination
              $target = get_id_rubrique($article['keyword']);
              if (!$target) {
                  echo "<span style='color:#009'>"._T('spiptospip:no_target')." <strong>".$article['keyword']."</strong></span></li>\n";                    
              } else {
                  // tout est bon, on insert les donnnees ! 
                  
                  // traitement des documents
                  $_documents = $article['documents'];
                  $documents_current_article = array();
                  if ($_documents!="") {
                    $_documents = unserialize($_documents);                  
                    foreach($_documents as $_document) {                      
                        $id_distant = $_document['id'];
                        $source = $_document['url'];
                        $titre = $_document['titre'];
                        $desc = $_document['desc'];                       
                        // inspire de ajouter_un_document () de inc_getdocument.php ?
                        if ($a = recuperer_infos_distantes($source)) {                         
                    			$id_type = $a['id_type'];
                    			$taille = $a['taille'];                  			
                    			$largeur = $a['largeur'];
                    			$hauteur = $a['hauteur'];
                    			$ext = $a['extension'];
                    			$type_image = $a['type_image'];
                    
                    			$distant = 'oui';
                    			$mode = 'document';
                          
                          $sql="INSERT INTO ".$table_prefix."_documents(id_type,titre,date,descriptif,fichier,taille,largeur,hauteur,mode,distant,idx) 
                                                                VALUES ('$id_type',
                                                                        '".addslashes($titre)."',
                                                                        '$_date',
                                                                        '".addslashes($desc)."',
                                                                        '".addslashes($source)."',
                                                                        '$taille',
                                                                        '$largeur',
                                                                        '$hauteur',
                                                                        '$mode',                                                                        
                                                                        '$distant',
                                                                        'oui'                                                                      
                                                                        )";
                          
        				          spip_query($sql);
                          $id_nouveau_doc = spip_insert_id(); 
                          $documents_current_article[$id_distant] = $id_nouveau_doc;                   			
                    		}  
                    }
                  } 
                  
                  // traitement de l'article                             
                  $_surtitre = addslashes($article['surtitre']);
              		$_titre = addslashes($article['titre']);
              		$_soustitre = addslashes($article['soustitre']);
              		$_descriptif = addslashes(convert_extra($article['descriptif'],$documents_current_article));
              		$_chapo = addslashes(convert_extra($article['chapo'],$documents_current_article));
              		$_texte = addslashes(convert_extra($article['texte'],$documents_current_article));
              		$_ps = addslashes(convert_extra($article['ps'],$documents_current_article));
              		$_date =  date('Y-m-d H:i:s',time()); // $article['date'];  // date de la syndication ou date de l'article ?
              		$_lang =  addslashes($article['lang']);
              		$_id_rubrique = $target;            		          		
              		$_statut = STATUT_DEFAUT;
              		$_id_auteur = $article['auteur'];
              		$_link = $article['link'];
              		$_licence = $article['licence'];                           		
              		
              		// on cite la source originale ds le champs ps et la licence
              		$_ps .= addslashes(_T('spiptospip:origin_url'))." [".$_link."->".$_link."]";
              		
                  // licence ?                
                  if ($_licence !="" && !isLicenceInstalled)                               		
                        $_ps .= addslashes(_T('spiptospip:article_license'))." ".$_licence;
                 
                                              	            		
              		// ....dans la table articles             	
              		$sql3 = "INSERT INTO ".$table_prefix."_articles (lang,surtitre,titre,soustitre,id_rubrique,descriptif,chapo,texte,ps,statut,accepter_forum,date) ";
  				        $sql3.="VALUES( '$_lang','$_surtitre','$_titre','$_soustitre',$_id_rubrique,'$_descriptif','$_chapo','$_texte','$_ps','$_statut','pos','$_date')";
  				        spip_query($sql3);
  				        $id_nouvel_article = spip_insert_id();
  				        echo "<a href='?exec=articles&amp;id_article=$id_nouvel_article' style='padding:5px;border:1px solid;background:#ddd;display: block;'>"._T('spiptospip:imported_view')."</a>";
                  $mailLog .= $article['titre'] ."\n"._T('spiptospip:imported_view').": ".$GLOBALS['meta']['adresse_site']."/ecrire/articles.php?id_article=$id_nouvel_article \n\n";
  				                      			        
                  // ... dans la table auteurs
  				        if ($_id_auteur) {
  				            $auteurs = explode(", ",$_id_auteur);
  				            foreach($auteurs as $auteur) {
  				                $id_auteur = get_id_auteur($auteur);
  				                if ($id_auteur) {  				                
        				            $sql="INSERT INTO ".$table_prefix."_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur, $id_nouvel_article)";
        				            spip_query($sql);                              				            
        				          }
                      }
                  }
                  
                  // ....dans la table documents_article
                  foreach($documents_current_article as $document_current_article) { 
                       $sql="INSERT INTO ".$table_prefix."_documents_articles (id_document,id_article) VALUES ('$document_current_article','$id_nouvel_article')";                     
                       spip_query($sql);                  			
                  }                       
                                       
                  // .... dans le groupe mot "licence" ?
                  if ($_licence !="" && isLicenceInstalled) {                              		
                      $id_mot = get_id_mot($_licence);
                      if ($id_mot) {
                        $sql = "INSERT INTO ".$table_prefix."_mots_articles (id_mot, id_article) VALUES ($id_mot,$id_nouvel_article)";
                        spip_query($sql);                       
                      } 
                  }               
                                               
  				        
              }
                       
              echo "</li>\n";            
              
            }           
  
          }  
           echo "</ul>\n";
          
          // alerte email ?	
          if (PREVENIR_EMAIL && $mailLog !="") {
              envoyer_mail(EMAIL_S2S,"Syndication automatique SPIP2SPIP", $mailLog);	    
          } 
          
          // debug ?
          if (DEBUG_S2S) {  
            echo $mailLog;
            foreach ($article as $k=>$detail) 
                 echo "\n<br /><strong>$k :</strong>".convert_ln($detail);
          }
          
        }
      } else {
        echo "<div style='color:red'>$articles</div>\n";;
      }
      
      // update syndic date
      $sql = "UPDATE ".$table_prefix."_spip2spip SET last_syndic = NOW() WHERE id=$current_id LIMIT 1";
      spip_query($sql);
      
  }
  
  echo "<div style='margin:20px 0'><a href='?exec=spip2spip'>"._T('spiptospip:back')."</a></div>\n";
  
  fin_cadre_relief();
  fin_page();
}
?>
