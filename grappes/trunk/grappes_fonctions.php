<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 * 
 * Fichier des fonctions du plugin
 * 
 * @package SPIP\Grappes\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'autorisation de base
 */
function autoriser_grappe(){}

/**
 * Autorisation de création de grappe
 * 
 * Retourne la même chose que l'action de modification
 * 
 * @param string $faire
 * 	Action, ici creer
 * @param string $type
 * 	Type de l'objet, ici grappe
 * @param int $id
 * 	Identifiant numérique de l'objet
 * @param array $qui
 * 	La contenu de la session visiteur en cours
 * @param string|array $opt
 * 	Les options
 * @return boolean
 * 	true si autorisé, false sinon
 */
function autoriser_grappe_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

/**
 * Autorisation de modification de grappe
 * 
 * On autorise les admins non restreints dans tous les cas 
 * (création et modification de toutes les grappes), l'id_admin pour la modification
 * d'une grappe particulière
 * 
 * @param string $faire
 * 	Action, ici modifier (mais utilisé également pour creer)
 * @param string $type
 * 	Type de l'objet, ici grappe
 * @param int $id
 * 	Identifiant numérique de l'objet
 * @param array $qui
 * 	La contenu de la session visiteur en cours
 * @param string|array $opt
 * 	Les options
 * @return boolean
 * 	true si autorisé, false sinon
 */
function autoriser_grappe_modifier_dist($faire, $type, $id, $qui, $opt){
	$id_admin = sql_getfetsel('id_admin','spip_grappes','id_grappe='.intval($id));
	return ((($qui['statut']=='0minirezo') AND !$qui['restreint']) OR ($qui['id_auteur'] == $id_admin));
}

/**
 * Autorisation d'association d'un objet à une grappe
 * 
 * Vérifie la configuration de la grappe.
 * 
 * Si pas de configuration spécifique, seuls les administrateurs et l'id_admin peuvent associer un objet
 * 
 * @param string $faire
 * 	Action, ici associer
 * @param string $type
 * 	Type de l'objet, ici grappe
 * @param int $id
 * 	Identifiant numérique de l'objet
 * @param array $qui
 * 	La contenu de la session visiteur en cours
 * @param array $opt
 * 	Les options, ici si cible est passée dans le tableau, on vérifie si ce type d'objet est autorisé
 * @return boolean
 * 	true si autorisé, false sinon
 */
function autoriser_grappe_associer_dist($faire, $type, $id, $qui, $opt){
	$res = sql_fetsel(array('id_admin','liaisons','options'),'spip_grappes','id_grappe='.sql_quote($id));
	if (!is_array($options = @unserialize($res['options'])))
		$acces = array('0minirezo');
	else
		$acces = is_array($options['acces'])?$options['acces']:array('0minirezo');

	// tester le statut de l'auteur
	if (!in_array($qui['statut'],$acces) OR ($res['id_admin'] != $qui['id_auteur']))
		return false;

	// tester si l'on a le droit d'ajouter cet objet
	if ($opt['cible']) {
		$liaisons = explode(',',$res['liaisons']);
		if (!in_array(table_objet($opt['cible']),$liaisons))
			return false;
	}

	return true;
}

?>
