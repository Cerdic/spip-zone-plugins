<?php

/*	Fonction appelee par 'veille_voir', via le fond			*/
/*	'/prive/infos/publication.html'.					*/
/*	Permet de verifier qu'on est bien autorise changer de statut 	*/
/*	a la volee (sans entrer dans 'veille_edit') et affiche le	*/
/*	formulaire si oui.						*/


function instituer_publication($id_publication, $statut=-1){
	
	// On verifie les autorisations pour changer le statut
	$autorisation = autoriser('modifier','publication', $id_publication);
	// Si c'est bon, alors on appelle la fonction du plugin 
	// qui va effectuer l'operation 'inc/instituer_objet'
	if ($autorisation) {
		// On precharge la fonction du formulaire
		$instituer_publication = charger_fonction('instituer_publication', 'inc');
		// On l'execute en la retournant.
		return $instituer_publication($id_publication, $statut);
	}

	//Sinon, on ne retourne rien
	return "";
}


?>
