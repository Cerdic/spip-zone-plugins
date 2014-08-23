<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* CURL Contenu de la page */

/**
 * 
 * Récupère une page complète en CURL 
 *
 * à partir d'une URL
 * 
 *
 * @param string $url
 *	l'url de la page
 *
 * @return string 
 * 	la chaine
 *
**/
function la_page($url) {
	$timeout = 10;
	
	$ch = curl_init($url);
	
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
	if (preg_match('`^https://`i', $url))
	{
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Récupération du contenu en utf-8 retourné par la requête
	$la_page = curl_exec($ch);
	
	curl_close($ch);
	return $la_page;
}

/**
 * 
 *
 * Isole un contenu à partir d'une page récupérée
 *
 * @param string $chaine
 *	un contenu html
 *
 * @return string 
 * 	le contenu isolé
 *
**/
function isoler_contenu($chaine){
	//Identifiant du div pour isoler le contenu via xpath
	$div_id_contenu = trim(lire_config('aspirateur/div_id_contenu'));
	if($div_id_contenu){
		$doc = new DOMDocument();
		$doc->loadHTML($chaine);
		$xpath = new DOMXpath($doc);

		$tags = $xpath->query("//*[@id='$div_id_contenu']");
		    
			if (!is_null($tags)) {
				foreach ($tags as $tag) {
				    $innerHTML = '';
				
				    //see http://fr.php.net/manual/en/class.domelement.php#86803
				    $children = $tag->childNodes;
				    foreach ($children as $child) {
					$tmp_doc = new DOMDocument();
					$tmp_doc->appendChild($tmp_doc->importNode($child,true));       
					$innerHTML .= $tmp_doc->saveHTML();
				    }
				
				    return trim($innerHTML);
				}
			}
	}else{
	//Sinon ereg sur variables de début et de fin pour isoler le contenu
	$motif_debut_contenu_regex = lire_config('aspirateur/motif_debut_contenu_regex');
	$motif_fin_contenu_regex = lire_config('aspirateur/motif_fin_contenu_regex');
	if (preg_match("/$motif_debut_contenu_regex(.*)$motif_fin_contenu_regex/sU", $chaine, $contenu))
		$chaine = $contenu[1];
	else $chaine = $contenu;
	}

	return $chaine;   
}


/**
 * 
 * Recherche le titre d'une page à partir de son url
 * 
 *
 * @param string $url
 *	l'url de la page
 *
 * @return string 
 * 	la chaine du titre
 *
**/
function recupere_titre($url){
   //recupere toute la page
   $la_page=la_page($url);
   //fabriquer le titre -> todo réviser pour tester en premier le <title> sur $texte_encoded
   if(preg_match("/<title>(.*)<\/title>/siU", $la_page, $title_matches)){
   	// Clean up title: remove EOL's and excessive whitespace.
        $titre = preg_replace('/\s+/', ' ', $title_matches[1]);
        $titre = trim($titre);
        if ($titre!='') return $titre;	
   }
   
   $pattern = "/<h1(.*?)>(.*?)<\/h1>/";
   if(preg_match($pattern, $la_page, $h1)){
   $titre=$h1[2];
   $titre = preg_replace('#&nbsp;#Umis','',$titre);
   return $titre;	
   }
   return "titre_temporaire_de_la_page";
}

/**
 * 
 * Renvoie le contenu html d'une page à partir de son url
 *
 * Utilise plusieurs fonctions de traitement
 * 
 *
 * @param string $page_referente
 *	l'url de la page referente
 *
 * @param string $url_site_aspirer
 *
 * @return string 
 * 	le contenu traité
 *
**/
function recupere_contenu($page_referente,$url_site_aspirer){

	//recupere toute la page
	$contenu=la_page($page_referente);

	//isole un contenu spécifique
	$contenu=isoler_contenu($contenu);

	//nettoie le contenu html (option *)
	$nettoyer_contenu = lire_config('aspirateur/nettoyer_contenu');
	if($nettoyer_contenu==1) $contenu=clean_contenu($contenu);
	
	//force l'utf-8 (option *)
	$forcer_utf8 = lire_config('aspirateur/forcer_utf8');
	if($forcer_utf8==1) $contenu=char($contenu);
	
	//passe les liens en absolus
	$contenu=@liens_absolus($contenu, $url_site_aspirer);

	//convertit les entités HTML en unicode et les &eacute; en &#123;
	//$contenu = html2unicode($contenu, true /* secure */); //fonction SPIP
   
	return $contenu;
}

/**
 * 
 * Traite les documents d'un texte (si ils existent sur le site à aspirer)
 *
 * @param string $texte
 *	Le texte en entier, avec des liens, des documents ou des images (ou pas)
 *
 * @return array
 *	['texte'] Le texte avec en option le traitement SPIP des documents
 *	['documents'] Les documents extraits
 *
 *
**/
function traite_texte_documents($texte){
	
		$motif_chemin_documents = lire_config('aspirateur/motif_chemin_documents');
		//si SPIP demandé faire un str_replace dans le texte pour les documents rapatriés dans  SPIP
		$activer_spip = lire_config('aspirateur/activer_spip');
	
		//pour les documents, isoler les liens, en vérifiant que c'est bien le dossier qui nous intéresse
		$methode="loadHTML";
		$linksinside=array();
		$linksinside=recupere_links($texte,$methode,'a','href');
		$imagesinside=array();
		$imagesinside=recupere_links($texte,$methode,'img','src');
		$all_links=array_merge($linksinside,$imagesinside);
		
		//analyse et remplace en local les images trouvés dans le texte
		//option de réécriture des liens dans le texte, 
		//sauf les enclosures (nécessaires pour rapatrier éventuellement)
		$documents = array();
		foreach ($all_links as $linkin){
			//verifie si le lien est un document à conserver grace au motif demandé
			if (preg_match("'$motif_chemin_documents'", $linkin)){
				$documents[] = $linkin;
							
				if($activer_spip==1){
				//si SPIP faire un str_replace dans le texte pour les documents rapatriés dans SPIP
				$lien_en_spip=lien_spip_document($linkin);
				$texte = str_replace($linkin, $lien_en_spip, $texte);
				}
			}	
		}
		
		return array("texte"=>$texte,"documents"=>$documents);
}

