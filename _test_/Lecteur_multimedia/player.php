<?php

/**
 * enclosures
 */

	
	 function enclosure_it($url, $titre){
		$enclosure = '<a rel="enclosure" href="'.$url.'"> '.$titre.' </a>' ;	
		return $enclosure ;
	}
	


	/* static public */
	// Contrairement au plugin original (http://zone.spip.org/trac/spip-zone/browser/_plugins_branche_stable_/_spip_1_9_0_/dewplayer)
	// Cette version pour la version 1.9.1 utilisera la modification du modèle doc pour traiter les adresses relatives 
	// qu'on retrouverait si on placerait un lien dans le texte par une balise <docXX>
	function Player_post_propre($texte) {
	
		$reg_formats="mp3";

		//trouver des liens complets 
		unset($matches) ;
		preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*>(.*)<\/a>/iU", $texte, $matches);
		
	    //print_r($matches);
		// S'il n'y a pas de lien sur des fichier de format mp3, retourner le texte sans changement
		if(!$matches[1][0]) return $texte; 

		$url_a=$matches[1];
		$lien=$matches[0];
		$titre_a=$matches[3];


		//remplacer le lien sur des fichier de format mp3 par le player flash permettant de jouer ce fichier 
		$y=0;
		foreach($url_a as $url){

			$titre=$titre_a[$y];
			if(preg_match_all("/http:\/\/[a-zA-Z0-9 ()\/\:\._%\?+'=~-]*\.mp3?/iU", $titre, $matches) AND $fichier=basename($url)) $titre = $fichier ;
			$texte = ereg_replace($lien[$y],enclosure_it($url,$titre).$GLOBALS['param_perso']['dewplayer'], $texte);
			
			
			$y++;
		}
		return $texte;
	}

?>