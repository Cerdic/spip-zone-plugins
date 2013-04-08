<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @note
 *   http://programmer.spip.net/autoriser
 */
function association_autoriser() {
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
function is_in_groups($id_auteur, $id_groupes) {
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
		return TRUE;
	} else {
		return false;
	}
}

/*****************************************
 * @defgroup autoriser_associer
 *
 * Groupes d'acces
 * Les autorisation d'acces sont geres par des groupes de membres (d'auteur SPIP
 * en fait, un auteur non membre peut avoir un access, par exemple le personnel
 * administratif de l'asso) Qu'ils soient ou on present dans le groupe, les
 * webmestres ont tous les acces ; les admins non restreints certains.
 *
 * Mapping des id_groupe (xy<100) et des autorisations associées :
 *
 * 0y Groupes ****************************
 * 01 => edition des groupes y compris ceux d'autorisation. defaut: webmestres
 * 02 => edition des groupes d'id>100. defaut : admin non restreints
 * 03 => voir les groupes d'id>100. defaut : redacteurs
 * 1y Comptabilite ***********************
 * 10 => expert-comptable : toutes les autorisations sur la compta.
 * 11 => comptable/tresorier : peut enregistrer des ecritures mais pas modifier le plan comptable
 * 12 => auditeur : acces en lecture seule a la comptabilite
 * 3y Membres ****************************
 * 30 => editer membres. Ajouter/Supprimer des membres, editer les membres (ajout cotisations, modifications informations). defaut : admin non restreint
 * 31 => voir membres. Lister les membres et voir leur page. defaut : admin non restreint
 * 32 => synchroniser les membres. defaut : admin non restreint
 * 33 => relancer les membres. defaut : admin non restreint
 *
 * @param string $faire
 *   Action demandee (mapping id_goupe xy<100) :
 *   - x0 = gerer_ : droits complets sur le module,
 *     dont configuration et synchronisations.
 *   - x1|x4|x7 = editer_ : editer (ajouter, modifier, supprimer) + lire toutes les infos (voir details, exporter, lister).
 *  - x2|x5|x8 = export_ : lire tout (voir details exporter, lister).
 *    les exports necessitent de pouvoir voir tous les details...
 *  - x3|x6|x9 = list_ : lister (voir seulement champs affiches)
 *    les listes n'affichent qu'un certain nombre d'informations
 *    (parfois configurables) et d'autres ne sont pas accessibles (par defaut)
 * @param string $type
 *   Type d'bjet concerne (mapping id_goupe xy<100) :
 *   - 0y = _groupes : Autorisations ; Groupes utilisateurs
 *   - 1y = _compta : Comptabilite
 *   - 2y = _profil : Profil Association
 *   - 3y = _membres : Membres
 *   - 4y = _dons : Dons
 *   - 5y = _ventes : Ventes
 *   - 6y = _ressources|_prets : Ressources (locations et prets)
 *   - 7y = _activites : Inscriptions aux activites
 *   - 8y : reserve pour un usage futur
 *   - 9y : reserve pour un usage futur
 * @param int $id
 *   ID de l'objet concerne
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

/// 0 /// groupes d'autorisations (id_groupe<100) et groupes propres a l'association

/**
 * Gestion des autorisations.
 * defaut : webmestres.
 * groupe : 01.
 */
function autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE; // on retourne ok pour tous les webmestres
	}
	return is_in_groups($qui['id_auteur'], 1); // c'est le groupe 1 qui liste les gens qui peuvent toucher aux autorisations
}

/**
 * Edition des groupes.
 * defaut : admins non-restreints.
 * groupes : 01,02.
 */
function autoriser_association_editer_groupes_dist($faire, $type, $id, $qui, $opt) {
	if ($id!=0 && $id<100) { // on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); // si l'id est-il inferieur a 100 (groupe d'autorisations) et different de 0 (creation d'un groupe) : on retourne le resultat de la fonction d'autorisation de gestion des autorisations
	}
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(2,1));
}

/**
 * Voir les groupes.
 * defaut : redacteurs.
 * groupes : 01,02,03.
 */
function autoriser_association_voir_groupes_dist($faire, $type, $id, $qui, $opt) {
	if ($id!=0 && $id<100) { // on n'arrive jamais ici sauf si quelqu'un entre des urls a la main ce qui peut arriver
		return autoriser_association_gerer_autorisations_dist($faire, $type, $id, $qui, $opt); // si l'id est-il inferieur a 100 (groupe d'autorisations) et different de 0 (creation d'un groupe) : on retourne le resultat de la fonction d'autorisation de gestion des autorisations
	}
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(3,2,1));
}

/// 1 /// comptabilite (module des comptes et modules dependant...)

/**
 * Droits complets (configuration et gestion) sur la compatabilite. (expert comptable)
 * defaut : webmestre
 * groupe : 10.
 */
function autoriser_association_gerer_compta_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], 10);
}

/**
 * Edition et exporter des operations comptables. (comptable/tresorier)
 * Cela inclut normalement l'edition dans d'autres modules si necessaire
 * (la permission adequade incorpore donc ce groupe automatiquemnt)
 * defaut : webmestre
 * groupes : 10,11.
 */
function autoriser_association_editer_compta_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(11,10));
}

/**
 * Consultation elargie de la compatabilite.
 * Cela inclut la consultation et l'export du "Grand Journal/Livre des operations"
 * des etats/syntheses comptables, et des operations liees a la comptabilite
 * (dans les autres modules)...
 * defaut : webmestre
 * groupes : 10,11,12.
 */
function autoriser_association_exporter_compta_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(12,11,10));
}

/**
 * Consulter la compatabilite. (auditeur comptable et autres membres du CA)
 * Cette consultation (lecture seule) est celle du "Grand Journal/Livre des operations"
 * ainsi que la consultation et l'export des etats/syntheses comptables !
 * defaut : webmestre
 * groupes : 10,11,12,13.
 */
function autoriser_association_voir_compta_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(13,12,11,10));
}

/// 2 /// profil de l'association (page d'accueil et configuration du plugin)

/**
 * Ceux qui peuvent voir les infos de l'association doivent avoir le bouton dans
 * l'espace privé avec l'interface normale
 * defaut : redacteurs.
 * groupes : 10,20,21,23.
 */
function autoriser_association_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Ceux qui peuvent voir les infos de l'association doivent avoir le bouton dans
 * l'espace privé avec le plugin navigation prive (bando)
 * defaut : redacteurs.
 * groupes : 10,20,21,23.
 */
function autoriser_association_bando_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Modifier le profil de l'association.
 * defaut : webmestre
 * groupes : 10,20,21.
 */
function autoriser_association_editer_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && $qui['webmestre']=='oui') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(21,20,10));
}

/**
 * Voir le profil de l'association.
 * defaut : rédacteurs.
 * groupes : 20,21,22,23.
 */
function autoriser_association_voir_profil_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(23,22,21,20,13,12,11,10));
}

/// 3 /// membres de l'association

/**
 * Creer un membre depuis un auteur spip
 * defaut : admins non restreints.
 * groupe : aucun.
 */
function autoriser_associer_adherents_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_association_gerer_membres_dist($faire, $type, $id, $qui, $opt);
}

/// Un membre peut declarer lui-meme sa cotisation
/// si la destination et l'imputation des cotisations sont uniques.
/// Le tresorier n'aura plus qu'a valider.
function autoriser_association_ajouter_cotisation_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint'])
		return true;
	if ($id != $GLOBALS['visiteur_session']['id_auteur'])
		return false;
	if (intval($GLOBALS['association_metas']['destinations'])>1)
		return false;
	return (sql_countsel('spip_asso_plan', "(active=1) AND (classe=" . sql_quote($GLOBALS['association_metas']['classe_banques']) . ") AND (code!=" . sql_quote($GLOBALS['association_metas']['pc_intravirements']) . ')') == 1);
}

/**
 * Synchroniser les membres avec les auteurs SPIP.
 * defaut : admin non restreint.
 * groupe : 30.
 */
function autoriser_association_gerer_membres_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], 30);
}

/**
 * Editer les informations des membres.
 * defaut : admin non restreint.
 * groupe : 10,11,30,31.
 */
function autoriser_association_editer_membres_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(31,30,11,10));
}

/**
 * Voir et exporter les informations des membres.
 * defaut : admin non restreint.
 * groupes : 11,30,31,32.
 */
function autoriser_association_exporter_membres_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(32,31,30,11));
}

/**
 * Voir la page des membres et leurs pages personnelle.
 * defaut : admin non restreint et membre en question.
 * groupes : 10,11,12,30,31,32,33.
 */
function autoriser_association_voir_membres_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	if ($id == intval($GLOBALS['visiteur_session']['id_auteur'])) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(33,32,31,30,12,11,10));
}

/**
 * Publiposter des messages (de rappels) aux membres.
 * defaut : admin non restreint.
 * groupes : 11,30,32,35.
 */
function autoriser_association_relancer_membres_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(35,32,30,11)); // c'est le groupe 35 qui a le pouvoir de relancer les membres, le 32 celui de generer les listes et etiquettes... (est-ce que les relances font parti des messages envoyes par le secretaire ?), le 11 celui de reclamer les cotisations... (mais dans certaines associations il ne s'occupe pas de cet aspect administratif qui est laisse a la charge du secretaire avec qui il collabore sur ce point)
}

/**
 * Recevoir un recu fiscal
 * defaut : admin non restreint et le genereux donateur pour lui meme
 * groupe : aucun.
 */
function autoriser_fiscaliser_membres($faire, $type, $id, $qui, $opt) {
  return (($qui['statut']=='0minirezo' && !$qui['restreint']) || ($id==$GLOBALS['visiteur_session']['id_auteur']));
}

/// 4 /// dons a l'association

/**
 * Droits complets sur le module des dons
 * defaut : admin non restreint.
 * groupes : 10,11,40.
 */
function autoriser_association_gerer_dons_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(40,11,10));
}

/**
 * Editer les donations.
 * groupes : 10,11,40,41.
 * sinon, comme les cotisations
 */
function autoriser_association_editer_dons_dist($faire, $type, $id, $qui, $opt) {
	if (is_in_groups($qui['id_auteur'], array(41,40,11,10)))
		return TRUE;
	return autoriser_association_ajouter_cotisation_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Exporter les donations.
 * defaut : admin non restreint.
 * groupes : 10,11,40,41,42.
 */
function autoriser_association_exporter_dons_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(42,41,40,11,10));
}

/**
 * Lister les donations.
 * defaut : admin non restreint.
 * groupes : 10,11,12,40,41,42,43.
 */
function autoriser_association_voir_dons_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(43,42,41,40,12,11,10));
}

/// 5 /// ventes de l'association

/**
 * Droits complets sur le module des ventes.
 * defaut : admin non restreint.
 * groupes : 10,11,50.
 */
function autoriser_association_gerer_ventes_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(50,11,10));
}

/**
 * Editer les ventes.
 * defaut : admin non restreint.
 * groupes : 10,11,50,51.
 */
function autoriser_association_editer_ventes_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(51,50,11,10));
}

/**
 * Exporter les ventes.
 * defaut : admin non restreint.
 * groupes : 10,11,50,51,52.
 */
function autoriser_association_exporter_ventes_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(52,51,50,11,10));
}

/**
 * Lister les ventes.
 * defaut : admin non restreint.
 * groupes : 10,11,12,50,51,52,53.
 */
function autoriser_association_voir_ventes_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(53,52,51,50,12,11,10));
}

/// 6 /// ressources associatives a disposition (prets ou locations)

/**
 * Droits complets sur le module des ressources.
 * defaut : admin non restreint.
 * groupes : 10,11,60.
 */
function autoriser_association_gerer_ressources_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(60,11,10));
}

/**
 * Editer les ressources.
 * defaut : admin non restreint.
 * groupes : 10,11,60,61.
 */
function autoriser_association_editer_ressources_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(61,60,11,10));
}

/**
 * Exporter les ressources.
 * defaut : admin non restreint.
 * groupes : 10,11,60,61,62.
 */
function autoriser_association_exporter_ressources_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(62,61,60,11,10));
}

/**
 * Lister les ressources.
 * defaut : redacteurs.
 * groupes : 10,11,12,50,51,52,53.
 */
function autoriser_association_voir_ressources_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(63,62,61,60,12,11,10));
}

/**
 * Editer les prets/locations.
 * defaut : admin non restreint.
 * groupes : 10,11,60,61,64.
 */
function autoriser_association_editer_prets_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(64,61,60,11,10));
}

/**
 * Exporter les prets/locations.
 * defaut : admin non restreint.
 * groupes : 10,11,60,61,62,64,65.
 */
function autoriser_association_exporter_prets_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(65,64,62,61,60,11,10));
}

/**
 * Lister les prets/locations.
 * defaut : admins non restreints.
 * groupes : 10,11,12,60,61,62,63,64,65,66.
 */
function autoriser_association_voir_prets_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(66,65,64,63,62,61,60,12,11,10));
}

/// 7 /// participations aux activites de l'association

/**
 * Droits complets sur le module des activites.
 * defaut : admin non restreint.
 * groupes : 10,11,70.
 */
function autoriser_association_gerer_activites_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(70,11,10));
}

/**
 * Lister les activites.
 * defaut : redacteurs.
 * groupes : 10,11,12,70,71,72,73.
 */
function autoriser_association_voir_activites_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' || $qui['statut']=='1comite') {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(73,72,71,70,12,11,10));
}

/**
 * Editer les inscriptions aux activites.
 * defaut : admin non restreint.
 * groupes : 10,11,70,71,74.
 */
function autoriser_association_editer_inscriptions_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(74,71,70,11,10));
}

/**
 * Exporter les inscriptions aux activites.
 * defaut : admin non restreint.
 * groupes : 10,11,60,71,72,74,75.
 */
function autoriser_association_exporter_inscriptions_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut']=='0minirezo' && !$qui['restreint']) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(75,74,72,71,70,11,10));
}

/**
 * Lister les inscripts aux activites.
 * defaut : admins non restreints et membres inscrits a l'activite.
 * groupes : 10,11,12,70,71,72,73,74,75,76.
 */
function autoriser_association_voir_inscriptions_dist($faire, $type, $id, $qui, $opt) {
	if ( ($qui['statut']=='0minirezo' && !$qui['restreint']) OR sql_getfetsel('*', 'spip_asso_activites', 'id_evenement='.intval($id).' AND id_auteur='.intval($qui['id_auteur'])) ) {
		return TRUE;
	}
	return is_in_groups($qui['id_auteur'], array(76,75,74,73,72,71,70,12,11,10));
}

/// 8 /// reserve

/// 9 /// reserve


?>