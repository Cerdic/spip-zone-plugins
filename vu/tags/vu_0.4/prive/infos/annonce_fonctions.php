<?php

/*	Fonction appelee par 'veille_voir', via le fond			*/
/*	'/prive/infos/annonce.html'.					*/
/*	Permet de verifier qu'on est bien autorise changer de statut 	*/
/*	a la volee (sans entrer dans 'veille_edit') et affiche le	*/
/*	formulaire si oui.						*/


function instituer_annonce($id_annonce, $statut=-1){
	
	// On verifie les autorisations pour changer le statut
	$autorisation = autoriser('modifier','annonce', $id_annonce);

	// Si c'est bon, alors on appelle la fonction du plugin 
	// qui va effectuer l'operation 'inc/instituer_objet'
	if ($autorisation) {
		// On precharge la fonction du formulaire
		$instituer_annonce = charger_fonction('instituer_annonce', 'inc');
		// On l'execute en la retournant.
		return $instituer_annonce($id_annonce, $statut);
	}

	//Sinon, on ne retourne rien
	return "";
}


?>
