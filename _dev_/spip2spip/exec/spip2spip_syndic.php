<?php
// version spip2spip manuelle
// (la version automatique spip2spip/spip2.php est appelle via le cron)
// TODO upgrade SPIP

if (!defined("_ECRIRE_INC_VERSION")) return;

include(dirname(__FILE__).'/../spiptospip_fonctions.php');
include_spip('inc/presentation');

//------------------------------- 
// Main
//-------------------------------
function exec_spip2spip_syndic(){
  include_spip("inc/distant"); 
  include_spip("inc/syndic"); 
  include_spip("inc/mail"); 
  include_spip("inc/getdocument"); 
  include_spip("inc/ajouter_documents");
  
  global $table_prefix;
  global $connect_statut;
  global $connect_toutes_rubriques;  
  //------------------------------- 
  // droits  - FIXME ? en SPIP 2 utiliser autoriser ?????
  //-------------------------------
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {   
          $commencer_page = charger_fonction('commencer_page', 'inc');
          echo $commencer_page(_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"));
	        echo _T('avis_non_acces_page');
          echo fin_gauche().fin_page();
	        exit;
  }
  
  //-------------------------------  
  // Recupere la config
  //-------------------------------
  // groupe mot cle "licence" installe ? (contrib: http://www.spip-contrib.net/Filtre-Licence )
  if (spip2spip_get_id_groupemot("licence"))  $isLicenceInstalled = true; 
                              else  $isLicenceInstalled = false;
                              
  // si cfg dispo, on charge les valeurs
  if (function_exists(lire_config))  {
      $import_statut = lire_config('spip2spip/import_statut');
      $import_mode = lire_config('spip2spip/import_mode');
      if (lire_config('spip2spip/citer_source')=="on") $citer_source=true; else  $citer_source=false;
      if (lire_config('spip2spip/email_alerte')=="on") $email_alerte=true; else  $email_alerte=false;
      $email_suivi = lire_config('spip2spip/email_suivi'); 
  } else { // sinon valeur par defaut
      $import_statut = "prop";         // statut des articles importés: prop(proposé),publie(publié)      
      $citer_source = true; 
      $email_alerte = true;
      $email_suivi = $GLOBALS['meta']['adresse_suivi']; // adresse de suivi editorial
  }
                             
  //------------------------------- 
  // Main
  //-------------------------------
  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"));
  echo gros_titre(_T('spiptospip:copy_spip2spip'),'',false);
  echo debut_gauche('', true);
  echo debut_boite_info(true)._T('spiptospip:intro_spip2spip');
  echo fin_boite_info(true);
  
  echo debut_droite('', true);
  echo debut_cadre_relief(true);
  
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
		  	    $documents_current_article = array();
            $current_titre = $article['titre'];
            $sql2 = "SELECT COUNT(titre) as c FROM ".$table_prefix."_articles WHERE titre='".addslashes($current_titre)."'";
            $nb_article = spip_fetch_array(spip_query($sql2));
            if ($nb_article['c']!=0) { // article deja connu et present ds la base                        
                  echo "<li>[<span style='color:#999'>"._T('spiptospip:imported_already')."</span>] $current_titre</li>\n"; 
            } else {  
              // nouvel article à importer
              echo "<li>[<span style='color:#090'>"._T('spiptospip:imported_new')."</span>] $current_titre<br />\n";
              
              // on cherche la rubrique destination
              $target = spip2spip_get_id_rubrique($article['keyword']);
              if (!$target) {
                  // pas de destination
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
                        // inspire de ajouter_un_document () de inc/getdocument.php 
                        if ($a = recuperer_infos_distantes($source)) {  
                          $fichier = $a['fichier'];                       
                    			$id_type = $a['id_type'];
                    			$taille = $a['taille'];                  			
                    			$largeur = $a['largeur'];
                    			$hauteur = $a['hauteur'];
                    			$ext = $a['extension'];
                    			$type_image = $a['type_image'];
                    
                    			$distant = 'oui';
                    			$mode = 'document';
                    			
                          $date =  date('Y-m-d H:i:s',time()); // date de la syndication ou date du doc original (a ajouter car non parse) ?
                    			// FIXME verif secu (par rapport ext) 
                    			
                    			// extension
                    			ereg("\.([^.]+)$", $nom_envoye, $match);
		                      $ext = (corriger_extension(strtolower($match[1])));
                           
                          // Prevoir traitement specifique pour videos                      		
                      		if ($ext != "mov" && $ext != "svg") {                      		 
                      		  // Si c'est une image, recuperer sa taille et son type (detecte aussi swf)
                      			if (!$size_image = @getimagesize($fichier)) 
                      			   $size_image = @getimagesize($source); // si on arrive pas en local, on teste en distant                                                 			
                      			$largeur = intval($size_image[0]);                      			
                      			$hauteur = intval($size_image[1]);
                      			$type_image = decoder_type_image($size_image[2]);
                      		}  		                      
                          
                          $sql="INSERT INTO ".$table_prefix."_documents(id_type,titre,date,descriptif,fichier,taille,largeur,hauteur,mode,distant,idx) 
                                                                VALUES ('$id_type',
                                                                        '".addslashes($titre)."',
                                                                        '$date',
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
              		$_descriptif = addslashes(spip2spip_convert_extra($article['descriptif'],$documents_current_article));
              		$_chapo = addslashes(spip2spip_convert_extra($article['chapo'],$documents_current_article));
              		$_texte = addslashes(spip2spip_convert_extra($article['texte'],$documents_current_article));
              		$_ps = addslashes(spip2spip_convert_extra($article['ps'],$documents_current_article));
              		$_date =  date('Y-m-d H:i:s',time()); // $article['date'];  // date de la syndication ou date de l'article ?
              		$_lang =  addslashes($article['lang']);
              		$_id_rubrique = $target;            		          		
              		$_statut = $import_statut;
              		$_id_auteur = $article['auteur'];
              		$_link = $article['link'];
              		$_licence = $article['licence'];                           		
              		
              		// on cite la source originale ds le champs ps et la licence
              		if ($citer_source)
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
                  $mailLog .= $article['titre'] ."\n"._T('spiptospip:imported_view').": ".$GLOBALS['meta']['adresse_site']."/ecrire/?exec=articles&id_article=$id_nouvel_article \n\n";
  				                      			        
                  // ... dans la table auteurs
  				        if ($_id_auteur) {
  				            $auteurs = explode(", ",$_id_auteur);
  				            foreach($auteurs as $auteur) {
  				                $id_auteur = spip2spip_get_id_auteur($auteur);
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
 
                   // traitement des evenements
                  $_evenements = $article['evenements'];
				          $_evenements = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $_evenements );
                  if ($_evenements!="") {
                   $_evenements=unserialize($_evenements);           
                    foreach($_evenements as $_evenement) {                      
                        $id_distant = $_evenement['idevent'];
                        $datedeb = $_evenement['datedeb'];
                        $datefin = $_evenement['datefin'];
                        $lieu = addslashes($_evenement['lieu']);
                        $horaire = $_evenement['horaire'];
                        $titre = addslashes($_evenement['titre']);                        
                        $desc = addslashes($_evenement['desc']);                         
                        $idsource = $_evenement['idsource'];      
						      
						//echo $titreart."==".$titre."<br />";		  
						if($_titre==$titre){		                      
                          $sql="INSERT INTO `".$table_prefix."_evenements` (`id_evenement` ,	`id_article` ,`date_debut` ,`date_fin` ,`titre` ,`descriptif` ,	`lieu` ,`horaire` ,`id_evenement_source` ,`idx` ,`maj`)
								VALUES ('".$id_distant."' , '".$id_nouvel_article."', '".$datedeb."', '".$datefin."', '".$titre."', '".$desc."', '".$lieu."', '".$horaire."', '".$idsource."', 'oui', NOW( ))";
						  echo "<div style='padding:5px;border:1px solid #5DA7C5;background:#ddd;display: block;'>"._T('spiptospip:event_ok').$datedeb." &agrave; ".$lieu."</div>";
						  /*echo $sql;
						  echo "<hr />";*/
        				          spip_query($sql);      
						}            			
                    	} 
                  }
                                       
                  // .... dans le groupe mot "licence" ?
                  if ($_licence !="" && isLicenceInstalled) {                              		
                      $id_mot = spip2spip_get_id_mot($_licence);
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
          if ($email_alerte && $mailLog !="") {
              envoyer_mail($email_suivi,"Syndication automatique SPIP2SPIP", $mailLog);	    
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
  echo fin_cadre_relief();
  
  // pied
  echo fin_gauche().fin_page();
}
?>