<?php
/**
 * Définit les autorisations du plugin Pensebetes
 *
 * @plugin     Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Pensebetes\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function pensebetes_autoriser() {
}


function autoriser_associerpensebetes_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Fonctions relatives aux menus de l'interface privée
 *
 */

/**
 * Autorisation de voir un élément de menu (pensebetes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebetes_menu_dist($faire, $type, $id, $qui, $opt) {
	 return true;
}


/**
 * Autorisation de voir le bouton d'accès rapide de création (pensebete)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebetecreer_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de voir le bouton d'outil collaboratif du mur
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_murs_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Fonctions relatives à l'Objet pensebete
 *
 */

/**
 * Autorisation de créer (pensebete)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebete_creer_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de voir (pensebete)
 *
 * On ne peut voir  les pensebetes dont on est l'auteur ou le destinataire.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebete_voir_dist($faire, $type, $id, $qui, $opt) {
	if (!$id_auteur=intval($qui['id_auteur'])) {
		return false;
	}
	if (!$id_donneur=intval($opt['id_donneur']) AND !$id_receveur=intval($opt['id_receveur'])) {
		$row = sql_fetsel('id_donneur,id_receveur', 'spip_pensebetes', 'id_pensebete=' . intval($id));
		if (!$row)
			return false;
		$id_donneur = $row['id_donneur'];$id_receveur = $row['id_receveur'];
	}

	if (in_array($id_auteur, array($id_donneur,$id_receveur))) {
		return true;
	}
	// on peut tout faire si on est admin mais c'est pas cool
	if ($qui['statut'] == '0minirezo') {
		return true;
	}

	return false;
}

/**
 * Autorisation de modifier (pensebete)
 *
 * On ne peut modifier que les pensebetes dont on est l'auteur.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebete_modifier_dist($faire, $type, $id, $qui, $opt) {
	if (!$id_auteur=intval($qui['id_auteur'])) {
		return false;
	}
	if (!$id_donneur=intval($opt['id_donneur'])) {
		$id_donneur = sql_getfetsel('id_donneur', 'spip_pensebetes', 'id_pensebete=' . intval($id));
		if (!$id_donneur)
		return false;
	}

	if ($id_auteur==$id_donneur) {
		return true;
	}
	// on peut tout faire si on est admin mais c'est pas cool
	if ($qui['statut'] == '0minirezo') {
		return true;
	}

	return false;
}

/**
 * Autorisation de supprimer (pensebete)
 *
 * On ne peut supprimer tous les pensebetes que l'on peut voir.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pensebete_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_pensebete_voir_dist($faire, $type, $id, $qui, $opt);
}

