<?php
// FIXME convertir en action ?
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_partageur_import() {
  // droits
	if (!autoriser('importer', 'partageur')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// data
	$id_rubrique = (int) _request("id_rubrique");
	$id_partageur = (int)_request("id_partageur");
	$id_article = (int)_request("id_article");
	
  $callback = partageur_syndiquer($id_partageur,$id_article,$id_rubrique);
  
  if (is_int($callback)) {
        // nouvel article
        include_spip('inc/headers');
        $redirect = _DIR_RACINE._DIR_RESTREINT_ABS."?exec=articles&id_article=$callback"; 
        redirige_par_entete($redirect); 
  } else {
        // erreur: page
        $commencer_page = charger_fonction('commencer_page', 'inc');
        echo $commencer_page(_T("partageur:copie"),_T("partageur:copie"),_T("partageur:copie"));
        echo gros_titre(_T('partageur:copie'),'',false);
        echo debut_gauche('', true);
        //echo debut_boite_info(true);
        //echo fin_boite_info(true);
  
        echo debut_droite('', true);
        echo debut_cadre_relief(true);
        echo $callback;
        
        echo "<p><a href=\"?exec=partageur_add&id_rubrique=$id_rubrique\">"._T("partageur:retour_rubrique")."</a></p>";
        echo fin_cadre_relief();
        echo fin_gauche().fin_page();

  }



}

//---------------------------------------
// fonction principale: partageur_syndique
//
// effectue la syndicatioe  d'un article ($id_article) d'un site distant ($id_partageur) pour l'importer dans une rubrique ($id_rubrique) 
//---------------------------------------
function partageur_syndiquer($id_partageur,$id_article,$id_rubrique=0) {
    include_spip("inc/distant"); 
    include_spip("inc/syndic"); 
    include_spip("inc/getdocument"); 
    include_spip("inc/ajouter_documents");


    $log_html = "";
    $id_nouvel_article = false;
    
    //-------------------------------  
    // Recupere la config (A FAIRE...)
    //-------------------------------
                                
    // on charge les valeurs de CFG
    if (lire_config('partageur/import_statut')=="publie") $import_statut = "publie";  else $import_statut = "prop";    
    if (lire_config('partageur/import_mot_article')=="on")  $import_mot_article=true; else  $import_mot_article=false;
    if (lire_config('partageur/import_mot_evnt')=="on")  $import_mot_evt=true; else  $import_mot_evt=false;
    if (lire_config('partageur/import_mot_groupe_creer')=="oui")  $import_mot_groupe_creer=true; else  $import_mot_groupe_creer=false;
    if (lire_config('partageur/import_mot_groupe'))  $id_import_mot_groupe = (int) lire_config('partageur/import_mot_groupe');
                                                else $id_import_mot_groupe = -1;
    
    //-------------------------------
    // selection du site
    //-------------------------------    
    if ($row_site = sql_fetsel("*","spip_partageurs",'id_partageur='.sql_quote($id_partageur))) {    
     
      $titre_site = $row_site["titre"];            
      $url_syndic = $row_site['url_site']."/spip.php?page=backend-partageur&id_article=$id_article";
      spip_log("partageur: ".$url_syndic);
          
      // Aller chercher les donnees du flux RSS et les analyser
      $rss = recuperer_page($url_syndic, true);
      if (!$rss) {
        $log_html .= "<div style='color:red'>"._T('avis_echec_syndication_02')."</div>";
      } else {
        
        $articles = analyser_backend_partageur($rss);              
        //----*************        
        // Des articles dispo pour ce site ?
        if (is_array($articles)) { 
            foreach ($articles as $article) {
            
              if (isset($article['link'])) {    		  	    
                $documents_current_article = array();
                $version_flux = $article['version_flux'];
                
                $current_titre = $article['titre'];
                $current_link  = $article['link'];                                              
                // Est que l'article n'a pas été déjà importé ?
                $nb_reponses = sql_countsel("spip_articles","s2s_url=".sql_quote($current_link));                               
                if ($nb_reponses>0) {                                   
                   // article deja connu et present ds la base
                   $log_html .= "[<span style='color:#999'>"._T('partageur:imported_already')."</span>] <a href='$current_link'>$current_titre</a>\n";
                   spip_log("partageur: deja importe: ".$current_link);                    
                } else {
                   // nouvel article à importer
                   $log_html .= "[<span style='color:#090'>"._T('partageur:imported_new')."</span>] <a href='$current_link'>$current_titre</a>\n";
                   
                   // securite verifie sur rubrique destination existe
                   $target=$id_rubrique;
                   $is_rubrique_destination_valide = partageur_verifier_id_rubrique($id_rubrique);
                   if (!$is_rubrique_destination_valide) {  // pas de destination
                      $log_html .= _T('partageur:erreur_rubrique_invalide');                    
                   } else {   
                      // tout est bon, on insert les donnnees !
                      
                      // etape 1 -  traitement des documents
                      $_documents = $article['documents'];                    
                      $documents_current_article = array();
                      if ($_documents!="") {
                        $_documents = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $_documents ); 
                        $_documents = unserialize($_documents);                  
                        foreach($_documents as $_document) {                    
                            $id_distant = $_document['id'];
                            $source = $_document['url'];
                            $titre = stripslashes($_document['titre']);                        
                            $desc =  stripslashes($_document['desc']);
                                              
                            // inspire de @ajouter_un_document() - inc/ajout_documents.php 
                            if ($a = recuperer_infos_distantes($source)) { 
                            
                                $type_image = $a['type_image'];
                          			unset($a['type_image']);
                          			unset($a['body']);
                          			unset($a['mode']); //
                          
                          			$a['date'] = 'NOW()';
                          			$a['distant'] = 'oui';
                          			//$a['mode'] = 'document';
                          			$a['fichier'] = set_spip_doc($source);
                          			                          			
                          			$a['titre'] = $titre;     // infos partageur, recuperer via le flux
                          			$a['descriptif'] = $desc;
                          			
                          			$documents_current_article[$id_distant] = sql_insertq("spip_documents", $a);
                                 
                        		}                            
                        }                        
                      } 
                      
                      
                      // etape 2 -  traitement de l'article                             
                      $_surtitre = $article['surtitre'];
                  		$_titre = $article['titre'];
                  		$_soustitre = $article['soustitre'];
                  		$_descriptif = partageur_convert_extra($article['descriptif'],$documents_current_article,$version_flux);
                  		$_chapo = partageur_convert_extra($article['chapo'],$documents_current_article,$version_flux);
                  		$_texte = partageur_convert_extra($article['texte'],$documents_current_article,$version_flux);
                  		$_ps = partageur_convert_extra($article['ps'],$documents_current_article,$version_flux);
                  		$_date =  date('Y-m-d H:i:s',time()); // $article['date'];  // date de la syndication ou date de l'article ?
                  		$_lang = $article['lang'];
                  		$_logo = $article['logo'];
                  		$_id_rubrique = $target; 
                      $_id_secteur = partageur_get_id_secteur($target);           		          		
                  		$_statut = $import_statut;
                  		$_id_auteur = $article['auteur'];
                  		$_link = $article['link'];
                  		$_trad = $article['trad'];
                  		                                         
                                                  	            		
                  		// ....dans la table articles                                	
                  		$id_nouvel_article = sql_insertq("spip_articles",array(
                                              'lang' => $_lang,
                                              'surtitre' => $_surtitre,
                                              'titre' => $_titre,
                                              'soustitre' => $_soustitre,
                                              'id_rubrique' => $_id_rubrique,
                                              'id_secteur' => $_id_secteur,
                                              'descriptif' => $_descriptif,
                                              'chapo' => $_chapo,
                                              'texte' => $_texte,
                                              'ps' => $_ps,
                                              'statut' => $_statut,
                                              'accepter_forum' => 'non',
                                              'date' => $_date,
                                              's2s_url' => $_link,
                                              's2s_url_trad' => $_trad,
                                              ));      				        
      				        $log_html  .= "<a href='?exec=articles&amp;id_article=$id_nouvel_article' style='padding:5px;border-bottom:3px solid;background:#eee;display:block;'>"._T('spiptospip:imported_view')."</a>";
                     
                      // gestion lien traduction
                     if  ($_trad) {                     
                          if ($_trad == $_link) { // il s'agit de l'article origine de traduc 
                              sql_updateq('spip_articles', array("id_trad"=>$id_nouvel_article), "id_article=$id_nouvel_article");   // maj article orig trad                              
                              sql_updateq('spip_articles', array("id_trad"=>$id_nouvel_article), "s2s_url_trad=".sql_quote($_link)); // maj article trad (si deja importe ds une session precedente)
                          } else { // il s'agit d'un article traduit, on cherche si on a l'article origine de trad en local
                              if ($row = sql_fetsel("id_article","spip_articles","s2s_url=".sql_quote($_trad))) {
                                  $id_article_trad = (int) $row['id_article'];                                  
                                  sql_updateq('spip_articles', array("id_trad"=>$id_article_trad), "id_article=$id_nouvel_article"); // maj article trad
                                  sql_updateq('spip_articles', array("id_trad"=>$id_article_trad), "id_article=$id_article_trad");   // maj article orig trad (si deja importe ds une session precedente)
                              } 
                          }                                   
                      } 
                                    			        
                      // ... dans la table auteurs
                      if ($_id_auteur) {
      				            $auteurs = explode(", ",$_id_auteur);
      				            foreach($auteurs as $auteur) {
      				                $id_auteur = partageur_get_id_auteur($auteur);
      				                if ($id_auteur)   	
                                @sql_insertq("spip_auteurs_articles",array('id_auteur'=>$id_auteur,'id_article'=>$id_nouvel_article));
                          }
                      }
                      
                      // ....dans la table documents_article
                      foreach($documents_current_article as $document_current_article) { 
                          @sql_insertq('spip_documents_liens',array(
                                      'id_document'=>$document_current_article, 'id_objet'=>$id_nouvel_article,
                                      'objet' => 'article'));
                      }  
                      
                      // ... si logo, tente de l'importer                      
                      if ($_logo) {                                                      
                            $logo_local = copie_locale($_logo);                         
                            if ($logo_local) {                                 
                                $logo_local_dest = "IMG/arton$id_nouvel_article.".substr($logo_local,-3);                                                        
                                @rename( _DIR_RACINE.$logo_local, _DIR_RACINE.$logo_local_dest);
                            }
                      }
                      
                      // etape 3 - traitement des mots de l'article
                      $_mots = $article['mots'];                                           
                      if ($_mots!="" && $import_mot_article) {  
                        $_mots = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $_mots );                     
                        $_mots = unserialize($_mots); 
                        foreach($_mots as $_mot) {                                               
                            $groupe = stripslashes($_mot['groupe']);                            
                            $titre  = stripslashes($_mot['titre']);                                                     
                            partageur_insert_mode_article($id_nouvel_article, $titre, $groupe, $import_mot_groupe_creer, $id_import_mot_groupe,"article");                                              
                        }
                      }
                            
                      
                      // etape 4 - traitement des evenements
                      $_evenements = $article['evenements'];                      
                      if ($_evenements!="") {  
                        $_evenements = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $_evenements );                      
                        $_evenements = unserialize($_evenements);                                                          
                        foreach($_evenements as $_evenement) {                  
                            $datedeb = $_evenement['datedeb'];
                            $datefin = $_evenement['datefin'];
                            $lieu = stripslashes($_evenement['lieu']);
                            $adresse = partageur_convert_extra(stripslashes($_evenement['adresse']),$documents_current_article,$version_flux);
                            $horaire = $_evenement['horaire'];
                            $titre = stripslashes($_evenement['titre']);                        
                            $desc = partageur_convert_extra(stripslashes($_evenement['desc']),$documents_current_article,$version_flux);
                            $motevts = $_evenement['motevts'];
                                                                                     		  		                      
                            $id_nouvel_evt = sql_insertq('spip_evenements',array(
                						            'id_article'=> $id_nouvel_article,
                						            'date_debut'=> $datedeb,
                						            'date_fin'=> $datefin,
                						            'titre'=>$titre,
                						            'descriptif'=>$desc,
                						            'lieu'=>$lieu,
                						            'adresse'=>$adresse,
                						            'horaire'=>$horaire));
                            $log_html .= "<div style='padding:2px 5px;border-bottom:1px solid #5DA7C5;background:#eee;display:block;'>"._T('partageur:event_ok')." : $datedeb  $lieu</div>";
                						
                            // mot cle ?                            
                            if ($motevts!="" && $import_mot_evt) { 
                              foreach($motevts as $motevt) {                                
                                $groupe = stripslashes($motevt['groupe']);                            
                                $titre  = stripslashes($motevt['titre']);                                  
                                partageur_insert_mode_article($id_nouvel_evt, $titre, $groupe, $import_mot_groupe_creer, $id_import_mot_groupe, "evenement");                              
                              }
                            }
                            // #mot cle   
                						            			
                        } 
                      } // # etape 4: evenements
                   

                   }  // # insert article
                   
                  
                }

              }  
             
              

            }
        } else {
          $log_html .= "<div style='color:purple'>"._T('partageur:aucun_article')."</div>";
        }
        // #analyse article
        //----*************
      } 	
   
      
    } // #selection du site
    

    if ($id_nouvel_article) return $id_nouvel_article;
                    else    return $log_html;   
    
}


// -----------------------
// Fonctions Parsing
// -----------------------

//
// verifie s'il s'agit du bon format de backend
// a terme peut etre utile pour recuperer le numero de version
function is_partageur_backend($str) {  
	// Chercher un numero de version
	if (preg_match('/(spip2spip)[[:space:]](([^>]*[[:space:]])*)version[[:space:]]*=[[:space:]]*[\'"]([-_a-zA-Z0-9\.]+)[\'"]/', $str, $regs)) 
	     return true;
    	 return false;
}

// 
// parse le backend xml partageur 
// basée sur la fonction originale: ecrire/inc/syndic.php -> analyser_backend()
function analyser_backend_partageur($rss){  
  include_spip("inc_texte.php"); # pour couper()
	include_spip("inc_filtres.php"); # pour filtrer_entites()
		
	$xml_tags = array('surtitre','titre','soustitre','descriptif','chapo','texte','ps','auteur','link','trad','evenements', 'lang','logo','keyword','mots','licence','documents'); 
	
	$syndic_regexp = array(
				'item'           => ',<item[>[:space:]],i',
				'itemfin'        => '</item>',
				
				'surtitre'       => ',<surtitre[^>]*>(.*?)</surtitre[^>]*>,ims',
				'titre'          => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
				'soustitre'      => ',<soustitre[^>]*>(.*?)</soustitre[^>]*>,ims',
				'descriptif'     => ',<descriptif[^>]*>(.*?)</descriptif[^>]*>,ims',				
				'chapo'          => ',<chapo[^>]*>(.*?)</chapo[^>]*>,ims',				
				'texte'          => ',<texte[^>]*>(.*?)</texte[^>]*>,ims',				
				'ps'             => ',<ps[^>]*>(.*?)</ps[^>]*>,ims',
				'auteur'         => ',<auteur[^>]*>(.*?)</auteur[^>]*>,ims',
				'link'           => ',<link[^>]*>(.*?)</link[^>]*>,ims',
				'trad'           => ',<trad[^>]*>(.*?)</trad[^>]*>,ims',
				'evenements'     => ',<evenements[^>]*>(.*?)</evenements[^>]*>,ims',
        'lang'           => ',<lang[^>]*>(.*?)</lang[^>]*>,ims',
        'logo'           => ',<logo[^>]*>(.*?)</logo[^>]*>,ims',
        'keyword'        => ',<keyword[^>]*>(.*?)</keyword[^>]*>,ims',
        'mots'           => ',<mots[^>]*>(.*?)</mots[^>]*>,ims',
        'licence'        => ',<licence[^>]*>(.*?)</licence[^>]*>,ims',
        'documents'      => ',<documents[^>]*>(.*?)</documents[^>]*>,ims',
		
	);
	
	// documents
	$xml_doc_tags = array('id','url','titre','desc');	
	$document_regexp = array(		
  			'document'       => ',<document[>[:space:]],i',
				'documentfin'    => '</document>',
        	
				'id'             => ',<id[^>]*>(.*?)</id[^>]*>,ims',
        'url'            => ',<url[^>]*>(.*?)</url[^>]*>,ims',
				'titre'          => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
				'desc'           => ',<desc[^>]*>(.*?)</desc[^>]*>,ims',
	);
	
	// mots
	$xml_mot_tags = array('groupe','titre');	
	$mot_regexp = array(		
  			'mot'       => ',<mot[>[:space:]],i',
				'motfin'    => '</mot>',
        	
				'groupe'    => ',<groupe[^>]*>(.*?)</groupe[^>]*>,ims',
				'titre'     => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
	);
	
	// evenements
	$xml_event_tags = array('datedeb','datefin','titre','desc','lieu','adresse','horaire','motevts');
	// on ne gere pas pour l'instant idevent/idsource qui permet de conserver la liaison des repetitions
	
	$evenement_regexp = array(		
  			'evenement'        => ',<evenement[>[:space:]],i',
				'evenementfin'     => '</evenement>',				
        'datedeb'          => ',<datedeb[^>]*>(.*?)</datedeb[^>]*>,ims',
				'datefin'          => ',<datefin[^>]*>(.*?)</datefin[^>]*>,ims',
				'titre'            => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
				'desc'             => ',<desc[^>]*>(.*?)</desc[^>]*>,ims',
				'lieu'             => ',<lieu[^>]*>(.*?)</lieu[^>]*>,ims',
				'adresse'          => ',<adresse[^>]*>(.*?)</adresse[^>]*>,ims',
				'horaire'          => ',<horaire[^>]*>(.*?)</horaire[^>]*>,ims',
        'motevts'          => ',<motevts[^>]*>(.*?)</motevts[^>]*>,ims',         			
	);
	
  $xml_motevt_tags = array('groupe','titre');
	$motevt_regexp = array(		
  			'motevt'       => ',<motevt[>[:space:]],i',
				'motevtfin'    => '</motevt>',
        	
				'groupe'    => ',<groupe[^>]*>(.*?)</groupe[^>]*>,ims',
				'titre'     => ',<titre[^>]*>(.*?)</titre[^>]*>,ims',
	);
	
	// fichier backend correct ?
	if (!is_partageur_backend($rss)) return _T('spiptospip:avis_echec_syndication_01');
	
	// Echapper les CDATA
	$echappe_cdata = array();
	if (preg_match_all(',<!\[CDATA\[(.*)]]>,Uims', $rss,
	$regs, PREG_SET_ORDER)) {
		foreach ($regs as $n => $reg) {
			$echappe_cdata[$n] = $reg[1];
			$rss = str_replace($reg[0], "@@@SPIP_CDATA$n@@@", $rss);
		}
	}
	
  // supprimer les commentaires
	$rss = preg_replace(',<!--\s+.*\s-->,Ums', '', $rss);
	
	// multi (pas echappe)
	$rss = str_replace("&lt;multi&gt;",  "@@@MULTI@@@", $rss);
	$rss = str_replace("&lt;/multi&gt;",  "@@@MULTJ@@@", $rss);
	
	// version du flux
	$version_flux = 0;
	if (preg_match_all(',<spip2spip version="(.*?)">,Uims',$rss,$r, PREG_SET_ORDER)) 
	foreach ($r as $regs) {
	   $version_flux  = (float) $regs[1];
  }
  spip_log("partageur - version flux: $version_flux");
	
	// analyse de chaque item
	$items = array();
	if (preg_match_all($syndic_regexp['item'],$rss,$r, PREG_SET_ORDER))
	foreach ($r as $regs) {
		$debut_item = strpos($rss,$regs[0]);
		$fin_item = strpos($rss,
		$syndic_regexp['itemfin'])+strlen($syndic_regexp['itemfin']);
		$items[] = substr($rss,$debut_item,$fin_item-$debut_item);
		$debut_texte = substr($rss, "0", $debut_item);
		$fin_texte = substr($rss, $fin_item, strlen($rss));
		$rss = $debut_texte.$fin_texte;
	}

	// Analyser chaque <item>...</item> du backend et le transformer en tableau
	if (!count($items)) return _T('spiptospip:avis_echec_syndication_01');
	
	foreach ($items as $item) {
	 
		$data = array();
    
		// Date
		$la_date = "";
		if (preg_match(",<date>([^<]*)</date>,Uims",$item,$match))		$la_date = $match[1];
		if ($la_date)  		$la_date = my_strtotime($la_date);
		if ($la_date < time() - 365 * 24 * 3600	OR $la_date > time() + 48 * 3600)		$la_date = time();
		$data['date'] = $la_date;
		
		// version du flux
		$data['version_flux'] = $version_flux;
			
		// Recuperer les autres tags du xml
		foreach ($xml_tags as $xml_tag) {		  
		  if (preg_match($syndic_regexp[$xml_tag],$item,$match)) $data[$xml_tag] = $match[1];
  				                                              else $data[$xml_tag] = "";
    }	
    
    // On parse le noeud documents
    if ($data['documents'] != "") {        
          $documents = array();
          if (preg_match_all($document_regexp['document'],$data['documents'],$r2, PREG_SET_ORDER))
          	foreach ($r2 as $regs) {
          		$debut_item = strpos($data['documents'],$regs[0]);
          		$fin_item = strpos($data['documents'],
          		$document_regexp['documentfin'])+strlen($document_regexp['documentfin']);
          		$documents[] = substr($data['documents'],$debut_item,$fin_item-$debut_item);
          		$debut_texte = substr($data['documents'], "0", $debut_item);
          		$fin_texte = substr($data['documents'], $fin_item, strlen($data['documents']));
          		$data['documents'] = $debut_texte.$fin_texte;
          }
          
          $portfolio = array();
          if (count($documents)) {          
              foreach ($documents as $document) {                 
                 $data_node = array();
                 foreach ($xml_doc_tags as $xml_doc_tag) {
                    if (preg_match($document_regexp[$xml_doc_tag],$document,$match)) $data_node[$xml_doc_tag] = $match[1]; 
  				                                                                      else $data_node[$xml_doc_tag] = "";
  				       } 
                $portfolio[] = $data_node;                                                     
              }             
              $data['documents'] =  serialize($portfolio);
          }       
    } # noeud documents
	

	  // On parse le noeud evenement
    if ($data['evenements'] != "") {
          $evenements = array();
          if (preg_match_all($evenement_regexp['evenement'],$data['evenements'],$r3, PREG_SET_ORDER))
          	foreach ($r3 as $regs) {
          		$debut_item = strpos($data['evenements'],$regs[0]);
          		$fin_item = strpos($data['evenements'],$evenement_regexp['evenementfin'])+strlen($evenement_regexp['evenementfin']);
          		$evenements[] = substr($data['evenements'],$debut_item,$fin_item-$debut_item);
          		$debut_texte = substr($data['evenements'], "0", $debut_item);
          		$fin_texte = substr($data['evenements'], $fin_item, strlen($data['evenements']));
          		$data['evenements'] = $debut_texte.$fin_texte;				
          }
          
          $agenda = array();
          if (count($evenements)) {          
              foreach ($evenements as $evenement) {                 
                 $data_node = array();
                 foreach ($xml_event_tags as $xml_event_tag) {
                    if (preg_match($evenement_regexp[$xml_event_tag],$evenement,$match)) $data_node[$xml_event_tag] = $match[1]; 
  				                                                                      else $data_node[$xml_event_tag] = "";
  				       } 
  				       
  				       // On parse le noeud motevt (mot evenement) ?
  				       if ($data_node['motevts'] != "") {                              
                        $motevts = array(); 
                        if (preg_match_all($motevt_regexp['motevt'],$data_node['motevts'],$r2, PREG_SET_ORDER))
                        foreach ($r2 as $regs) {
                        		$debut_item = strpos($data_node['motevts'],$regs[0]);
                        		$fin_item = strpos($data_node['motevts'],
                        		$motevt_regexp['motevtfin'])+strlen($motevt_regexp['motevtfin']);
                        		$motevts[] = substr($data_node['motevts'],$debut_item,$fin_item-$debut_item);                        		
                        		$debut_texte = substr($data_node['motevts'], "0", $debut_item);
                        		$fin_texte = substr($data_node['motevts'], $fin_item, strlen($data_node['motevts']));
                        		$data_node['motevts'] = $debut_texte.$fin_texte;
                        }
                        
                        $motcleevt = array();                         
                        if (count($motevts)) {          
                            foreach ($motevts as $motevt) {                 
                               $data_node_evt = array();
                               foreach ($xml_motevt_tags as $xml_motevt_tag) {
                                  if (preg_match($motevt_regexp[$xml_motevt_tag],$motevt,$match)) $data_node_evt[$xml_motevt_tag] = $match[1]; 
                				                                                              else $data_node_evt[$xml_motevt_tag] = "";
                				       } 
                              $motcleevt[] = $data_node_evt;                  
                            }
                            $data_node['motevts'] = $motcleevt; ;                            
                        }       
                 }  				       
  				       // #noeud motevt
  				       
                $agenda[] = $data_node;                                                     
              }  
     
              $data['evenements'] =  serialize($agenda); 
          }       
    } #noeud evenements
    
    // On parse le noeud mots    
    if ($data['mots'] != "") {        
          $mots = array();          
          if (preg_match_all($mot_regexp['mot'],$data['mots'],$r2, PREG_SET_ORDER))
          	foreach ($r2 as $regs) {
          		$debut_item = strpos($data['mots'],$regs[0]);
          		$fin_item = strpos($data['mots'],
          		$mot_regexp['motfin'])+strlen($mot_regexp['motfin']);
          		$mots[] = substr($data['mots'],$debut_item,$fin_item-$debut_item);          		
          		$debut_texte = substr($data['mots'], "0", $debut_item);
          		$fin_texte = substr($data['mots'], $fin_item, strlen($data['mots']));
          		$data['mots'] = $debut_texte.$fin_texte;          		
          }
          
          $motcle = array();
          if (count($mots)) {          
              foreach ($mots as $mot) {                 
                 $data_node = array();
                 foreach ($xml_mot_tags as $xml_mot_tag) {
                    if (preg_match($mot_regexp[$xml_mot_tag],$mot,$match)) $data_node[$xml_mot_tag] = $match[1]; 
  				                                                            else $data_node[$xml_mot_tag] = "";
  				       } 
                $motcle[] = $data_node;                          
              }             
              $data['mots'] =  serialize($motcle);              
          }       
    }	#noeud mots
	  
	  
		// Nettoyer les donnees et remettre les CDATA et multi en place
		foreach ($data as $var => $val) {
			$data[$var] = filtrer_entites($data[$var]);
			foreach ($echappe_cdata as $n => $e)
				$data[$var] = str_replace("@@@SPIP_CDATA$n@@@",$e, $data[$var]);
      if (!defined("_SPIP2SPIP_IMPORT_HTML")) $data[$var] = trim(textebrut($data[$var]));  // protection import HTML 
			$data[$var] = str_replace("@@@MULTI@@@", "<multi>", $data[$var]);
	    $data[$var] = str_replace("@@@MULTJ@@@", "</multi>", $data[$var]);	
		}

		//$data['item'] = $item;  //utile pour partageur ?		
		$articles[] = $data;    
	}
 
  return $articles;
}

// -----------------------
// Fonctions SQL
// -----------------------

//
// verifier si une rubrique existe
function partageur_verifier_id_rubrique($id_rubrique) {   
   if ($row = sql_fetsel("id_rubrique","spip_rubriques","id_rubrique=".sql_quote($id_rubrique))) 
               return true;
   return false;  

}


//
// recupère id du secteur
function partageur_get_id_secteur($id_rubrique) { 
   if ($row = sql_fetsel("id_parent","spip_rubriques","id_rubrique=$id_rubrique")) 
               return $row['id_parent']; 
   return 0;  
}

//
// recupere id d'un auteur selon son nom sinon le creer
function partageur_get_id_auteur($name) { 
   if (trim($name)=="") 
              return false;
   if ($row = sql_fetsel("id_auteur","spip_auteurs","nom=".sql_quote($name))) 
              return $row['id_auteur']; 

    // auteur inconnu, on le cree ... 
    return sql_insertq('spip_auteurs',array('nom'=>$name,'statut'=>'1comite'));
}

//
// insert un mot-cle a un objet (article / evenement)
function partageur_insert_mode_article($id_objet, $mot_titre, $groupe_titre, $mode_creer_groupe, $id_groupe=-1,$objet_lie="article") {

   if ($mode_creer_groupe) {
        // groupe existe ?
        if ($row = sql_fetsel("id_groupe","spip_groupes_mots","titre=".sql_quote($groupe_titre))) { 
              $id_groupe = $row['id_groupe'];
        } else {
            $id_groupe = sql_insertq('spip_groupes_mots',array(
                                      'titre'=> $groupe_titre, 
                                      'tables_liees'=> $objet_lie."s", 
                                      'minirezo' => 'oui',
                                      'comite' => 'oui',
                                      'forum' => 'non'));
        }
        
   }     	
     
   if ($id_groupe>0){                   
        // mot existe ?
        if ($row = sql_fetsel("id_mot","spip_mots","titre=".sql_quote($mot_titre)." AND id_groupe=".intval($id_groupe))) { 
              $id_mot = $row['id_mot'];
        } else {
              if ($row = sql_fetsel("titre","spip_groupes_mots","id_groupe=".intval($id_groupe))) 
                          $type = $row['titre'];
              $id_mot = sql_insertq('spip_mots',array(
                                      'titre'=> $mot_titre, 
                                      'id_groupe'=> intval($id_groupe), 
                                      'type' => $type));                                      
        }
            
        sql_insertq("spip_mots_".$objet_lie."s", array(
                                      'id_mot' => intval($id_mot), 
                                      "id_".$objet_lie => intval($id_objet)));
                   
   } else {
      spip_log("partageur pas de groupe-clé import specifie");
   }

}


// -----------------------
// Fonctions de formatage 
// -----------------------


//
// restaure le formatage des extra
function partageur_convert_extra($texte,$documents,$version_flux=1.6) {
	$texte = partageur_convert_ln($texte,$version_flux); 
	$texte = partageur_convert_img($texte,$documents);
	return $texte;
}

//
// restaure le formatage des img et doc avec le tableau fourni
function partageur_convert_img($texte,$documents) {
  $texte_avt_regex = $texte;
  krsort($documents);
	foreach($documents as $k=>$val) {	
	   $texte = preg_replace("/__IMG$k(.*?)__/i", "<img$val$1>",$texte);
	   // si le doc est employe en tant image, changer son mode pour qu'il sorte du portfolio (mode=document) et passe en image (mode=image)
	   if ($texte_avt_regex != $texte) partageur_update_mode_document($val,'image'); 
	   
	   // autre mise a jour non image
	   $texte = preg_replace("/__DOC$k(.*?)__/i", "<doc$val$1>",$texte);
	   
     $texte_avt_regex = $texte;	   
  }	
  
  //$texte = preg_replace("/__(IMG|DOC)(.*?)__/i", "",$texte); // nettoyage des codes qui resteraient eventuellement
  $texte = preg_replace("/__(.*?)__/i", "",$texte); // expression plus large (pour prevoir la compatabilite future si ajout d'autres extras)
	return $texte;
}

//
// restaure le formatage des ln
function partageur_convert_ln($texte,$version_flux=1.6) {
  if ($version_flux<1.7)	$texte = str_replace("__LN__","\n\n",$texte);
                  else    $texte = str_replace("__LN__","\n",$texte); 
	return $texte;
}

//
// change le mode (vignette/document/) du document
function partageur_update_mode_document($id_document,$mode="vignette") {  
   sql_updateq('spip_documents', array("mode"=>$mode), "id_document=$id_document");
}




?>