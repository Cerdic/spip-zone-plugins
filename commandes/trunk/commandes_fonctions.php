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
	$statuts =  array(
		'encours'        => _T('commandes:statut_encours'),
		'erreur'         => _T('commandes:statut_erreur'),
		'attente'        => _T('commandes:statut_attente'),
		'partiel'        => _T('commandes:statut_partiel'),
		'paye'           => _T('commandes:statut_paye'),
		'envoye'         => _T('commandes:statut_envoye'),
		'retour'         => _T('commandes:statut_retour'),
		'retour_partiel' => _T('commandes:statut_retour_partiel'),
	);

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

/**
 * Fonction qui retourne les différents modes de paiement possibles
 *
 * @param bool|string $mode
 *     vide ou false pour retourner un tableau
 *     nom d'un mode précis pour retourner sa chaîne de langue
 * @return array|string
 *     array: tableau associatif des modes possibles et leurs chaînes de langue
 *     string: chaîne de langue d'un statut
 */
function commandes_lister_modes_paiement_dist($mode=false){
	$modes =  array(
		'espece'   => _T('commandes:paiement_espece'),
		'cheque'   => _T('commandes:paiement_cheque'),
		'virement' => _T('commandes:paiement_virement'),
		'cb'       => _T('commandes:paiement_cb'),
		'paypal'   => _T('commandes:paiement_paypal'),
		'bitcoin'  => _T('commandes:paiement_bitcoin'),
		'autre'    => _T('commandes:paiement_autre')
	);

	if ($mode and $nom = $modes[$mode])
		return $nom;
	if ($mode) return $mode;
	else
		return $modes;
}


/**
 * Filtre pour retourner la chaîne de langue correspondant à un mode de paiement 
 *
 * @param string $mode
 *     nom d'un mode précis pour retourner sa chaîne de langue
 * @return string
 *     string: chaîne de langue d'un statut
 */
function filtre_nom_mode_paiement($mode=false){
	return commandes_lister_modes_paiement_dist($mode);
}

?>
