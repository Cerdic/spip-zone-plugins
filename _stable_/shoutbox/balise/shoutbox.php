<?php

/*
 * #SHOUTBOX dans le squelette
 *
 * (c) Fil 2007, licence GUN/GPL
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

	list($objet, $type, $id, $defaut) = $args;

	// valeur par defaut du formulaire
	if (!isset($defaut)) $defaut = '...';

	// nom de la shoutbox passe en argument #SHOUTBOX{},
	// sinon le contexte d'objet, sinon 'normal'
	$a = sinon(strval($type.$id), sinon($objet,'normal'));

	// Valeurs pas bonnes : on retourne un resultat vide
	if (strlen($a) > 25)
		return '';

	// OK : on envoie nos donnees
	return array($defaut, $a);
}

// http://doc.spip.org/@balise_SHOUTBOX_dyn
function balise_SHOUTBOX_dyn($defaut, $a) {

	// analyser les donnees

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
		$ou = 'objet,auteur,texte,date';
		$quoi = _q($a) .','
			. _q(sinon($GLOBALS['auteur_session']['nom'], $GLOBALS['ip'])) .','
			. _q(strval(_request('shoutbox_'.$a))) .','
			. 'NOW()';
		if (isset($GLOBALS['auteur_session']['id_auteur'])) {
			$ou .= ',id_auteur';
			$quoi .= ','._q($GLOBALS['auteur_session']['id_auteur']);
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
				'defaut' => $defaut,
				'bouton' => 'ok',
				'nouveau' => isset($id), # si on vient de faire l'insertion
				'erreur' => $erreur
			)
		);
}



?>
