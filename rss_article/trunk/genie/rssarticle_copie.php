<?php
/**
 * Plugin RSS article pour Spip 3.0
 * Licence GPL
 * 
 *
 */

// TODO
// - gerer les mots-clés hors enclosure ?
include_spip("inc/mail");
include_spip('inc/filtres'); 
include_spip('inc/distant');
include_spip('inc/chercher_logo');
include_spip('inc/rubriques');
include_spip('inc/config');

function genie_rssarticle_copie_dist($t){  

  // configuration (ou valeurs par defaut)    
  if (lire_config('rssarticle/import_statut')=="publie")       $import_statut="publie"; else  $import_statut="prop";     
  if (lire_config('rssarticle/mode')=="auto")       $mode_auto=true; else  $mode_auto=false;  
  if (lire_config('rssarticle/email_alerte')=="on") $email_alerte=true; else  $email_alerte=false;
  if (lire_config('rssarticle/copie_logo')=="on")   $copie_logo=true; else  $copie_logo=false; 
  if (lire_config('rssarticle/html2spip')=="on")   $html2spip=true; else  $html2spip=false; 
  $email_suivi = lire_config('rssarticle/email_suivi'); 
  
  // autres valeurs
  $accepter_forum =	substr($GLOBALS['meta']['forums_publics'],0,3);
  
  // principe de pile:
  // on boucle sur les derniers articles syndiques pour les retirer ensuite
  // bourrin voir les requetes avec jointure du Miroir ou du site Rezo 
  $log = "";
  $log_c = 0;
  
  // boucle sur les sites publies 
  if ($mode_auto) $u = sql_select("id_syndic,id_rubrique,id_secteur","spip_syndic","statut='publie'");   // tous 
          else    $u = sql_select("id_syndic,id_rubrique,id_secteur","spip_syndic","statut='publie' AND rssarticle='oui'");
  
  while ($b = sql_fetch($u)) {
       $id_syndic = (int) $b['id_syndic'];
       $id_rubrique = (int) $b['id_rubrique'];
       $id_secteur = (int) $b['id_secteur'];
  
       // sur chaque site copie les derniers syndication
       $s = sql_select("*", "spip_syndic_articles", "statut='publie' AND id_syndic='$id_syndic'","","maj DESC","10");  // par flot de 10 articles / site pour limiter la charge
       while ($a = sql_fetch($s)) {
       		$titre =  $a['titre'];
       		$url =  $a['url'];
          $id_syndic_article = $a['id_syndic_article']; 
                    
          // article avec mm titre existe ? (test doublons sur l'url plutot que sr le titre)
	        if (!$row = sql_fetsel("id_article","spip_articles","url_site=".sql_quote($url))) {        
            
            $texte = $a['descriptif'];
            //traitement pour syntaxe SPIP
            if($html2spip)
                  $texte = html2spip($texte);
            $lang  = $a['lang'];
            $url   = $a['url'];
            $tags =  $a['tags'];
            $lsDate = $a['date'];
            
          
            if ($lang=="") 	
                $lang = $GLOBALS['spip_lang'];  
                
            // cas particulier: 
            // site multilingue avec la configuration: 1 lang par rubrique  
            // on force l'article a avoir la langue de la rubrique ds lequel il est importee(pour omaidi)            
            if ($GLOBALS['meta']['multi_rubriques']=='oui') {
                  $s_lang = sql_select("lang", "spip_rubriques", "id_rubrique=$id_rubrique");
                  while ($a_lang = sql_fetch($s_lang)) 
                      $lang = $a_lang['lang'];                   
            }
            
            //$lsDate = date('Y-m-d H:i:s');            
            // creation de l'article
            $id_article = sql_insertq( 'spip_articles', array(
                                'titre'=>$titre, 'id_rubrique'=>$id_rubrique,
                                'texte'=>$texte, 'statut'=>$import_statut, 'id_secteur'=>$id_secteur,
                                'date'=> $lsDate, 'accepter_forum'=>$accepter_forum, 'lang'=>$lang, 'url_site'=>$url));
                                
            // lier article et site
            sql_insertq( 'spip_articles_syndic', array('id_article'=>$id_article, 'id_syndic'=>$id_syndic));
                                
            // gestion auteur            
            $auteurs= explode(", ",$a['lesauteurs']);            
            foreach ($auteurs as $k => $auteur) {                       
                 if ($current_id_auteur = rssarticle_get_id_auteur($auteur))
                      sql_insertq( 'spip_auteurs_liens', array('id_auteur'=>$current_id_auteur, 'id_objet'=>$id_article, 'objet'=>'article'));                
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
            
            // logo
            if ($copie_logo) {             
               if ($logo_site = inc_chercher_logo_dist($id_syndic,"id_syndic")) {
                  $logo_article = "arton$id_article.".$logo_site[3];
                  @copy($logo_site[0],_DIR_LOGOS."$logo_article");
               }                 
            }
                                                       		
        		$log_c++;
        		$log .= "\n - $titre";  
            
             // on "depublie" l'article syndique qui vient d'etre copie
            sql_update("spip_syndic_articles", array('statut' => '"refuse"'), "id_syndic_article=$id_syndic_article");

            // Mise à jour des dates de rubriques après création d'un article dedans
           if ($id_article) {
               if (function_exists('calculer_rubriques'))
                   calculer_rubriques();
               if (function_exists('calculer_langues_rubriques'))
                   calculer_langues_rubriques();
               if (function_exists('propager_les_secteurs'))
                   propager_les_secteurs();
           }
                  
          }  // test doublons
       }  
  } // FIN PILE
    
 	
	// log et alerte email
  $log .= "\n\n---------\nPlugin Copie RSS en Articles: $log_c articles copies\n";
  spip_log($log);
  $log .= $GLOBALS['meta']['adresse_site']."/ecrire/?exec=accueil";
       
  if ($email_alerte && $email_suivi !="" && $log_c > 0)                 
                  envoyer_mail($email_suivi,"Copie RSS en Articles", $log);
              
	// maintenance generale
  // mode auto: on efface les syndic_articles de plus de 2 mois pour soulager le systeme (cf genie/syndic) 
  // attention: on efface sur l'ensemble des sites syndiques ss tenir compte de l'option		
	if ($mode_auto) sql_delete('spip_syndic_articles', "maj < DATE_SUB(NOW(), INTERVAL 2 MONTH) AND date < DATE_SUB(NOW(), INTERVAL 2 MONTH)");
 
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

/**
 * 
 * Nettoyer l'utf-8 et ses accents 
 *
**/
function clean_utf8($t) {
	if (!preg_match('!\S!u', $t))
		$t = preg_replace_callback(',&#x([0-9a-f]+);,i', 'utf8_do', utf8_encode(utf8_decode($t)));
	return $t;
}


//passe le html en SPIP
//repris de memo.php, merci

function html2spip($lapage){
	$lapage=clean_utf8($lapage);
	
	// remettre les double quotes casé par texte_backend
	$lapage = str_replace('&#034;','"',$lapage);
	
	// PRETRAITEMENTS
	$lapage = str_replace("\n\r", "\r", $lapage); // echapper au greedyness de preg_replace
	$lapage = str_replace("\n", "\r", $lapage);

	// itals
	$lapage = preg_replace(",<(i|em)( [^>\r]*)?".">(.+)</\\1>,Uims", "{\\3}", $lapage);
	
	// gras (pas de {{ pour eviter tout conflit avec {)
	$lapage = preg_replace(",<(b|h[4-6])( [^>]*)?".">(.+)</\\1>,Uims", "@@b@@\\3@@/b@@", $lapage);
	$lapage = preg_replace(",<strong( [^>]*)?".">(.+)</strong>,Uims", "@@b@@\\2@@/b@@", $lapage);
	
	// entites
	include_spip('inc/charsets');
	$lapage = html2unicode($lapage, true); //secure?
		
	// liens avec possibilités de non fermeture du tag
	$lapage = preg_replace(",<a[ \t\n\r][^<>]*href=[^<>]*(http[^<>]*)[^<>]>(.*?)<,uims", "[\\2->\\1] <", $lapage);

	// images (cf ressource)
	$lapage = preg_replace(",<img[ \t\n\r][^<>]*src=[^<>]*(http[^<>'\"]*)[^<>]*>,uims","[img]\\1[//img]", $lapage);

		
	// intertitres
	$lapage = preg_replace(",<(h[1-3])( [^>]*)?".">(.+)</\\1>,Uims", "\r{{{ \\3 }}}\r", $lapage);
	// tableaux
	$lapage = preg_replace(",<tr( [^>]*)?".">,Uims", "<br />\r", $lapage);
	$lapage = preg_replace(",<t[hd]( [^>]*)?".">,Uims", " | ", $lapage);

	// POST TRAITEMENT
	$lapage = str_replace("\r", "\n", $lapage);

	// SUPPRIME LES TAGS
	if (eregi("<title.*>(.*)</title>", $lapage, $regs))
	$titre = textebrut($regs[1]);
	$lapage = textebrut($lapage);
	
	// Suite tableaux
	$lapage = preg_replace(",\n[| ]+\n,", "", $lapage);
	$lapage = preg_replace(",\n[|].+?[|].+?[|].+,", "\\0|\r", $lapage);
	
	// retablir les gras
	$lapage = preg_replace(",@@b@@(.*)@@/b@@,Uims","{{\\1}}",$lapage);
	
	//retablir les images pour les lire avec le plugin ressource
	$lapage = preg_replace('#\[img\](.*)\[\//img\]#Umis', "<$1>", $lapage);
	
	//nettoyer les "] qui dépassent parfois
	$lapage = preg_replace(",\"\],uims", "]", $lapage);
	
	return $lapage;
}

?>