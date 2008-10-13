<?php
// version spip2spip pour le cron
// (il est aussi possible de lancer manuelement via le backoffice exec/spip2spip_syndic)
//
// attention script muet (appel par cron), ne rien afficher ! (utiliser le mailLog et spip_log)

if (!defined("_ECRIRE_INC_VERSION")) return;
include(dirname(__FILE__).'/spiptospip_fonctions.php');

function spip2spip_ajouter_cron($taches) {
	$taches['spip2spip'] = 30;
	return $taches;
}

function cron_spip2spip($t) {
  //
  // equivalent de exec/spip2spip_syndic.php sans sortie (pour cron)
  
  include_spip("inc/distant"); 
  include_spip("inc/syndic"); 
  include_spip("inc/mail"); 
  include_spip("inc/getdocument"); 
  
  // Recupere la config
  //-------------------------------
  global $table_prefix;
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
      $import_mode = "import_once";    // import une fois (import_once) ou synchro (import_synchro)
      $citer_source = true; 
      $email_alerte = true;
      $email_suivi = $GLOBALS['meta']['adresse_suivi']; // adresse de suivi editorial
  }
  
  
  //------------------------------- 
  // Main
  //-------------------------------
  // quel est le site a syndiquer (le plus vieux et ancien > 1heure)
  spip_log("spip2spip: something new ?");
  //orignal $sql = "SELECT * FROM ".$table_prefix."_spip2spip WHERE last_syndic < DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY last_syndic LIMIT 1";
  $sql = "SELECT * FROM ".$table_prefix."_spip2spip ORDER BY last_syndic LIMIT 1";  
  $sites = spip_query($sql);
  $mailLog = "";   
  while($row_site  = spip_fetch_array($sites)) {         
      $current_id   = $row_site["id"];
      $current_site = $row_site["site_titre"];
      $url_syndic   = $row_site["site_rss"];
      $date_syndic  = $row_site["last_syndic"];
      
      $mailLog = "";
              
      // Aller chercher les donnees du flux RSS et les analyser
      $rss = recuperer_page($url_syndic, true);
      if (!$rss)  $articles = _T('avis_echec_syndication_02');
          else		$articles = analyser_backend_spip2spip($rss);

      // Des articles dispo pour ce site ?
      if (is_array($articles)) {
        foreach ($articles as $article) {
                 
          // Est que l'article n'a pas été déjà importée ?
          if (isset($article['titre'])) {
		  	$documents_current_article = array();
            $current_titre = $article['titre'];
            $sql2 = "SELECT COUNT(titre) as c FROM ".$table_prefix."_articles WHERE titre='".addslashes($current_titre)."'";
            $nb_article = spip_fetch_array(spip_query($sql2));
            if ($nb_article['c']!=0) { 
                       // article deja connu et present ds la base                        
                       if ($import_mode != "import_synchro") {
                              // mode import_once: on ne fait rien
                              
                       } else {                       
                              // mode import_synchro: maj de l'article (code de plantinet)            
            
                              $sql3 = "SELECT * FROM ".$table_prefix."_articles WHERE titre='".addslashes($current_titre)."'";
                              $modif = spip_fetch_array(spip_query($sql3));
                      				
                      				$_amodif=false; $_elemmodif="";
                                      $_surtitre = $article['surtitre'];
                      				if($modif['surtitre']!=$_surtitre){$_amodif=true; $_elemmodif .="surtitre, ";}
                      				
                      				$_titre = $article['titre'];
                      				if($modif['titre']!=$_titre){$_amodif=true;$_elemmodif .="titre, ";}
                      				
                      				$_soustitre = $article['soustitre'];
                      				if($modif['soustitre']!=$_soustitre){$_amodif=true;$_elemmodif .="soustitre, ";}
                      				
                      				$_descriptif = spip2spip_convert_extra($article['descriptif'],$documents_current_article);
                      				if($modif['descriptif']!=$_descriptif){$_amodif=true;$_elemmodif .="descriptif, ";}
                      				
                      				$_chapo = spip2spip_convert_extra($article['chapo'],$documents_current_article);
                      				if($modif['chapo']!=$_chapo){$_amodif=true;$_elemmodif .="chapo, ";}
                      				
                      				$_texte = spip2spip_convert_extra($article['texte'],$documents_current_article);
                      				if($modif['texte']!=$_texte){$_amodif=true;$_elemmodif .="texte, ";}
                      				
                      				$_ps = spip2spip_convert_extra($article['ps'],$documents_current_article);
                      				if($modif['ps']!=$_ps){$_amodif=true;$_elemmodif .="ps, ";}
                      				
                      				$_date =  date('Y-m-d H:i:s',time()); // $article['date'];  // date de la syndication ou date de l'article ?
                      				$_lang =  $article['lang'];
                      				if($modif['lang']!=$_lang){$_amodif=true;$_elemmodif .="lang, ";}
                      				
                      				$_id_auteur = $article['auteur'];
                      				if($modif['auteur']!=$_auteur){$_amodif=true;$_elemmodif .="auteur, ";}
                      				
                      				$_link = $article['link'];
                      				
                      				$_licence = $article['licence']; 
                      				if($modif['licence']!=$_licence){$_amodif=true;$_elemmodif .="licence, ";}		 
                      			 
                      			 	if($_amodif){		//(lang,surtitre,titre,soustitre,id_rubrique,descriptif,chapo,texte,ps,statut,accepter_forum,date) `texte`='".addslashes($_texte)."',
                      				$sql4 = "UPDATE ".$table_prefix."_articles ";
                        				        $sql4.="SET  `lang`='".addslashes($_lang)."',`surtitre`='".addslashes($_surtitre)."',`titre`='".addslashes($_titre)."',`soustitre`='".addslashes($_soustitre)."',`descriptif`='".addslashes($_descriptif)."',`chapo`='".addslashes($_chapo)."',`texte`='".addslashes($_texte)."',`ps`='".addslashes($_ps)."',`statut`='".$_statut."',`date`='".$_date."' ";
                      						$sql4.="WHERE id_article='".$modif['id_article']."';";
                        				        spip_query($sql4);
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
                      							
                      							if($_titre==$titre){	  
                      								$sqle = "SELECT * FROM ".$table_prefix."_evenements WHERE id_article='".$modif['id_article']."'";
                      								$rese=spip_query($sqle);
                      								$dedans=false;
                      								$rechevent=array($id_distant,$datedeb,$datefin,$lieu,$horaire,$titre,$desc,$idsource);
                      								while($modifevent = spip_fetch_array($rese)){
                      										if($id_distant==$modifevent['id_evenement']){ $dedans=true; }
                      								}	
                      								if(!$dedans){
                      									 $sql="INSERT INTO `".$table_prefix."_evenements` (`id_evenement` ,	`id_article` ,`date_debut` ,`date_fin` ,`titre` ,`descriptif` ,	`lieu` ,`horaire` ,`id_evenement_source` ,`idx` ,`maj`)
                      										VALUES ('".$id_distant."' , '".$modif['id_article']."', '".$datedeb."', '".$datefin."', '".$titre."', '".$desc."', '".$lieu."', '".$horaire."', '".$idsource."', 'oui', NOW( ))";
                      						  
                      										  spip_query($sql);  
                      								}
                      							}
                             			
                                          	} 
                                        }	
					                    // maj de l'article fin(code de plantinet) 
                       }
            } else {  
              // nouvel article à importer
              
              // on cherche la rubrique destination
              $target = spip2spip_get_id_rubrique($article['keyword']);
              if (!$target) {
                  // pas de destination                                    
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
						      
  
						if($_titre==$titre){		                      
                          $sql="INSERT INTO `".$table_prefix."_evenements` (`id_evenement` ,	`id_article` ,`date_debut` ,`date_fin` ,`titre` ,`descriptif` ,	`lieu` ,`horaire` ,`id_evenement_source` ,`idx` ,`maj`)
								VALUES ('".$id_distant."' , '".$id_nouvel_article."', '".$datedeb."', '".$datefin."', '".$titre."', '".$desc."', '".$lieu."', '".$horaire."', '".$idsource."', 'oui', NOW( ))";
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
                  
                  // log pour suivi du cron                             
  				        spip_log("spip2spip: nouvel article $id_nouvel_article from $_link"); 
              }
             
            }           
  
          }  

          
          // alerte email ?	
          if ($email_alerte && $mailLog !="") {
              envoyer_mail($email_suivi,"Syndication automatique SPIP2SPIP", $mailLog);	    
          } 
          
        }
      } 
      
      // update syndic date
      $sql = "UPDATE ".$table_prefix."_spip2spip SET last_syndic = NOW() WHERE id=$current_id LIMIT 1";
      spip_query($sql);
  }
  spip_log("spip2spip (end)");


}

?>
