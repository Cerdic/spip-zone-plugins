<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* traitement RSS */

/**
 *
 * Fabriquer un flux pour un fichier rss et l'enregistrer
 *
 * La seule variable est le nom du fichier
 *
 * @example 
 * 	echo do_rss("nom_fichier.xml");
 *
 * @param string $fichier
 *	Le nom du fichier xml à enregistrer
 *
 * @return string 
 * 	Le flux_rss.xml enregistré
 *
**/

function do_rss($fichier,$nombre_de_pages){
	$path=_DIR_IMG."aspirateur/";
	// verif si repertoire aspirateur dispo
	if (!is_dir($path)) {                                     
                   if (!mkdir ($path, 0777)) // on essaie de le creer  
                        return _T('aspirateur:erreur_ecrire_stockage').$path; 
        }
	$nom_site_aspirer = lire_config('aspirateur/nom_site_aspirer');
	$url_site_aspirer = lire_config('aspirateur/url_site_aspirer');
	$descriptif_site = lire_config('aspirateur/descriptif_site');
	$page_referente = lire_config('aspirateur/page_referente');
	

	$flux="<?xml version='1.0' encoding='utf-8'?>\n"; 
	$flux.= "<rss version='2.0'	
		xmlns:dc='http://purl.org/dc/elements/1.1/'
		xmlns:content='http://purl.org/rss/1.0/modules/content/'
		xmlns:atom='http://www.w3.org/2005/Atom'>\n";
	
	$flux.= "<channel xml:lang='fr'>\n";
	$flux.= "<title>".$nom_site_aspirer."</title>\n";
	$flux.= "<link>".$url_site_aspirer."</link>\n";
	$flux.= "<description>".$descriptif_site."</description>\n";    
	$flux.= "<language>fr</language>\n";  
	$flux.= "<generator>SPIP - www.spip.net</generator>\n";  
	$flux.= "<atom:link href='".url_absolue(_DIR_IMG."/aspirateur/$fichier")."' rel='self' type='application/rss+xml' />\n";

	//agir sur chaque liens de page trouvés
	$links = array();
	$links = recupere_links($page_referente,'loadHTMLFile','a','href');
	
	//on reduit le tableau au nombre de liens demandées
	array_splice($links, $nombre_de_pages);
	$flux .= traiter_items($links,$url_site_aspirer);
	
	$flux.= "</channel>\n";
	$flux.= "</rss>\n";
	if(file_put_contents($path.$fichier, $flux)) return $path.$fichier;
}


/**
 * 
 * Fabrique une collection d'items à insérer dans un channel RSS
 *
 * prend en argument un array de liens
 *
 * Affiche indépendamment de l'insertion la liste des liens
 *
 * @example 
 * 	traiter_items($array);
 *
 * @param string $links
 *	array des liens
 * 
 * @param string $url_du_site
 *	url du site référent
 *
 * @return string 
 *
**/
function traiter_items($links,$url_du_site){

	$flux=null;
	
	foreach ($links as $link){
		
		//le contenu
		$titre= recupere_titre($link);
		$texte= recupere_contenu($link,$url_du_site);
		
		//pour enclosure
		//extrait les documents du contenu, sans changer l'url d'origine
		$traite_texte_documents=traite_texte_documents($texte);
		$documents=$traite_texte_documents['documents'];
		
		//dans le texte transforme les liens des documents en chemin SPIP (option *)
		$traite_texte_documents=traite_texte_documents($texte);
		$texte=$traite_texte_documents['texte'];
		
		//traitement du texte via SPIP pour rss
		$texte = texte_backend($texte); 
		
		//cree un item pour chaque page
		$flux.= "\n<item xml:lang='fr'>\n";
		
			$flux.=do_item($titre,$link,$texte);
			
			//enclosure des liens de documents trouvés dans le contenu
			foreach ($documents as $document){
				$flux.= enclosure_doc($document);	
			}
			
		$flux.= "</item>\n";
	}
		
	return $flux;
}

/**
 * 
 * Compose le contenu d'un item à insérer dans une collection d'items rss
 *
 * @example 
 * 	do_item($titre,$url_page,$texte_encoded);
 *
 * @param string $titre
 *	Le titre de l'item
 * @param string $url_page
 *	L'url de l'item
 * @param string $texte_encoded
 *	Le texte html encodé pour le RSS de l'item
 *
 * @return string 
 *
**/
function do_item($titre,$url_page,$texte_encoded){
	$nom_site_aspirer = lire_config('aspirateur/nom_site_aspirer');
	$flux = "<title>".$titre."</title>\n";
	$flux.= "<guid isPermaLink='true'>".quote_amp($url_page)."</guid>\n";
	$flux.= "<dc:date>".gmdate("Y-m-d\TH:i:s\Z")."</dc:date>\n"; //todo
	$flux.= "<dc:format>text/html</dc:format>\n";
	$flux.= "<dc:language>fr</dc:language>\n";
	$flux.= "<dc:creator>".$nom_site_aspirer."</dc:creator>\n";
	$flux.= "<description></description>\n"; //todo
	$flux.= "<pubDate></pubDate>\n";
	$flux.= "<content:encoded>".$texte_encoded."</content:encoded>\n";
	
	return $flux;
}

/**
 * 
 * Compose le contenu d'un enclosure à insérer dans un item RSS
 *
 * @param string $fichier
 * chemin du document, qui doit exister !
 *
 * @return string 
 * 	le tag enclosure à insérer
 *
**/
function enclosure_doc($fichier){
	include_spip('inc/distant');
	$a=recuperer_infos_distantes($fichier);
	$length= $a['taille'];
	$extension= $a['extension'];
	$type=$a['mime_type'];
	//if($type !='text/html')
	return '<enclosure url="'.$fichier.'" length="'.$length.'" type="'.$type.'" />'."\n";	
}

// not use
/**
 * Encode du HTML pour transmission XML
 * notamment dans les flux RSS
 *
 * http://doc.spip.org/@texte_backend
 *
 * @param $texte
 * @return mixed
 */

function texte_backend_aspirateur($texte) {

	static $apostrophe = array("&#8217;", "'"); # n'allouer qu'une fois

	// echapper les tags &gt; &lt;
	$texte = preg_replace(',&(gt|lt);,S', '&amp;\1;', $texte);

	// importer les &eacute;
	$texte = filtrer_entites($texte);

	// " -> &quot; et tout ce genre de choses
	$u = $GLOBALS['meta']['pcre_u'];
	$texte = str_replace("&nbsp;", " ", $texte);
	$texte = preg_replace('/\s\s+/'," ",$texte);
	// ne pas echapper les sinqle quotes car certains outils de syndication gerent mal
	$texte = entites_html($texte, false, false);

	// verifier le charset
	$texte = charset2unicode($texte);

	// Caracteres problematiques en iso-latin 1
	if ($GLOBALS['meta']['charset'] == 'iso-8859-1') {
		$texte = str_replace(chr(156), '&#156;', $texte);
		$texte = str_replace(chr(140), '&#140;', $texte);
		$texte = str_replace(chr(159), '&#159;', $texte);
	}

	// l'apostrophe curly pose probleme a certains lecteure de RSS
	// et le caractere apostrophe alourdit les squelettes avec PHP
	// ==> on les remplace par l'entite HTML
	return str_replace($apostrophe, "'", $texte);
}

