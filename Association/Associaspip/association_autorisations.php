<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @note
 *   http://programmer.spip.net/autoriser
 */
function association_autoriser(){
}

/**
 * Teste si un auteur est dans un groupe ou une liste de groupes
 *
 * @param int $id_auteur
 *   ID de l'auteur dont on verifie l'appartenance
 * @param int|array $id_groups
 *   Liste des ID des groupes d'appartenance a verifier
 * @return bool
 *   Vrai si l'auteur est dans le(s) groupe(s), faux sinon
 */
function is_in_groups($id_auteur, $id_groupes)
{
	if (is_array($id_auteur)) {
		$id_auteur = $id_auteur['id_auteur'];
	}
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

/*****************************************
 * @defgroup autoriser_associer
 *
 * Groupes
 * Les autorisation d'acces sont geres par des groupes de membres(d'auteur SPIP
 * en fait, un auteur non membre peut avoir un access, par exemple le personnel
 * administratif de l'asso)
 *
 * Mapping des id de groupes et des autorisations associées :
 * - qu'ils soient ou on present dans le groupe, les webmestres ont tous les acces,
 * - les admins non restreints certains.
 * 0x Groupes ****************************
 * 01 => edition des groupes y compris ceux d'autorisation. defaut: webmestres
 * 02 => edition des groupes d'id>100. defaut : admin non restreints
 * 03 => voir les groupes d'id>100. defaut : redacteurs
 * 1x Comptabilite ***********************
 * 10 => expert-comptable : toutes les autorisations sur la compta.
 * 11 => comptable/tresorier : peut enregistrer des ecritures mais pas modifier le plan comptable
 * 12 => auditeur : acces en lecture seule a la comptabilité
 * 2x Association ************************
 * 20 => editer le profil et les options de l'association. defaut : webmestres
 * 21 => voir info association. defaut : redacteurs
 * 3x Membres ****************************
 * 30 => editer membres. Ajouter/Supprimer des membres, editer les membres (ajout cotisations, modifications informations). defaut : admin non restreint
 * 31 => voir membres. Lister les membres et voir leur page. defaut : admin non restreint
 * 32 => synchroniser les membres. defaut : admin non restreint
 * 33 => relancer les membres. defaut : admin non restreint
 * 4x Dons *******************************
 * 5x Ventes *****************************
 * 6x Prets ******************************
 * 7x Ressources *************************
 * 8x Activites **************************
 * 9x ************************************
 *
 * @param string $faire
 *   Action demandee (par exemple : voir / modifier / etc.)
 * @param string $type
 *   Objet concerne (par exemple : don / membre / vente / etc.)
 * @param int $id
 * @param int $qui
 *   Auteur (par defaut celui connecte)
 * @param array $opt
 *   Options supplementaires pour l'autorisation
 * @return bool
 * @note
 *   http://programmer.spip.net/La-librairie-autoriser
 *   http://programmer.spip.net/Definir-des-boutons
 *   http://programmer.spip.net/Definir-des-onglets
 *   http://programmer.spip.net/Processus-de-la-fonction-autoriser
 *   http://programmer.spip.net/Creer-ou-surcharger-des
 *
** @{ */

/**
 * Ceux qui peuvent voir les infos de l'association doivent avoir le bouton dans
 * l'espace privé avec l'interface normale
 * defaut : redacteurs.
 * groupe : -.
 */
function autoriser_association_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Ceux qui peuvent voir les infos de l'association doivent avoir le bouton dans
 * l'espace privé avec le plugin navigation prive (bando)
 * defaut : redacteurs.
 * groupe : -.
 */
function autoriser_association_bando_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Autorisation d'editer des membres (ou de creer un membre depuis un auteur spip)
 * defaut : admins non restreints.
 * groupe : -.
 */
function autoriser_associer_adherents_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo' && !$qui['restreint']); // on retourne ok pour tous les admins non restreints
}

/**
 * Gestion des autorisations.
 * defaut : webmestres.
 * groupe : 01.
 */
function autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return true; // on retourne ok pour tous les webmestres
	}
	return is_in_groups($qui['id_auteur'], 1); // c'est le groupe 1 qui liste les gens qui peuvent toucher aux autorisations
}

/**
 * Edition des groupes.
 * defaut : admins non-restreints.
 * groupe : 01,02.
 */
function autoriser_association_editer_groupes_dist($faire, $type, $id, $qui, $opt) {
	if ($id!=0 && $id<100) { // on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); // si l'id est-il inferieur a 100 (groupe d'autorisations) et different de 0 (creation d'un groupe) : on retourne le resultat de la fonction d'autorisation de gestion des autorisations
	}
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true; // on retourne ok pour tous les admins non restreints
	}
	return is_in_groups($qui['id_auteur'], array(1,2)); // c'est le groupe 2 qui a le pouvoir de modifier les groupes (mais pas les autorisations), le groupe 1 peut modifier tout ce qui touche aux groupes (ceux d'autorisations inclus)
}

/**
 * Voir les groupes.
 * defaut : redacteurs.
 * groupe : 03.
 */
function autoriser_association_voir_groupes_dist($faire, $type, $id, $qui, $opt) {
	if ($id!=0 && $id<100) { // on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); // si l'id est-il inferieur a 100 (groupe d'autorisations) et different de 0 (creation d'un groupe) : on retourne le resultat de la fonction d'autorisation de gestion des autorisations
	}
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return true; // on retourne ok pour tous les redacteurs
	}
	return is_in_groups($qui['id_auteur'], array(3)); // c'est le groupe 3 qui a le pouvoir de voir les groupes(mais pas ceux des autorisations)
}

/**
 * Configurer la compatabilite. (expert comptable)
 * defaut : webmestre
 * groupe : 10
 */
function autoriser_association_configurer_compta_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return true; // on retourne ok pour tous les webmestres
	}
	return is_in_groups($qui['id_auteur'], array(10)); // c'est le groupe 10 qui a le pouvoir de configurer la comptabilite
}

/**
 * Modifier le profil de l'association.
 * defaut : webmestre
 * groupe : 20
 */
function autoriser_association_editer_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return true; // on retourne ok pour tous les webmestres
	}
	return is_in_groups($qui['id_auteur'], array(20,10)); // c'est le groupe 20 qui a le pouvoir d'editer le profil de l'association. le groupe 10 peut avoir besoin d'acceder a certains options de configuration aussi.
}

/**
 * Voir le profil de l'association.
 * defaut : rédacteurs.
 * groupe : 20,21.
 */
function autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return true; // on retourne ok pour tous les redacteurs
	}
	return is_in_groups($qui['id_auteur'], array(21,20)); // c'est le groupe 21 qui a le pouvoir de voir les informations de l'association, le 20 celui de les editer aussi
}

/**
 * Editer les informations des membres.
 * defaut : admin non restreint.
 * groupe : 30
 */
function autoriser_association_editer_membres($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true; // retourne ok pour les admins complets
	}
	return is_in_groups($qui['id_auteur'], array(30)); // c'est le groupe 30 qui a le pouvoir de modifier les infos de tout le monde
}

/**
 * Voir la page des membres et leurs pages personnelle.
 * defaut : admin non restreint.
 *   Pour les pages de membres, le membre en question y a forcement accès
 * groupe : 30,31.
 */
function autoriser_association_voir_membres($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true; // ok pour les admins non restreints
	}
	if ($id == intval($GLOBALS['visiteur_session']['id_auteur'])) {
		return true; // ok si l'auteur connecté correspond à la page du membre a visiter
	}
	return is_in_groups($qui['id_auteur'], array(31,30)); // c'est le groupe 31 qui a le pouvoir de voir les infos de tout le monde, le 30 celui de les editer
}

/**
 * Voir la page des membres et leurs pages personnelle.
 * defaut : admin non restreint.
 *   Pour les pages de membres, le membre en question y a forcement accès
 * groupe : 31,32.
 */
function autoriser_association_synchroniser_membres($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return true; // ok pour les admins non restreints
	}
	if ($id == intval($GLOBALS['visiteur_session']['id_auteur'])) {
		return true; // ok si l'auteur connecté correspond à la page du membre a visiter
	}
	return is_in_groups($qui['id_auteur'], array(31,30)); // c'est le groupe 31 qui a le pouvoir de voir les infos de tout le monde, le 32 celui de les synchroniser
}

?>