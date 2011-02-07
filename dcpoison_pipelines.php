<?php

	/*
		Plugin Name: Duplicate Content Poison pour SPIP
		Author: Etienne BRACKERS (Loiseau2nuit.net) d'apres une creation originale de 512banque (deliciouscadaver.com) pour le CMS Wordpress
		Author URI: http://www.loiseau2nuit.net/ & http://www.deliciouscadaver.com
		Version: 0.1
		Description: Replaces a and i characters in your feed with their russian homographs, so that scrapers and normal users won't duplicate your precious content.
		Plugin URI: http://www.deliciouscadaver.com <- mettre le lien de la doc quand elle sera ecrite
	*/

	
	// Remplacement via affichage_final de tous les "a" et les "i"
	// par leurs homographes dans l'alphabet cyrillique
	// si et seulement si on est sur que ce n'est pas Googlebot qui demande la page
	// principe a etendre pour les autres bot (cf todo.txt)
	
		function dcpoison_affichage_final( $texte ) {
			global $notice;    
				if( !dcpoison_IsGooglebot() ) {
				        $texte = preg_replace("'(?!<.*?)i(?![^<>]*?>)'s", "і", $texte); 
					$texte = preg_replace("'(?!<.*?)a(?![^<>]*?>)'s", "а", $texte); 
        				$texte = str_replace(array('&lаquo;', '&rаquo;', 'аre_PаyPаl_LogіnPleаse'), array('&laquo;', '&raquo;', 'Are_PayPal_LoginPlease'), $texte);
					return $texte;
				} else {
					return $texte;
				}
		}
?>
