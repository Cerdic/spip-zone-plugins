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

function action_synchroniser_asso_groupe_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$grp = association_recuperer_entier('id_groupe');
	$zar = sql_getfetsel('id_zone', 'spip_asso_groupes', "id_groupe=$grp");
	$log = array(0); // temoin de la synchro : l'indice 0 est le nombre de succes...
	$desc_table = charger_fonction('trouver_table', 'base');
	if ( $desc_table('spip_zones_auteurs') ) { // SPIP2
		$ztl = 'spip_zones_auteurs';
	} elseif ( $desc_table('spip_zones_liens') ) { // SPIP3
		$ztl = 'spip_zones_liens';
	} else { // ??
		$ztl = 'spip_zones_liens';
	}
	switch ( _request('dir2cp') ) { // direction de la synchro
		case 'imp' : // import: zones_% -> asso_fonctions
			$imp = association_recuperer_liste('imp', TRUE); // tableau de la liste des statuts
			if ( count($imp) ) // restreindre selon auteurs.statut specifie(s)
				$qaw = sql_in('a.statut', $imp);
			else // tous ...sauf supprimes...
				$qaw = "a.statut<>'5poubelle' ";
			$join = ' AS z INNER JOIN spip_auteurs AS a ON z.id_auteur=a.id_auteur';
			switch ( substr($ztl, 11) ) {
				case 'auteurs': // SPIP 2
					$q = sql_select('id_auteur', $ztl.$join, "z.id_zone=$zar AND $qaw");
					break;
				case 'liens': // SPIP 3
					$q = sql_select('id_objet', $ztl.$join, "z.objet='auteur' AND z.id_zone=$zar AND $qaw");
					break;
			}
			while ( $aut = sql_fetch($q) ) { // inserer un par un (c'est moins performant que d'inserer en lot, mais d'une part on ne plante pas tout si on tente d'inserer en doublon et d'autre part le compte est celui des insertions reussies)
				if ( sql_countsel('spip_asso_membres', 'id_auteur='.$aut['id_auteur']) ) // filtre pour n'inserer que les membres...
				$log[$aut['id_auteur']] = sql_insertq('spip_asso_fonctions', array(
					'id_auteur' => $aut['id_auteur'],
					'id_groupe' => $grp,
				) );
				else // logger quand meme...
					$log[$aut['id_auteur']] = FALSE;
				if ($log[$aut['id_auteur']]) // en cas d'insertion...
					$log[0]++; // ...en tenir le compte
			}
			sql_free($q);
			break;
		case 'exp' : // export: asso_fonctions -> zones_%
			$where = "f.id_groupe=$grp";
			$etat = association_recuperer_liste('etat', TRUE);
			if ( count($etat) ) // restreindre selon asso_membres.statut_interne specifie(s)
				$where .= ' AND '. sql_in('m.statut_interne', $statut);
			$cat = association_recuperer_liste('cat', TRUE);
			if ( count($cat) ) // restreindre selon asso_membres.id_categorie specifie(s)
				$where .= ' AND '. sql_in('m.id_categorie', $cat);
			$join =  ($etat || $cat) ? ' LEFT JOIN spip_asso_membres AS m ON f.id_auteur=m.id_auteur' : ''; // si pas de restriction alors tous
			$q = sql_select('f.id_auteur', 'spip_asso_fonctions AS f'.$join, $where);
			while ( $aut = sql_fetch($q) ) { // inserer un par un (c'est moins performant que d'inserer en lot, mais d'une part on ne plante pas tout si on tente d'inserer en doublon et d'autre part le compte est celui des insertions reussies)
				switch ( substr($ztl, 11) ) {
					case 'auteurs': // SPIP 2
						$log[$aut['id_auteur']] = sql_insertq($ztl, array(
							'id_auteur' => $aut['id_auteur'],
							'id_zone' => $zar,
						) );
						break;
					case 'liens': // SPIP 3
						$log[$aut['id_auteur']] = sql_insertq($ztl, array(
							'id_objet' => $aut['id_auteur'],
							'id_zone' => $zar,
							'objet' => 'auteur',
						) );
						break;
					default: // SPIP X
						break;
				}
				if ($log[$aut['id_auteur']]) // en cas d'insertion...
					$log[0]++; // ...en tenir le compte
			}
			sql_free($q);
			break;
		default: // nop
			break;
	}
	return $log; // debug
#	return count($log[0]); // on retourne le nombre d'insertions faites
}

?>