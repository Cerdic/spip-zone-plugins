<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_formulaire_playtaliste($p) {
    return calculer_balise_dynamique($p, 'formulaire_playtaliste', array('id_document'));
}

function balise_formulaire_playtaliste_stat($args, $filtres) {
	return $args;
}

function balise_formulaire_playtaliste_dyn($id_document) {
	
	$ajouter =  _request('ajouter_'.$id_document);
	$supprimer =  _request('supprimer_'.$id_document);
	$vider = _request('vider');
	$cookie_vide = 'oui';
	$doublon = 'non';
	
	// si le cookie existe on récupère son contenu,
	if (isset($_COOKIE['playtaliste'])){
		$cookie_vide = 'non';
		$playliste = unserialize($_COOKIE['playtaliste']);
		// on regarde si le doc n'est pas déjà dans la playliste
		if (in_array($id_document, $playliste))
			$doublon = 'oui';
	}

	// si il y a demande d'ajout du doc et que ce n'est pas un doublon
	if(($ajouter) && ($doublon !='oui')){
	
		// si la playliste n'existe pas on crée un tableau qui va recevoir le contenu
		if (!isset($playliste)) 
			$playliste = array();

		//on ajoute le doc au tableau de la playliste et on enregistre dans le cookie
		$playliste[] = $id_document;
		include_spip('inc/cookie');
		spip_setcookie('playtaliste', serialize($playliste), time()+86400);
		$doublon = 'oui';
		$cookie_vide = 'non';
		
	}
	
	
	if(($supprimer) && ($doublon ='oui')){
		
		//on supprime le doc du tableau de la playliste et on enregistre dans le cookie
			unset($playliste[array_search($id_document, $playliste)]);
			$playliste = array_values($playliste);
			include_spip('inc/cookie');
			spip_setcookie('playtaliste', serialize($playliste), time()+86400);
						
			$nb = count($playliste);
			if ($nb == 'O'){
				include_spip('inc/cookie');
				spip_setcookie('playtaliste', '', 0);
				$cookie_vide = 'oui';
			}
			$doublon = 'non';
			
	}
	
	// si on demande de vider la playlist
	if ($vider){
	
		include_spip('inc/cookie');
		spip_setcookie('playtaliste', '', 0);
		$cookie_vide = 'oui';
		$doublon = 'non';
		
	}
	
	
    return array(
        'formulaires/formulaire_playtaliste', 
        0, 
        array(
        	'id_document' => $id_document,
        	'doublon' => $doublon,
			'cookie_vide' => $cookie_vide
        )
    );
}
?>
