<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

// fonction pour le pipeline, n'a rien a effectuer
function association_autoriser(){}

// autorisation d'editer des membres (ou de creer un membre depuis un auteur spip)
function autoriser_associer_adherents_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo' && !$qui['restreint']); // on retourne ok pour tous les admins non restreints
}

/* Les autorisation d'acces sont geres par des groupes de membres(d'auteur SPIP en fait,
un auteur non membre peut avoir un access, par exemple le personnel administratif de l'asso) */
/* Mapping des id de groupes et des autorisations associées, qu'ils soient ou non present dans le groupe, les webmestres ont tous les acces, les admins non restreints certains */
/* 1 => edition des groupes y compris ceux d'autorisation. defaut: webmestres */
/* 2 => edition des groupes d'id>100. defaut : admin non restreints */
/* 3 => voir les groupes d'id>100. defaut : redacteurs */
/* 10 => expert-comptable: toutes les autorisations sur la compta. */
/* 11 => comptable: peut enregistrer des ecritures mais pas modifier le plan comptable */
/* 12 => auditeur: acces en lecture seule a la comptabilité */
/* 20 => editer le profil et les options de l'association. defaut : webmestres */
/* 21 => voir info association. defaut : redacteurs */
/* 30 => editer membres. Ajouter/Supprimer des membres, editer les membres(ajout cotisations, modifications informations). defaut : admin non restreint */
/* 31 => voir membres. Lister les membres et voir leur page. defaut : admin non restreint */


/* teste si un auteur est dans un groupe ou une liste de groupes */
/* $id_groups peut etre un tableau ou un simple id de groupe */
/* return a boolean */
function is_in_groups($id_auteur, $id_groupes)
{
	$where = "id_auteur=$id_auteur AND ";
	if (is_array($id_groupes)) {
		$where .= sql_in("id_groupe", $id_groupes);
	} else {
		$where .= "id_groupe=$id_groupes";
	}

	if (sql_countsel("spip_asso_groupes_liaisons", $where) != 0) {
		return true;
	} else {
		return false;
	}
}


/**** Groupes ****/
/* gestion des autorisations. defaut : webmestres */
function autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return true; // on retourne ok pour tous les webmestres
	}
	return is_in_groups($qui['id_auteur'], 1); /* c'est le groupe 1 qui liste les gens qui peuvent toucher aux autorisations */
}

/* edition des groupes */
function autoriser_association_editer_groupes_dist($faire, $type, $id, $qui, $opt) {
	/* si l'id est-il inferieur a 100 et different de 0(creation d'un groupe) -> groupe d'autorisations: on retourne le resultat de la fonction d'autorisation de gestion des autorisations */
	if ($id!=0 && $id<100) { /* on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver */
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); 	
	}

	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true; // on retourne ok pour tous les admins non restreints
	}

	return is_in_groups($qui['id_auteur'], array(1,2)); // c'est le groupe 2 qui a le pouvoir de modifier les groupes(mais pas les autorisations), le groupe 1 peut modifier tout ce qui touche aux groupes(ceux d'autorisations inclus)
}

/* voir les groupes. defaut : redacteurs */
function autoriser_association_voir_groupes_dist($faire, $type, $id, $qui, $opt) {
	/* si l'id est-il inferieur a 100 et different de 0(creation d'un groupe) -> groupe d'autorisations: on retourne le resultat de la fonction d'autorisation de gestion des autorisations */
	if ($id!=0 && $id<100) { /* on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver */
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); 	
	}

	/* defaut : redacteurs */
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return true; 
	}

	return is_in_groups($qui['id_auteur'], array(3)); // c'est le groupe 3 qui a le pouvoir de voir les groupes(mais pas ceux des autorisations)
}


/**** Profil Association ****/
/* modifier le profil de l'association. defaut : webmestre */
function autoriser_association_editer_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return true; // on retourne ok pour tous les webmestres
	}

	return is_in_groups($qui['id_auteur'], array(20)); // c'est le groupe 20 qui a le pouvoir d'editer le profil de l'association.
}

/* voir le profil de l'association. defaut : rédacteurs */
function autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') return true;

	return is_in_groups($qui['id_auteur'], array(21,20)); // c'est le groupe 21 qui a le pouvoir de voir les informations de l'association, le 20 celui de les editer aussi
}

/* ceux qui peuvent voir les infos de l'association doivent avoir le bouton dans l'espace privé avec l'interface normale ou plugin navigation prive(bando) */
function autoriser_association_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}
function autoriser_association_bando_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}


/**** Gestion des membres ****/
/* editer les informations des membres. defaut : admin non restreint. */
function autoriser_association_editer_membres($faire, $type, $id, $qui, $opt) {
	/* defaut: tous les admins non restreints */
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true;
	}

	return is_in_groups($qui['id_auteur'], array(30)); // c'est le groupe 30 qui a le pouvoir de modifier les infos de tout le monde
}

/* voir la page des membres et leurs pages personnelle. defaut : admin non restreint. Pour les pages de membres, le membre en question y a forcement accès */
function autoriser_association_voir_membres($faire, $type, $id, $qui, $opt) {
	/* defaut: tous les admins non restreints */
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true;
	}

	/* si l'auteur connecté correspond à la page du membre a visiter c'est ok */
	if ($id == intval($GLOBALS['visiteur_session']['id_auteur'])) {
		return true;
	}

	return is_in_groups($qui['id_auteur'], array(31,30)); // c'est le groupe 31 qui a le pouvoir de voir les infos de tout le monde, le 30 celui de les editer
}

?>
