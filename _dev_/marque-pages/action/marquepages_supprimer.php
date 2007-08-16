<?php
/*
 * Plugin marque-pages
 * Outils pour gérer un (ou plusieurs) système de marque-pages partagés
 * 
 * Auteur : Vincent Finkelstein
 * Distribué sous licence GPL
 * 
 */

function action_marquepages_supprimer(){
	
	include_spip('inc/marquepages_api');
	
	global $auteur_session;
	$id_auteur = $auteur_session['id_auteur'];
	$id_forum = _request('id');
	$redirect = _request('r');
	$redirect = rawurldecode($redirect);
	
	if(!marquepages_supprimer($id_forum))		
		// Si ça marche pas on revient sans rien faire
		redirige_par_entete($redirect);
	else{
		
		// Sinon on revient en recalculant la page
		if (strpos($redirect, '?') != false)
			$redirect .= '&var_mode=recalcul';
		else
			$redirect .= '?var_mode=recalcul';
		redirige_par_entete($redirect);
		
	}
	
}

?>
