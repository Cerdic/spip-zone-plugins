<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function informerpremiereconnexion_affichage_final($flux){
	// Si on a un utilisateur connecté
	if (
		include_spip('inc/session')
		and session_get('id_auteur') > 0
		and session_get('en_ligne') == '0000-00-00 00:00:00'
		and !session_get('informer_premiere_connexion')
	){
		// On ajoute le message en haut de page, au tout début
		$message = recuperer_fond('inclure/informer_premiere_connexion');
		$flux = preg_replace('|(<body[^>]*>)|is', "$1\n$message", $flux);
		
		// On ajoute quelques styles minimaux par défaut
		$styles = <<<POUET
<style type="text/css">
#informer_premiere_connexion{
	background-color:#E5F9CD;
	coor:black;
	font-size:1.2em;
	padding:1em;
	text-align:center;
}
</style>
POUET;
		$flux = preg_replace('|(<head[^>]*>)|is', "$1\n$styles", $flux);
		
		// Maintenant on peut affirmer que le message a été lu
		session_set('informer_premiere_connexion', 'oui');
	}
	
	return $flux;
}
