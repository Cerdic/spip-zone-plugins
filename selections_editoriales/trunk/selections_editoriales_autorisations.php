<?php
/**
 * Définit les autorisations du plugin Sélections éditoriales
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function selections_editoriales_autoriser(){}


// -----------------
// Objet selections


/**
 * Autorisation de voir un élément de menu (selections)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selections_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de voir le bouton d'accès rapide de création (selection)
 * - pouvoir créer une sélection
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selectioncreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'selection', '', $qui, $opt);
} 

/**
 * Autorisation de créer (selection)
 * - au moins rédacteur
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selection_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] <= '1comite'; 
}

/**
 * Autorisation de voir (selection)
 * - tout le monde
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selection_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (selection)
 * - être admin complet
 * - ou faire partie des auteurs liés
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selection_modifier_dist($faire, $type, $id, $qui, $opt) {
	$ok = (
		($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or ($auteurs = selections_auteurs_objet('selection', intval($id)) and in_array($qui['id_auteur'], $auteurs))
	);
	
	return $ok;
}

/**
 * Autorisation de supprimer (selection)
 * - pouvoir modifier la sélection
 * - et qu'elle soit vide de contenus
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selection_supprimer_dist($faire, $type, $id, $qui, $opt) {
	$ok = (
		autoriser('modifier', $type, $id, $qui, $opt)
		and !sql_countsel('spip_selections_contenus', 'id_selection = '.intval($id))
	);
	
	return $ok;
}

/**
 * Autorisation de créer des contenus dans une sélection
 * - pouvoir modifier la sélection
 * - ne pas dépasser le nombre limite de contenu s'il existe
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selection_creerselectionscontenudans_dist($faire, $type, $id, $qui, $opt) {
	$id_selection = intval($id);
	
	$ok = (
		autoriser('modifier', $type, $id, $qui, $opt)
		and (
			!$limite = intval(sql_getfetsel('limite', 'spip_selections', 'id_selection = '.$id_selection))
			or
			$limite > sql_countsel('spip_selections_contenus', 'id_selection = '.$id_selection)
		)
	);
	
	return $ok;
}

/**
 * Autorisation de lier/délier l'élément (selections)
 * - pouvoir modifier l'objet où l'on se trouve
 * - et qu'il fasse partie des objets configurés
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerselections_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	include_spip('base/objets');
	
	$ok = (
		$objets = lire_config('selections_editoriales/objets')
		and is_array($objets)
		and in_array(table_objet_sql($type), $objets)
		and autoriser('modifier', $type, $id, $qui, $opt)
	);
	
	return $ok;
}


// -----------------
// Objet selections_contenus


/**
 * Autorisation de créer (selectionscontenu)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selectionscontenu_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

/**
 * Autorisation de voir (selectionscontenu)
 * - tout le monde
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selectionscontenu_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (selectionscontenu)
 * - pouvoir modifier la sélection parente
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selectionscontenu_modifier_dist($faire, $type, $id, $qui, $opt) {
	$ok = (
		(
			(isset($opt['id_selection']) and $id_selection = intval($opt['id_selection']))
			or $id_selection = sql_getfetsel('id_selection', 'spip_selections_contenus', 'id_selections_contenu = ' . intval($id))
		)
		and autoriser('modifier', 'selection', $id_selection, $qui, $opt)
	);
	
	return $ok;
}

/**
 * Autorisation de supprimer (selectionscontenu)
 * - pouvoir modifier le contenu
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_selectionscontenu_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

/**
 * Lister les auteurs d'un objet
 *
 * @param string $objet Type de l'objet
 * @param int $id_objet Identifiant de l'objet
 * @param string $cond='' Condition supplémentaire
 * @return array Retourne une liste d'identifiant d'auteurs
 */
function selections_auteurs_objet($objet, $id_objet, $cond='') {
	return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='$objet' AND id_objet=".sql_quote($id_objet). ($cond ? " AND $cond" : ''));
}

