<?php
/**
 * Plugin auteurs_syndic
 * par kent1
 * Les autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function auteurs_syndic_autoriser(){}

/**
 * 
 * Autoriser a modifier un site
 * Voir l'original également http://code.spip.net/@autoriser_site_modifier_dist
 * 
 * @param unknown_type $faire
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opt
 */
function autoriser_site_modifier($faire, $type, $id, $qui, $opt) {
	/**
	 * Si on est administrateur on peut le modifier
	 */
	if ($qui['statut'] == '0minirezo' AND !$qui['restreint'])
		return true;

	$t = sql_fetsel("id_rubrique,statut", "spip_syndic", "id_syndic=".sql_quote($id));
	$auteur = sql_getfetsel("id_auteur", "spip_auteurs_syndic", "id_syndic=".sql_quote($id)." AND id_auteur=".$qui['id_auteur']);
	
	return (($t
		AND autoriser('voir','rubrique',$t['id_rubrique'])
		AND autoriser('modifier', 'rubrique', $t['id_rubrique']))
		OR $auteur
	);
}
?>