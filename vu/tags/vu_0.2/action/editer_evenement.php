<?php

/* 	On trouve ici les fonctions 'action' appelees par le formulaire		*/
/*	'editer_evenement' : insertion d'un nouvel evenement, revision 		*/
/* 	d'un evenement existant...						*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Edition ? Revision ?
function action_editer_evenement() {

	$securiser_action = charger_fonction('securiser_action', 'inc');

	// Teste si autorisation pour les actions d'editions
	$arg = $securiser_action();
/*
	// Envoi depuis les boutons "publier/supprimer cet evenement"
		if (preg_match(',^(\d+)\Wstatut\W(\w+)$,', $arg, $r)) {
		$id_evenement = $r[1];
		set_request('statut', $r[2]);
		revisions_evenement($id_evenement);
	} 	
*/
	// Envoi depuis le formulaire d'edition d'un evenement existant	
	if ($id_evenement = intval($arg)) { 	
		// Si cet evenement possede un 'id', alors elle n'est pas nouvelle
		// Effectuons donc une revision plutot qu'une edition.
		revisions_evenements($id_evenement);
	}

	// Envoi depuis le formulaire de creation d'un evenement	
	else if ($arg == 'oui') {
		$id_evenement = insert_evenement();
		if ($id_evenement) revisions_evenements($id_evenement);
	}
	// Erreur
	else{
		// Si nous sommes dans aucun des cas precedent, alors on 
		// a un probleme : renvoyons une erreur.
		include_spip('inc/headers');
		redirige_url_ecrire();
	}

	// A ce stade, il ne nous reste plus a choisir quelle page s'affiche a l'issue du processus
	if (_request('redirect')) {
		// Si une information de redirection existe on la recupere, et on redirige
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_evenement', $id_evenement, '&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else 
		// Sinon on se contente de renvoyer l'id de l'objet
		// (Utile par exemple pour une creation, ou la redirection est geree en amont)
		return array($id_evenement,'');

}

// Cette fonction ne sert en fait qu'a ajouter une nouvelle ligne dans la table, 
// elle y insert juste la date du jour, puis retourne 
// l'id de la ligne creee. Le reste la fonction 
// revision_evenement s'en occupe.
function insert_evenement() {

	spip_log("[Vu!] -- Cr&eacute;ation d'une nouvel evenement dans la base", "vu!");
	return sql_insertq("spip_vu_evenements", array('date' => date('Y-m-d')));

}


// Enregistre une revision d'un evenement
// $c est un contenu (par defaut on prend le contenu via _request())
function revisions_evenements($id_evenement, $c=false) {

// ** Champs normaux **
	if ($c === false) {
		// Si $c a sa valeur par defaut, alors on en fait un tableau,
		// que l'on remplit avec les nouvelles valeurs des differents champs
		$c = array();
		foreach (array(
			// Pour chacun de ces champs,
			'titre', 'lien', 'organisateur', 'date_evenement', 'lieu_evenement',
			'type', 'descriptif', 'source_lien', 'source_nom', 'statut'
		) as $champ)
			// on en recupere la nouvelle valeur (a condition qu'ils ne soient pas vides),
			if (($a = _request($champ)) !== null)
				// que l'on met dans le tableau $c
				$c[$champ] = $a;
	}

	// Si l'evenement est publie, invalider les caches et demander sa reindexation
	// (indispensable pour mettre a jour l'evenement du cote public)
	// $t est le statut actuel de l'objet, que l'on recupere en premier lieu
	$t = sql_getfetsel("statut", "spip_vu_evenements", "id_evenement=$id_evenement");
	if ($t == 'publie') {
		// Si le statut est publie, alors on indique que cet evenement devra etre invalidee
		$invalideur = "id='id_evenement/$id_evenement'";
		// et on demande une reindexation
		$indexation = true;
	}
	// On charge le fichier qui contient la fonction necessaire...
	include_spip('inc/modifier');
	// ... que l'on execute ensuite avec les parametres definis juste au dessus
	modifier_contenu('evenement', $id_evenement,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);


// ** Un cas special : changer le statut ? **
	// On recupere pour commencer le statut actuel de la breve,
	$row = sql_fetsel("statut", "spip_vu_evenements", "id_evenement=$id_evenement");
	// pour ensuite le stocker dans deux variables differentes
	$statut_ancien = $statut = $row['statut'];
	// Si un nouveau statut est demande, ET qu'il est different de l'actuel, 
	// ?? a rajouter pour la suite : "ET que nous avons les autorisations pour le changer"
	if (_request('statut', $c) AND _request('statut', $c) != $statut) {
		// Alors $statut acquiere sa valeur nouvelle (vu au dessus avec $champs)
		$statut = $champs['statut'] = _request('statut', $c);
	}

// ** Rendre effective la revision **
	// Si le tableau contenant les nouvelles valeurs est vide (rien a changer),
	// alors c'est termine !
	if (!$champs) return;

	// Si l'etape precedente est passee, alors on a des choses a faire.
	// On demande simplement une mise a jour de la table avec les nouvelles valeurs ($champs)
	sql_updateq('spip_vu_evenements', $champs, "id_evenement=$id_evenement");

// ** Post-modifications **
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_evenement/$id_evenement'");

}


?>
