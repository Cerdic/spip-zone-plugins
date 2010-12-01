<?php

/*	Fonction appelee par 'actualites_voir', via le fond			*/
/*	'/prive/infos/actualite.html'.					*/
/*	Permet de verifier qu'on est bien autorise changer de statut 	*/
/*	a la volee (sans entrer dans 'actualites_edit') et affiche le	*/
/*	formulaire si oui.						*/


function instituer_actualite($id_actualite, $statut=-1){
	
	// On verifie les autorisations pour changer le statut
	$autorisation = autoriser('modifier','actualite', $id_actualite);

	// Si c'est bon, alors on appelle la fonction du plugin 
	// qui va effectuer l'operation 'inc/instituer_objet'
	if ($autorisation) {
		// On precharge la fonction du formulaire
		$instituer_actualite = charger_fonction('instituer_actualite', 'inc');
		// On l'execute en la retournant.
		return $instituer_actualite($id_actualite, $statut);
	}

	//Sinon, on ne retourne rien
	return "";
}

function actualite_voir_en_ligne ($type, $id, $statut=false, $image='racine-24.gif', $af = true, $inline=true) {

	$en_ligne = $message = '';
	switch ($type) {
	
	case 'actualite':
			if ($statut == 'publie')
				$en_ligne = 'calcul';
			else if ($statut == 'prop')
				$en_ligne = 'preview';
			break;
	
	default: return '';
	}

	if ($en_ligne == 'calcul')
		$message = _T('icone_voir_en_ligne');
	else if ($en_ligne == 'preview'
	AND autoriser('previsualiser'))
		$message = _T('previsualiser');
	else
		return '';

	$h = generer_url_action('redirect', "type=$type&id=$id&var_mode=$en_ligne");

	return $inline  
	  ? icone_inline($message, $h, $image, "rien.gif", $GLOBALS['spip_lang_left'])
	: icone_horizontale($message, $h, $image, "rien.gif",$af);

}

?>
