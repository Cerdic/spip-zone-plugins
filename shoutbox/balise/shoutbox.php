<?php

/*
 * #SHOUTBOX dans le squelette
 *
 * (c) Fil 2007, licence GNU/GPL
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
	return calculer_balise_dynamique($p,
		'SHOUTBOX', # nom de la balise ?
		array('objet')
	);
}


// Dans $args on recupere un array des valeurs collectees par balise_SHOUTBOX
// (dans cet exemple : aucun) ainsi que les eventuels
// parametres supplementaires : #SHOUTBOX{article,#ID_ARTICLE}
// Dans $filtres on recupere ce qui est apres la balise dans la notation
// [(#SHOUTBOX|x)]
function balise_SHOUTBOX_stat($args, $filtres) {

	// valeur par defaut de la taille de la zone d'affichage (en nombre messages)
	$taille = isset($args[2]) ? $args[2] : 10;

	// nom de la shoutbox passe en argument #SHOUTBOX{},
	// sinon le contexte d'objet, sinon 'normal'
	$objet = isset($args[1]) ? $args[1] : $args[0];
	$a = sinon($objet,'normal');

	// Valeurs pas bonnes : on retourne un resultat vide
	if (strlen($a) > 25)
		return '';

	// OK : on envoie nos donnees
	return array($a, $taille);
}

// https://code.spip.net/@balise_SHOUTBOX_dyn
function balise_SHOUTBOX_dyn($a, $taille) {

	// Le nickname c'est celui qu'on a donne, meme si on est loge
	if (isset($GLOBALS['visiteur_session']['session_nom'])) {
		$nick = $GLOBALS['visiteur_session']['session_nom'];
	} elseif (isset($GLOBALS['visiteur_session']['nom'])) {
		$nick = $GLOBALS['visiteur_session']['nom']; 
	} else {
		$nick = '';
	}

	// si $_POST correspondant a notre formulaire : stocker un truc
	// dans la base de donnees
	if (_request('valide'.$a)
	AND strlen($val = strval(_request('shoutbox_'.$a)))) {
		// antispam
		if (_request('nobot')) {
			spip_log('spam');
			return '';
		}

		// stocker dans la base de donnees (ici table spip_meta)
		$ou = 'objet,auteur,texte,date';
		$quoi = _q($a) .','
			. _q(sinon($nick, $GLOBALS['ip'])) .','
			. _q($val) .','
			. 'NOW()';
		if (isset($GLOBALS['visiteur_session']['id_auteur'])) {
			$ou .= ',id_auteur';
			$quoi .= ','._q($GLOBALS['visiteur_session']['id_auteur']);
		}
		$id = sql_insert('spip_shoutbox',
			"($ou)",
			"($quoi)"
		);


		// invalider les caches pour que tout le monde voie les messages
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}

	// Afficher le squelette resultant
	//
	// Ici il faut avoir fait le minimum de traitements (typo etc,
	// sont plutot a faire dans le squelette)

	// si appel ajax on ne renvoie que le contenu
	$squelette = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
		? 'inc-shoutbox'
		: 'formulaires/shoutbox';

	return
		array(
			// squelette
			$squelette,
			// delai
			3600,
			// contexte
			array(
				'objet' => $a,
				'taille' => $taille,
				'bouton' => 'ok',
				'nouveau' => isset($id), # si on vient de faire l'insertion
				'erreur' => $erreur
			)
		);
}
