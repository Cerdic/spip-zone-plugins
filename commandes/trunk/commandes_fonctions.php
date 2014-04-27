<?php
/**
 * Fonctions du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2013
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Une fonction qui retourne les différents statuts possibles pour une commande ou le nom d'un statut précis
 *
 * @param bool|string $statut
 *     vide ou false pour retourner un tableau
 *     nom d'un statut précis pour retourner sa chaîne de langue
 * @return array|string
 *     array: tableau associatif des statuts possibles et leurs chaînes de langue
 *     string: chaîne de langue d'un statut
**/
function commandes_lister_statuts($statut=false){

	// retourne les statuts déclarés dans declarer_tables_objets_sql
	$statuts =  array_map('_T',objet_info('commande','statut_textes_instituer'));

	if ($statut and $nom = $statuts[$statut])
		return $nom;
	if ($statut) return $statut;
	else
		return $statuts;
}

/** 
 * Fonction qui retourne l'identifiant du premier webmester
 *
 * @return int
 *     identifiant du premier webmaster
**/
function commandes_id_premier_webmestre()
{
	include_spip('base/abstract_sql');
	$query = sql_select("id_auteur","spip_auteurs","statut = '0minirezo' AND webmestre = 'oui'","","id_auteur");
	if ($row = sql_fetch($query)) {
		return( $row["id_auteur"] );
	}
	return false;
}


?>
