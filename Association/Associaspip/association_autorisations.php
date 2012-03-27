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
/* 1 => edition des groupes y compris ceux d'autorisation */
/* 2 => edition des groupes d'id>100 */
/* 10 => expert-comptable: toutes les autorisations sur la compta. */
/* 11 => comptable: peut enregistrer des ecritures mais pas modifier le plan comptable */
/* 12 => auditeur: acces en lecture seule a la comptabilité */

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

/* gestion des autorisations : seuls les webmestres ont acces si non listes */
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

?>
