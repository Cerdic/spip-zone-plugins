<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_synchroniser_asso_groupe() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$grp = association_recuperer_entier('id_groupe');
	$zar = sql_getfetsel('id_zone', 'spip_asso_groupes', "id_groupe=$grp");
	$laa = array(); // Liste des id_Auteurs rAjoutes
	$dir = _request('dir'); // direction de la synchro
	$desc_table = charger_fonction('trouver_table', 'base');
	if ( $desc_table('spip_zones_auteurs') ) { // SPIP2
		$ztl = 'spip_zones_auteurs';
	} elseif ( $desc_table('spip_zones_liens') ) { // SPIP3
		$ztl = 'spip_zones_liens';
	} else { // ??
		$ztl = 'spip_zones_liens';
	}
	switch ($dir) {
		case 'imp' : // zone -> groupe
			switch ($ztl) {
				case 'spip_zones_auteurs': // SPIP 2
					$q = sql_select('id_auteur', $zlt, "id_zone=$zar");
					break;
				case 'spip_zones_liens': // SPIP 3
					$q = sql_select('id_objet', $zlt, "objet='auteur' AND id_zone=$zar");
					break;
			}
			while ( $aut = sql_fetch($q) )
				if ( sql_countsel('spip_asso_membres', 'id_auteur='.$aut['id_auteur']) ) // filtre pour n'inserer que les membres
				$laa[] = sql_insertq('spip_asso_fonctions', array(
					'id_auteur' => $aut['id_auteur'],
					'id_groupe' => $grp,
				) );
			break;
		case 'exp' : // groupe -> zone
			$q = sql_select('id_auteur', 'spip_asso_fonctions', "id_groupe=$grp");
			while ( $aut = sql_fetch($q) )
				switch ($ztl) {
					case 'spip_zones_auteurs': // SPIP 2
						$laa[] = sql_insertq($ztl, array(
							'id_auteur' => $aut['id_auteur'],
							'id_zone' => $zar,
						) );
						break;
					case 'spip_zones_liens': // SPIP 3
						$laa[] = sql_insertq($ztl, array(
							'id_objet' => $aut['id_auteur'],
							'id_zone' => $zar,
							'objet' => 'auteur',
						) );
						break;
				}
			break;
	}
	return count($laa); // on retourne le nombre de membres inseres dans la table
}

?>