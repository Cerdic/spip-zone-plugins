<?php

/*
 * Un modele de balise dynamique : #SHOUTBOX dans le squelette
 *
 * (c) Auteurs 2007-2008, licence GUN/GPL
 * http://urldedocumentation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite



// Trois fonctions :

// 1. balise_SHOUTBOX indique au *compilo* que la balise est dynamique,
//    precise sa fonction _stat, et les elements de contexte a reserver
//    dans le compilateur (par exemple, dire qu'on veut reserver la valeur
//    #ID_ARTICLE d'un eventuel article englobant)

// 2. balise_SHOUTBOX_stat analyse ce contexte lors du *calcul* de la page
//    et le memorise sous forme d'un appel php a la fonction _dyn

// 3. balise_SHOUTBOX_dyn recupere les valeurs a l'*execution*
//    (c-a-d a chaque hit) et indique le squelette et contexte
//    de rendu de ce squelette, en lui passant les valeurs a afficher


function balise_SHOUTBOX ($p) {

	spip_log('balise_SHOUTBOX()');

	return calculer_balise_dynamique($p,
		'SHOUTBOX', # nom de la balise ?
		array('titre', 'nom', 'nom_site')
	);
}


// Dans $args on recupere un array des valeurs collectees par balise_SHOUTBOX
// (dans cet exemple : titre, nom, nom_site) ainsi que les eventuels
// parametres supplementaires : #SHOUTBOX{a,b,c}
// Dans $filtres on recupere ce qui est apres la balise dans la notation
// [(#SHOUTBOX|x)]
function balise_SHOUTBOX_stat($args, $filtres) {

	spip_log('balise_SHOUTBOX_stat()');


	list($titre, $nom, $nom_site, $a, $b, $c) = $args;

	// valeur par defaut du formulaire
	$defaut = sinon(sinon($titre,sinon($nom, $nom_site)),
		_T('info_sans_titre'));

	// nom de la shoutbox passe en argument #SHOUTBOX{}, sinon 'normal'
	$a = sinon($a, 'normal');

	// Valeurs pas bonnes : on retourne un resultat vide
	#if (!email_valide($email))
	#	return '';

	// OK : on envoie nos donnees
	return array($defaut, $a);
}

// http://doc.spip.org/@balise_SHOUTBOX_dyn
function balise_SHOUTBOX_dyn($defaut, $a) {

	spip_log('balise_SHOUTBOX_dyn()');

	// analyser les donnees
	$shoutbox = @unserialize($GLOBALS['meta']['shoutbox']);
	if (!is_array($shoutbox)) $shoutbox = array();
	if (!is_array($shoutbox[$a])) $shoutbox[$a] = array();

	// si $_POST correspondant a notre formulaire : stocker un truc
	// dans la base de donnees
	if (_request('valide'.$a)
	AND _request('shoutbox_'.$a)) {
		// antispam
		if (_request('nobot')) {
			spip_log('spam');
			return '';
		}

		// supprimer la 10eme ligne et ajouter la nouvelle
		while (count($shoutbox[$a]) > 9) array_shift($shoutbox[$a]);
		$ligne = date('H:i:s')
			. ' - '
			. sinon($GLOBALS['auteur_session']['nom'], $GLOBALS['ip'])
			. ': '
			. strval(_request('shoutbox_'.$a));
		$shoutbox[$a][] = $ligne;

		// stocker dans la base de donnees (ici table spip_meta)
		spip_log("INSERER DES DONNEES");
		ecrire_meta('shoutbox', serialize($shoutbox));
		
		// invalider les caches pour que tout le monde voie les messages
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}

	// Afficher le squelette resultant
	//
	// Ici il faut avoir fait le minimum de traitements (typo etc,
	// sont plutot a faire dans le squelette)
	return 
		array(
			// squelette
			'formulaires/shoutbox',
			// delai
			3600,
			// contexte
			array(
				# passer un id si on en met plusieurs
				'id' => $a,
				'defaut' => $defaut,
				'texte' => join('<br />',$shoutbox[$a]),
				'bouton' => 'ok',
				'erreur' => $erreur
			)
		);
}



?>
