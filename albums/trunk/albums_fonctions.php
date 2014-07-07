<?php
/**
 * Fonctions du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * critère `{orphelins}`
 *
 * Sélectionne les albums sans lien avec un objet éditorial
 *
 * @critere
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not?"":"NOT";

	$select = sql_get_select("DISTINCT id_album","spip_albums_liens as oooo");
	$where = "'" .$boucle->id_table.".id_album $not IN ($select)'";
	if ($cond){
		$_quoi = '@$Pile[0]["orphelins"]';
		$where = "($_quoi) ? $where : ''";
	}

	$boucle->where[]= $where;
}

?>
