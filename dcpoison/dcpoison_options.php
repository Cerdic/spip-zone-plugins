<?php
	/*
		Plugin Name: Duplicate Content Poison pour SPIP
		Author: Etienne BRACKERS (Loiseau2nuit.net) d'apres une creation originale de 512banque (deliciouscadaver.com) pour le CMS Wordpress
		Author URI: http://www.loiseau2nuit.net/ & http://www.deliciouscadaver.com
		Version: 0.1
		Description: Replaces a and i characters in your feed with their russian homographs, so that scrapers and normal users won't duplicate your precious content.
		Plugin URI: http://www.deliciouscadaver.com <- mettre le lien de la doc quand elle sera ecrite
	*/


	// ca je sais pas ce que c'est
	// Surement une subtilite wordpressienne qui m'echappe...
		
		// add_filter('the_content', 'add_buster'); 


	// verifier que ce n'est pas GoogleBot qui demande la page
	// auquel cas on lui sert la version bien francaise
	// sinon bye bye le referencement !

	// faire plutôt un test sur le useragent que sur une eventuelle ip ou Hostname

		function dcpoison_IsGooglebot() {
			$googleip = $_SERVER ['REMOTE_ADDR'];
			// souvent un nom de serveur du style crawl-66-249-66-1.googlebot.com
			$name = gethostbyaddr ( $googleip );
			if (strpos($name, "googlebot.com" )===false) {
				return false;
			} else {
				return true; // ce n'est pas googlebot
			}
		}


// Quelques pistes :


// $ua = $_SERVER['HTTP_USER_AGENT'];
// $uaGoogle="Googlebot/2.1 (+http://www.google.com/bot.html)";
//
// if($ua==$uaGoogle){
//  print("Salut Google Bot");
// }else{
//  print("Salut Visiteur");
// }


// liste des UA

// http://www.useragentstring.com/pages/useragentstring.php

?>

