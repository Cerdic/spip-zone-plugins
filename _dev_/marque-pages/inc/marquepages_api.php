<?php
/*
 * Plugin marque-pages
 * Outils pour gérer un (ou plusieurs) système de marque-pages partagés
 * 
 * Auteur : Vincent Finkelstein
 * Distribué sous licence GPL
 * 
 */

// Teste si on a le droit d'ajouter un marque-page
function marquepages_autoriser_creer($id_rubrique){
	
	global $auteur_session;
	include_spip('inc/autoriser');
	return autoriser('creersitedans', 'rubrique', intval($id_rubrique), $auteur_session, NULL);
	
}

// Teste si on peut supprimer un marque-page
function marquepages_autoriser_supprimer($id_forum){
	
	// On dit que si on a le droit de modifier un MP, alors on a le droit de le supprimer
	global $auteur_session;
	include_spip('inc/autoriser');
	return autoriser('modifier', 'forum', intval($id_forum), $auteur_session, NULL);
	
}

// Teste si tous les paramètres sont bons
// Renvoie un message d'erreur si ça va pas, sinon une chaîne vide
function marquepages_test_parametres($url, $titre, $description, $tags, $id_rubrique){
	
	$message_erreur = '';
	
	// Si on a pas le droit faut aussi un message d'erreur
	if (!marquepages_autoriser_creer($id_rubrique))
		return $message_erreur = _T('marquepages:pas_le_droit');
	
	// Tester le nom du site
	if (strlen ($titre) < 2)
		$message_erreur = _T('form_prop_indiquer_nom_site');
	
	// Tester l'URL du site
	include_spip('inc/distant');
	$url = _request('mp_url');
	if (!recuperer_page($url)){
		$message_erreur = _T('form_pet_url_invalide');
	}
	
	return $message_erreur;
	
}

// Renvoie l'identifiant du site s'il existe déjà, 0 sinon
function marquepages_existe($url, $id_rubrique=0){
	
	include_spip('base/abstract_sql');
	
	// On enlève le slash à la fin
	$url = preg_replace('|(.*)/$|i', '$1', $url);
	// On s'assure qu'il y a http://
	$url = preg_replace('|^(http://)?(.*)$|i', 'http://$2', $url);
	
	$where = array("url_site=" . _q($url), "statut='publie'");
	// On précise id_rubrique s'il est donné
	if($id_rubrique != 0)
		array_push($where, "id_rubrique=" . intval($id_rubrique));
	
	// On fait la requête
	$a = spip_abstract_fetsel(
		array('id_syndic'),
		array('spip_syndic'),
		$where,
		'',
		array(),
		'1'
	);
	
	return ($a['id_syndic']) ? intval($a['id_syndic']) : 0;
	
}

// Ajoute un marque-page, retourne 0 si ça marche pas
function marquepages_ajouter($url, $titre, $description, $tags, $id_rubrique){
	
	// Si jamais qqn utilise la fonction sans les tests avant
	if (marquepages_test_parametres($url, $titre, $description, $tags, $id_rubrique) == ''){
		
		// Si le site n'existe pas encore, on le crée
		if (($id_syndic = marquepages_existe($url, $id_rubrique)) == 0){
			
			// On enlève le slash à la fin
			$url = preg_replace('|(.*)/$|i', '$1', $url);
			// On s'assure qu'il y a http://
			$url = preg_replace('|^(http://)?(.*)$|i', 'http://$2', $url);
			
			sql_insert('spip_syndic', "(nom_site, url_site, id_rubrique, descriptif, date, statut, syndication)", "(" . _q($titre) . ", " . _q($url) . ", " . intval($id_rubrique) .", " . _q($description) . ", NOW(), 'publie', 'non')");
			$id_syndic = spip_insert_id();
			
		}
		
		// Ensuite on crée le marque-page proprement dit, cad le forum privé
		$id_forum = sql_insert('spip_forum', "(id_syndic, titre, texte, date_heure, maj, statut, id_auteur, auteur, email_auteur)", "($id_syndic," . _q($titre) . ", " . _q($description) . ", NOW(), NOW(), 'prive', " . _q($GLOBALS['auteur_session']['id_auteur']) . ", " . _q($GLOBALS['auteur_session']['nom']) . ", " . _q($GLOBALS['auteur_session']['email']) . ")");
		
		// Enfin on ajoute les mots-clés
		include_spip('inc/tag-machine');
		ajouter_liste_mots($tags, $id_forum, 'tags', 'forum', 'id_forum', true);
		
	}
	
	return $id_forum ? $id_forum : 0;
	
}

// Supprime un marque-page (on supprime jamais les sites)
// Retourne true si c'est bon, false sinon
function marquepages_supprimer($id_forum){
	
	// Si on a pas l'autorisation on quitte
	if(!marquepages_autoriser_supprimer($id_forum))
		return false;
	else{
		
		$r = spip_abstract_fetsel(
			array('id_syndic'),
			array('spip_forum'),
			array('id_forum=' . intval($id_forum))
		);
		$id_syndic = $r['id_syndic'];
		
		// on supprime déjà le marque-page
		$tout_va_bien = spip_query("delete from spip_forum where id_forum=" . intval($id_forum) . " or id_parent=" . intval($id_forum));
		
		if ($tout_va_bien){
			
			// si ya plus de marque-page sur le site, on le supprime aussi
			$r = spip_abstract_fetsel(
				array('titre'),
				array('spip_forum'),
				array('id_syndic=' . intval($id_syndic))
			);
			
			if (!$r['titre']){
				$tout_va_bien = spip_query("delete from spip_syndic where id_syndic=" . intval($id_syndic));
			}
			
		}
		
		return $tout_va_bien;
		
	}
	
}

?>
