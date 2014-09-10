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
 *
 * @example 
 * 	do_rss("nom_fichier.xml",3);
 *
 * @param string $fichier
 *	Le nom du fichier xml à enregistrer
 *
 * @param string $nombre_de_pages
 *	Le nombre_de_pages à traiter en items
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
		xmlns:wfw='http://wellformedweb.org/CommentAPI/'
		xmlns:sy='http://purl.org/rss/1.0/modules/syndication/'
		xmlns:slash='http://purl.org/rss/1.0/modules/slash/'
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
	
		//todo trouver une fonction configurable (choix du tag daté)
		$date = extraire_date($texte);
		$date = $date ? $date : gmdate("Y-m-d\TH:i:s\Z");
		
		//cree un item pour chaque page
		$flux.= "\n<item xml:lang='fr'>\n";
		
			$flux.=do_item($titre,$link,$texte,$date);
			
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
 * 	do_item($titre,$url_page,$texte,$date);
 *
 * @param string $titre
 *	Le titre de l'item
 *
 * @param string $url_page
 *	L'url de l'item
 *
 * @param string $texte
 *	Le texte html (encodé ou pas) pour le RSS de l'item
 *
 * @param string $date
 *	La date de l'item
 *
 * @return string 
 *
**/
function do_item($titre,$url_page,$texte,$date){
	$nom_site_aspirer = lire_config('aspirateur/nom_site_aspirer');
	$flux = "<title>".$titre."</title>\n";
	$flux.= "<guid isPermaLink='true'>".quote_amp($url_page)."</guid>\n";
	$flux.= "<dc:date>$date</dc:date>\n";
	$flux.= "<dc:format>text/html</dc:format>\n";
	$flux.= "<dc:language>fr</dc:language>\n";
	$flux.= "<dc:creator>".$nom_site_aspirer."</dc:creator>\n";
	$flux.= "<description></description>\n"; //todo
	$flux.= "<pubDate></pubDate>\n"; //todo
	$flux.= "<content:encoded><![CDATA[".$texte."]]>\n";
	$flux.= "</content:encoded>\n";
	
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
