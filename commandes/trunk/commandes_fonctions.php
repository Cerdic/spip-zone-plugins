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


/**
 * Une fonction pour memoriser les taxes par taux et retourner le tableau de detail en recap
 * @param null|float $prix_ht
 * @param null|float $prix_ttc
 * @return array|string
 */
function commande_totalise_taxes($prix_ht = null, $prix_ttc = null) {
	static $taxes = array();

	if ($prix_ht
		and $prix_ttc
		and $prix_ht = floatval(str_replace(',','.',$prix_ht))
		and $prix_ttc = floatval(str_replace(',','.',$prix_ttc))
		and (floatval($prix_ttc) - floatval($prix_ht))>0.001) {
		$taux = (string)round((floatval($prix_ttc)/floatval($prix_ht) - 1.0) * 100, 1);

		if (!isset($taxes[$taux])) {
			$taxes[$taux] = 0;
		}
		$taxes[$taux] += ($prix_ttc - $prix_ht);
	}

	if (is_null($prix_ht) or !strlen($prix_ht)){
		return $taxes;
	}
	return '';
}


