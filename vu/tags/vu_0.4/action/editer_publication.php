<?php

/* 	On trouve ici les fonctions 'action' appelees par le formulaire		*/
/*	'editer_publication' : insertion d'un nouvel publication, revision 		*/
/* 	d'un publication existant...						*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Edition ? Revision ?
function action_editer_publication() {

	$securiser_action = charger_fonction('securiser_action', 'inc');

	// Teste si autorisation pour les actions d'editions
	$arg = $securiser_action();
/*
	// Envoi depuis les boutons "publier/supprimer cet publication"
		if (preg_match(',^(\d+)\Wstatut\W(\w+)$,', $arg, $r)) {
		$id_publication = $r[1];
		set_request('statut', $r[2]);
		revisions_publication($id_publication);
	} 	
*/
	// Envoi depuis le formulaire d'edition d'un publication existant	
	if ($id_publication = intval($arg)) { 	
		// Si cet publication possede un 'id', alors elle n'est pas nouvelle
		// Effectuons donc une revision plutot qu'une edition.
		revisions_publications($id_publication);
	}

	// Envoi depuis le formulaire de creation d'un publication	
	else if ($arg == 'oui') {
		$id_publication = insert_publication();
		if ($id_publication) revisions_publications($id_publication);
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
			'id_publication', $id_publication, '&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else 
		// Sinon on se contente de renvoyer l'id de l'objet
		// (Utile par exemple pour une creation, ou la redirection est geree en amont)
		return array($id_publication,'');

}

// Cette fonction ne sert en fait qu'a ajouter une nouvelle ligne dans la table, 
// elle y insert juste la date du jour, puis retourne 
// l'id de la ligne creee. Le reste la fonction 
// revision_publication s'en occupe.
function insert_publication() {

	spip_log("[Vu!] -- Cr&eacute;ation d'une nouvel publication dans la base", "vu!");
	return sql_insertq("spip_vu_publications", array('date' => date('Y-m-d')));

}


// Enregistre une revision d'un publication
// $c est un contenu (par defaut on prend le contenu via _request())
function revisions_publications($id_publication, $c=false) {

// ** Champs normaux **
	if ($c === false) {
		// Si $c a sa valeur par defaut, alors on en fait un tableau,
		// que l'on remplit avec les nouvelles valeurs des differents champs
		$c = array();
		foreach (array(
			// Pour chacun de ces champs,
			'titre', 'lien', 'auteur', 'editeur', 'date_publication', 
			'type', 'descriptif', 'source_lien', 'source_nom', 'statut'
		) as $champ)
			// on en recupere la nouvelle valeur (a condition qu'ils ne soient pas vides),
			if (($a = _request($champ)) !== null)
				// que l'on met dans le tableau $c
				$c[$champ] = $a;
	}

	// Si la publication est publiee, invalider les caches et demander sa reindexation
	// (indispensable pour mettre a jour l'publication du cote public)
	// $t est le statut actuel de l'objet, que l'on recupere en premier lieu
	$t = sql_getfetsel("statut", "spip_vu_publications", "id_publication=$id_publication");
	if ($t == 'publie') {
		// Si le statut est publie, alors on indique que cet publication devra etre invalidee
		$invalideur = "id='id_publication/$id_publication'";
		// et on demande une reindexation
		$indexation = true;
	}
	// On charge le fichier qui contient la fonction necessaire...
	include_spip('inc/modifier');
	// ... que l'on execute ensuite avec les parametres definis juste au dessus
	modifier_contenu('publication', $id_publication,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);


// ** Un cas special : changer le statut ? **
	// On recupere pour commencer le statut actuel de la breve,
	$row = sql_fetsel("statut", "spip_vu_publications", "id_publication=$id_publication");
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
	sql_updateq('spip_vu_publications', $champs, "id_publication=$id_publication");

// ** Post-modifications **
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_publication/$id_publication'");

}


?>
