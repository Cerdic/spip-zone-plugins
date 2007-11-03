<?php

/*
 * Un modele de balise dynamique : #DYNAMIQUE dans le squelette
 *
 * (c) Auteurs 2007-2008, licence GUN/GPL
 * http://urldedocumentation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite



// Trois fonctions :

// 1. balise_DYNAMIQUE indique au *compilo* que la balise est dynamique,
//    precise sa fonction _stat, et les elements de contexte a reserver
//    dans le compilateur (par exemple, dire qu'on veut reserver la valeur
//    #ID_ARTICLE d'un eventuel article englobant)

// 2. balise_DYNAMIQUE_stat analyse ce contexte lors du calcul de la page
//    et le memorise sous forme d'un appel php a la fonction _dyn

// 3. balise_DYNAMIQUE_dyn recupere les valeurs a l'execution
//    (c-a-d a chaque hit) et indique le squelette et contexte
//    de rendu de ce squelette, en lui passant les valeurs a afficher


function balise_DYNAMIQUE ($p) {
	return calculer_balise_dynamique($p,
		'DYNAMIQUE', # nom de la balise ?
		array('id_auteur', 'id_article', 'email')
	);
}


// Dans $args on recupere un array des valeurs collectees par balise_DYNAMIQUE
// (dans cet exemple : id_ateur, id_article, email) ainsi que les eventuels
// parametres supplementaires : #DYNAMIQUE{a,b,c}
// Dans $filtres on recupere ce qui est apres la balise dans la notation
// [(#DYNAMIQUE|x)]
function balise_DYNAMIQUE_stat($args, $filtres) {
	list($id_auteur,$id_article,$email,$a,$b,$c) = $args;

	// Valeurs pas bonnes : on retourne un resultat vide
	if (!email_valide($email))
		return '';

	// OK : on conserve les donnees
	return $args;
}

// http://doc.spip.org/@balise_DYNAMIQUE_dyn
function balise_DYNAMIQUE_dyn($id_auteur, $id_article, $mail) {
	// traiter les donnees

	// antispam
	if (_request('nobot')) {
		spip_log('spam');
		return '';
	}

	// stocker un truc dans la base de donnees
	if (_request('enregistrer')
	AND _request('valeurs') == 'ok') {
		spip_log("INSERER DES DONNEES");
	}

	return 
		array(
			// squelette
			'formulaires/dynamique',
			// delai
			3600,
			// contexte
			array(
			'id' => $id,
			'email' => _T('form_prop_indiquer_email'),
			'texte' => 'un texte',
			'erreur' => $erreur
			)
		);
}



?>
