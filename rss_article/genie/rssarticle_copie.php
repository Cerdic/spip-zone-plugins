<?php
/**
 * Plugin RSS article pour Spip 2.0
 * Licence GPL
 * 
 *
 */

// TODO
// - gerer les mots-clés hors enclosure ?
// - gerer le nombre d'import (15 ou +) via cfg ?
include_spip("inc/mail");
include_spip('inc/filtres'); 
include_spip('inc/distant');


function genie_rssarticle_copie_dist($t){  
  
  // si cfg dispo, on charge les valeurs
  if (function_exists(lire_config))  {
        $import_statut = lire_config('rssarticle/import_statut');      
        if (lire_config('rssarticle/citer_source')=="on") $citer_source=true; else  $citer_source=false;
        if (lire_config('rssarticle/email_alerte')=="on") $email_alerte=true; else  $email_alerte=false;
        $email_suivi = lire_config('rssarticle/email_suivi'); 
  } else { // sinon valeur par defaut
        $import_statut = "prop";         // statut des articles importés: prop(proposé),publie(publié)      
        $citer_source = true;            // citer source ?
        $email_alerte = false;           // envoi email  ?
        $email_suivi = $GLOBALS['meta']['adresse_suivi']; // adresse de suivi editorial
  }
  
  // principe de pile:
  // on boucle sur les derniers articles syndiques pour les retirer ensuite
  // bourrin voir les requetes avec jointure du Miroir ou du site Rezo 
  $log = "";
  $log_c = 0;
  
  $s = sql_select("*", "spip_syndic_articles", "statut='publie'","","maj DESC","15");  
  while ($a = sql_fetch($s)) {
		$titre =  $a['titre'];
    $id_syndic_article = $a['id_syndic_article']; 	
				
	  // article avec mm titre existe ?
	  if (!$row = sql_fetsel("id_article","spip_articles","titre=".sql_quote($titre))) {        
        $id_syndic = $a['id_syndic'];
        
        // uniquement sur les sites publies
        if ($row2 = sql_fetsel("id_rubrique,id_secteur","spip_syndic","id_syndic=$id_syndic AND statut='publie'")) {  
            $id_rubrique = $row2['id_rubrique'];
            $id_secteur = $row2['id_secteur'];
                        
            $texte = $a['descriptif'];
            $lang  = $a['lang'];
            $url   = $a['url'];
            $tags =  $a['tags'];
            
            if ($citer_source) 
                 $texte .= "\n\n\nURL: [->$url]";  
            if ($lang=="") 	
                $lang = $GLOBALS['spip_lang'];    
        
            $lsDate = date('Y-m-d H:i:s');
            // creation de l'article
            $id_article = sql_insertq( 'spip_articles', array(
                                'titre'=>$titre, 'id_rubrique'=>$id_rubrique,
                                'texte'=>$texte, 'statut'=>$import_statut, 'id_secteur'=>$id_secteur,
                                'date'=> $lsDate, 'accepter_forum'=>'non', 'lang'=>$lang));
                                
            // gestion auteur            
            $auteurs= explode(", ",$a['lesauteurs']);            
            foreach ($auteurs as $k => $auteur) {                       
                 if ($current_id_auteur = rssarticle_get_id_auteur($auteur))
                      sql_insertq( 'spip_auteurs_articles', array('id_auteur'=>$current_id_auteur, 'id_article'=>$id_article));                
            }
            
            // tags a convertir en documents distants 
            $doc_distants = extraire_enclosures($tags);
		        foreach ($doc_distants as $k=>$doc_distant) {
                $infos = recuperer_infos_distantes($doc_distant);
                if ($infos['extension']) {
                    $ext    = $infos['extension'];
                    $taille = $infos['tailles']; 
                    $row = sql_fetsel("inclus", "spip_types_documents", "extension=" . sql_quote($ext) . " AND upload='oui'");  // extension autorisee ?
                    if ($row) {
                          $id_document = sql_insertq( 'spip_documents', array(
                                'extension'=>$ext, 
                                'date'=> $lsDate,
                                'fichier'=> $doc_distant,
                                'taille'=> $taille,
                                'mode' => 'document',
                                'distant' => 'oui'));
                          
                         sql_insertq( 'spip_documents_liens', array(
                                'id_document' =>$id_document, 
                                'id_objet'=> $id_article,
                                'objet'=> 'article',
                                'vu'=> 'non'));                      			
                    }
                }
                
            }
            
                    		
        		$log_c++;
        		$log .= "\n - $titre";             
        } 
    }
    
    // on depublie les articles syndiques qui ont scannes  (mm pour les sites non publies)
    sql_update("spip_syndic_articles", array('statut' => '"refuse"'), "id_syndic_article=$id_syndic_article");
  

	} // FIN PILE
	
	// log et alerte email
  $log .= "\nPlugin Copie RSS en Articles: $log_c articles copies";
  spip_log($log);     
  if ($email_alerte && $email_suivi !="" && $log_c > 0)                 
                  envoyer_mail($email_suivi,"Copie RSS en Articles", $log);
              
	// maintenance generale
  // on efface les syndic_articles de plus de 2 mois pour soulager le systeme (cf genie/syndic) 
  // attention: ici pour effacer sur l'ensemble des sites syndiques		 
	sql_delete('spip_syndic_articles', "maj < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND date < DATE_SUB(NOW(), INTERVAL 2 MONTH)");
 
	return 1;
}


//
// recupere id d'un auteur selon son nom sinon le creer
function rssarticle_get_id_auteur($nom) {  
   if (trim($nom)=="") 
        return false;
   
   if ($row = sql_fetsel(array("id_auteur"),"spip_auteurs","nom=".sql_quote($nom)))  
        return $row['id_auteur']; 

    // auteur inconnu, on le cree ... 
    return sql_insertq('spip_auteurs',array('nom'=>$nom,'statut'=>'1comite'));
}

//
// extraire les documents taggues enclosure 
// voir http://doc.spip.org/@afficher_enclosures
function extraire_enclosures($tags) {
	$s = array();
	foreach (extraire_balises($tags, 'a') as $tag) {
		if (extraire_attribut($tag, 'rel') == 'enclosure'
		AND $t = extraire_attribut($tag, 'href')) {
			$s[] = $t;
		}
	}
	return $s;
}

/*
UPDATE `spip_syndic_articles` SET statut="publie";
TRUNCATE TABLE `spip_articles`;
TRUNCATE TABLE `spip_auteurs_articles`;
DELETE FROM `spip_auteurs` WHERE id_auteur > 1; 
*/

?>