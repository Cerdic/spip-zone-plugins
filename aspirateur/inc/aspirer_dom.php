<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* DOM */

/**
 *
 * Prend en argument une URL ou un texte en html, la méthode pour parser, le tag et son attribut à trouver
 * Renvoie un array des attributs (href, src, …) trouvés
 * Necessite la fonction 
 *
 * @example
 * recupere_links('<div><img src="chemin_vers_img"></div>','loadHTML','img','src');
 * recupere_links('http://example.com','loadHTMLFile','a','href');
 *
 * @param $parent
 *	un texte html ou une URL
 * @param string $methode
 *   	'loadHTMLFile' si $parent est une URL
 *	'loadHTML' si $parent est un texte en html
 *
 * @param string $thistag
 *   permet de specifier le tag à rechercher
 * @param string $thisattribut
 *   permet de specifier l'attribut du tag à rechercher
 *
 * @return array
 *
**/
function recupere_links($parent,$methode="loadHTMLFile",$thistag='a',$thisattribut='href'){
	$links = array();
	$doc = new DOMDocument;
	$doc->preserveWhiteSpace = FALSE;

	@$doc->$methode($parent);
		
	$tags = $doc->getElementsByTagName($thistag);
		
	foreach ($tags as $tag){
		$attribut=$tag->getAttribute($thisattribut);
		$attribut=clean_href($attribut);
		$attribut=verifier_le_lien($attribut);
		
		//récupérer les textes des liens
		//$attributvalue=$tag->nodeValue;
		//$attributvalue=utf8_decode(str_replace("\r\n", "", $attributvalue));
		
		if(isset($attribut)) $links[] = $attribut;       
		
	}
	//on passe la page référente en premier si c'est une URL 
	if($thisattribut=='href' && $methode=="loadHTMLFile") 
		array_unshift($links, $parent);
	
	return array_unique($links);
}

/**
 * 
 * Nettoyer les espaces et les lignes d'une chaine
 * 
 * en l'occurence, des liens href très sales
 *
 * @param string $chaine
 *	la chaine à nettoyer
 *
 * @return string 
 * 	la chaine nettoyée
 *
**/
function clean_href($chaine){
	$chaine = preg_replace('#[[:blank:]]#Umis','',$chaine);
	$chaine = str_replace("\r\n", "", $chaine);
	//$href = strtolower(translitteration($href)); //reecrit les liens, necessite SPIP
	return $chaine;
}

/**
 * 
 * Vérifie les conditions d'un lien URL
 *
 * Utilise la fonction url_absolue de SPIP
 *
 * @param string $link
 *	l'url à vérifier
 *
 * @return string 
 * 	l'url absolue du lien ssi elle provient du site référent
 *
**/
function verifier_le_lien($link){
	$url_site_aspirer = lire_config('aspirateur/url_site_aspirer');
	//passer en lien absolu
	$link = url_absolue($link,$url_site_aspirer);
	//renvoyer uniquement le lien du site référent
	$parse_link=parse_url($link);
	$parse_url_du_site=parse_url($url_site_aspirer);
	if ($parse_link["host"] == $parse_url_du_site["host"]){
		//retourne le lien s'il n'a pas encore été aspiré
		$link = need_traitement($link,$url_site_aspirer);
		return $link;
	}
}
