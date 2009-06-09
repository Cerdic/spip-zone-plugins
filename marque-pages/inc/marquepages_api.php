<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function marquepages_formater_url($url){
	
	$url = trim($url);
	// On enlève le slash à la fin
	$url = preg_replace('|(.*)/$|i', '$1', $url);
	// On s'assure qu'il y a http://
	$url = preg_replace('|^(http://)?(.*)$|i', 'http://$2', $url);
	
	return $url;
	
}

// Tester si l'url est bonne
// Renvoie false si on arrive rien à récupérer
// Renvoie le titre si c'est une page HTML
// Sinon on essaye de renvoyer un titre pas trop moche
function marquepages_tester_url($url){
		
	include_spip('inc/distant');
	include_spip('base/abstract_sql');
	
	$url = marquepages_formater_url($url);
	$infos = recuperer_infos_distantes($url);
	
	if (!$infos)
		return false;
	elseif ($titre = trim($infos['titre']))
		return $titre;
	else{
		$chemin = parse_url($url, PHP_URL_PATH);
		$fichier = pathinfo($chemin, PATHINFO_FILENAME);
		$titre = rawurldecode($fichier);
		$titre = str_replace('_', ' ', $titre);
		$titre = str_replace('-', ' ', $titre);
		$titre = preg_replace('/(\s)+/', ' ', $titre);
		return $titre;
	}
	
}

// Renvoie l'identifiant du site s'il existe déjà, 0 sinon
function marquepages_site_existe($url, $id_rubrique=0){
	
	include_spip('base/abstract_sql');
	
	$url = marquepages_formater_url($url);
	
	$where = array(
		array('=', 'url_site', sql_quote($url)),
		array('=', 'statut', sql_quote('publie'))
	);
	
	// On précise id_rubrique s'il est donné
	if($id_rubrique != 0)
		array_push(
			$where,
			array('=', 'id_rubrique', intval($id_rubrique))
		);
	
	// On fait la requête
	$requete = sql_fetsel(
		'id_syndic',
		'spip_syndic',
		$where,
		'',
		array(),
		'1'
	);
	
	return $requete['id_syndic'] ? intval($requete['id_syndic']) : 0;
	
}

// Ajoute un marque-page, retourne 0 si ça marche pas
function marquepages_ajouter($id_rubrique, $url, $titre, $description, $statut, $tags){
	
	// Si jamais qqn utilise la fonction sans faire de tests avant
	if ($titre_defaut = marquepages_tester_url($url)){
		
		// Si le titre est vide on met celui par défaut
		if (!$titre)
			$titre = $titre_defaut;
		
		// Si le site n'existe pas encore, on le crée
		if (($id_syndic = marquepages_site_existe($url, $id_rubrique)) == 0){
			
			// On enlève le slash à la fin
			$url = marquepages_formater_url($url);
			
			$id_syndic = sql_insertq(
				'spip_syndic',
				array(
					'nom_site' => $titre,
					'url_site' => $url,
					'id_rubrique' => $id_rubrique,
					'descriptif' => $description,
					'date' => 'NOW()',
					'statut' => 'publie',
					'syndication' => 'non'
				)
			);
			
		}
		
		// Ensuite on crée le marque-page proprement dit, cad le forum
		$id_forum = sql_insertq(
			'spip_forum',
			array(
				'id_syndic' => $id_syndic,
				'url_site' => $url, // on remet l'URL, ça permet que la recherche prenne en compte
				'titre' => $titre,
				'texte' => $description,
				'date_heure' => 'NOW()',
				'statut' => $statut,
				'id_auteur' => $GLOBALS['auteur_session']['id_auteur'],
				'auteur' =>  $GLOBALS['auteur_session']['nom'],
				'email_auteur' => $GLOBALS['auteur_session']['email']
			)
		);
		
		// Enfin on ajoute les mots-clés s'il y en a
		if ($tags){
			include_spip('inc/tag-machine');
			ajouter_liste_mots($tags, $id_forum, 'tags', 'forum', 'id_forum', true);
		}
		
	}
	
	return $id_forum ? $id_forum : 0;
	
}

// Edite un marque-page déjà existant
// On ne peut pas changer l'URL ça n'a pas de sens
function marquepages_modifier($id_forum, $titre, $description, $statut, $tags){	
	
	// On modifie la table
	$tout_va_bien = sql_updateq(
		'spip_forum',
		array(
			'titre' => $titre,
			'texte' => $description,
			'statut' => $statut
		),
		'id_forum='.intval($id_forum)
	);
	
	// Enfin on ajoute les mots-clés s'il y en a
	if ($tags){
		include_spip('inc/tag-machine');
		ajouter_liste_mots($tags, $id_forum, 'tags', 'forum', 'id_forum', true);
	}
	
	return $tout_va_bien;
	
}

// Supprime un marque-page et éventuellement le site
// Retourne true si c'est bon, false sinon
function marquepages_supprimer($id_forum){
	
	// On commence par retirer tous les mots-clés
	sql_delete(
		'spip_mots_forum',
		'id_forum=' . intval($id_forum)
	);
	
	$r = sql_fetsel(
		'id_syndic',
		'spip_forum',
		array(
			array('=', 'id_forum', intval($id_forum))
		)
	);
	$id_syndic = $r['id_syndic'];
	
	// on supprime déjà le marque-page
	$tout_va_bien = sql_delete(
		'spip_forum',
		"id_forum=" . intval($id_forum) . " or id_parent=" . intval($id_forum)
	);
	
	if ($tout_va_bien){
		
		// si ya plus de marque-page sur le site, on le supprime aussi
		$r = sql_fetsel(
			'titre',
			'spip_forum',
			array(
				array('=', 'id_syndic', intval($id_syndic))
			)
		);
		
		if (!$r['titre']){
			$tout_va_bien = sql_delete(
				'spip_syndic',
				"id_syndic=" . intval($id_syndic)
			);
		}
		
	}
	
	return $tout_va_bien;
	
}

// Importer des marque-pages depuis un fichier d'export HTML de navigateur
function marquepages_importer_netscape($chemin, $id_rubrique){
	$retours = array();
	
	$html = file_get_contents($chemin);
	
	// On cree un tableau de tous les liens
    preg_match_all('/<a\s+(.*?)\s*\/*>([^<]*)/si', $html, $matches);
    $liens = $matches[1];
    $titres = $matches[2];
    
    foreach($liens as $i => $lien){
        $attributs = preg_split('/\s+/s', $lien);
        foreach ($attributs as $attribut) {
            $attribut = preg_split('/\s*=\s*/s', $attribut, 2);
            $attrTitre = $attribut[0];
            $attrValeur = eregi_replace('"', '&quot;', preg_replace('/([\'"]?)(.*)\1/', '$2', $attribut[1]));
            switch (strtolower($attrTitre)) {
                case "href":
                    $url = $attrValeur;
                    break;
                case "add_date":
                    $date = date('Y-m-d H:i:s', $attrValeur);
                    if (strtotime($date) > time())
                    	$date = date('Y-m-d H:i:s');
                    break;
            }
        }
        $titre = eregi_replace('"', '&quot;', trim($titre[$i]));
		
        marquepages_ajouter($id_rubrique, $url, $titre, $description, 'mppublic', '');
    }
    
   	$retours['message_ok'] = _T('marquepages:erreur_importation_ok');
	
	return $retours;
}

// Importer des marque-pages depuis un fichier d'export de delicious
function marquepages_importer_delicious($chemin, $id_rubrique){
	$retours = array();
	$retours['message_ok'] = 'Importation delicious';
	return $retours;
}

?>
