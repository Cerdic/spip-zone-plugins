<?php
/**
 * Fonctions du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Un fitre que l'on peut utiliser en argument d'une inclusion si on veut etre sur que l'affichage change quand la commande change
 * <INCLURE{fond=macommande,id_commande,hash=#ID_COMMANDE|commandes_hash} />
 *
 * @param int $id_commande
 * @return string
 */
function commandes_hash($id_commande){
	return md5(serialize(sql_allfetsel("id_commandes_detail,prix_unitaire_ht,taxe,objet,id_objet,quantite","spip_commandes_details","id_commande=".intval($id_commande))));
}

/**
 * Retourne les différents statuts possibles pour une commande ou le nom d'un statut précis
 *
 * @filtre
 *
 * @param bool|string $statut
 *     - vide ou false pour retourner un tableau
 *     - nom d'un statut précis pour retourner sa chaîne de langue
 * @return array|string
 *     - array: tableau associatif des statuts possibles et leurs chaînes de langue
 *     - string: chaîne de langue d'un statut
**/
function commandes_lister_statuts($statut=false){

	// retourne les statuts déclarés dans declarer_tables_objets_sql
	if (!function_exists('objet_info'))
		include_spip('inc/filtres');
	$statuts =  array_map('_T',objet_info('commande','statut_textes_instituer'));

	if ($statut and $nom = $statuts[$statut])
		return $nom;
	if ($statut) return $statut;
	else
		return $statuts;
}

/** 
 * Retourne l'identifiant du premier webmestre
 *
 * @return int|bool
 *     identifiant du premier webmestre
 *     false sinon (improbable...)
**/
function commandes_id_premier_webmestre(){
	$id_webmestre = sql_getfetsel('id_auteur', table_objet_sql('auteur'), "statut='0minirezo' AND webmestre='oui'");
	if ($id_webmestre = intval($id_webmestre))
		return $id_webmestre;
	else
		return false;
}

/**
 * Générer l'URL correspondant à la facture d'une commande
 *
 * @param int $id_commande
 * 		Identifiant de la commande
 * @return string
 * 		Retourne l'URL d'une page contenant la facture, ou rien si on n'en veut pas
 */
function filtre_generer_url_commande_facture_dist($id_commande) {
	return generer_url_public('facture', 'id_commande='.intval($id_commande));
}



