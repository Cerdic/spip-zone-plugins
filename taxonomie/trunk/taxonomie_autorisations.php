<?php
/**
 * Définit les autorisations du plugin Taxonomie
 *
 * @package    SPIP\TAXONOMIE\TAXON
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline.
 *
 * @pipeline autoriser
**/
function taxonomie_autoriser(){}


// -----------------
// Objet taxons

/**
 * Autorisation de créer un taxon : il faut être au moins rédacteur.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

/**
 * Autorisation de modifier un taxon : il faut pouvoir en créer un et que l'id soit précisé.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_modifier_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;
	if (intval($id)) {
		$autoriser = autoriser('creer', 'taxon', $id, $qui, $opt);
	}

	return $autoriser;
}

/**
 * Autorisation de supprimer un taxon : aucun taxon ne peut être supprimé individuellement.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return false;
}

/**
 * Autorisation de voir un taxon : tout le monde est autorisé.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation d'iconifier un taxon : seules les espèces et les taxons de rang inférieur possède un logo.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_iconifier_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	if ($id_taxon = intval($id)) {
		// On récupère le champ indiquant si le taxon est une espèce ou pas.
		$where = array("id_taxon=$id_taxon");
		$espece = sql_getfetsel('espece', 'spip_taxons', $where);
		if ($espece == 'oui') {
			$autoriser = true;
		}
	}

	return $autoriser;
}

/**
 * Autorisation de modifier le statut d'un taxon.
 * Cela n'est possible que :
 * - si l'auteur possède l'autorisation de modifier le taxon
 * - et le taxon est une espèce
 * - et que l'espèce est soit une feuille de la hiérarchie soit possède des enfants dont aucun n'est au statut
 *   publié.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 *
 * @return
 *        `true`si autorisé, `false`sinon.
 */
function autoriser_taxon_instituer_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	if ($id_taxon = intval($id)) {
		// On récupère les informations sur le taxon concerné et en particulier si celui-ci est bien une espèce
		// ou un descendant d'espèce.
		$from = 'spip_taxons';
		$where = array("id_taxon=$id_taxon");
		$select = array('espece', 'statut', 'tsn');
		$taxon = sql_fetsel($select, $from, $where);

		if (($taxon['espece'] == 'oui') and  autoriser('modifier', 'taxon', $id_taxon, $qui, $opt)) {
			// On vérifie que l'espèce ne possède pas des descendants directs
			$where = array('tsn_parent=' . intval($taxon['tsn']));
			$select = array('statut');
			$enfants = sql_allfetsel($select, $from, $where);
			if (!$enfants) {
				// Si le taxon est une feuille de la hiérarchie alors il peut toujours être institué.
				$autoriser = true;
			} else {
				// Le taxon a des descendants.
				// - si un descendants est publié alors l'espèce concernée l'est aussi et ne peut pas être
				//   instituée (prop ou poubelle) sous peine de casser la hiérarchie.
				// - si aucun descendant n'est publié alors quelque soit le statut de l'espèce concernée celle-ci
				//   peut être instituée.
				if (!in_array('publie', array_column($enfants, 'statut'))) {
					$autoriser = true;
				}
			}
		}
	}

	return $autoriser;
}


/**
 * Autorisation de voir la liste des taxons : tout le monde est autorisé.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxons_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Autorisation sur l'entrée de menu affichant la liste des taxons : même autorisation que
 * `voir_taxons`, c'est-à-dire, tout le monde.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxons_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return
		autoriser('voir', '_taxons', $id, $qui, $opt);
}

// -----------------
// Objet especes


/**
 * Autorisation de voir un élément de menu (especes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_especes_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
}


/**
 * Autorisation de voir le bouton d'accès rapide de création d'une espèce qui est un taxon
 * de la table `spip_taxons` dont l'indicateur `espece` est à 'oui'.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espececreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'espece', '', $qui, $opt);
}

/**
 * Autorisation de créer (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espece_creer_dist($faire, $type, $id, $qui, $opt) {

	// On vérifie qu'un règne est bien déjà chargé
	include_spip('inc/taxonomie');
	$regnes = regne_lister();
	$regne_existe = false;
	foreach ($regnes as $_regne) {
		if (regne_existe($_regne, $meta_regne)) {
			$regne_existe = true;
		}
	}

	// Il faut aussi être admin ou rédacteur.
	$autoriser = $regne_existe and autoriser('creer', 'taxon', $id, $qui, $opt);

	return $autoriser;
}
