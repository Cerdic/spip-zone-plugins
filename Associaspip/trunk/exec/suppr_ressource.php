<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_suppr_ressource() {
	$r = association_controle_id('ressource', 'asso_ressources', 'editer_ressources');
	if ($r) {
		list($id_ressource, $ressource) = $r;
		exec_suppr_ressource_args($id_ressource, $ressource);
	}
}

function exec_suppr_ressource_args($id_ressource, $ressource) {
	include_spip ('association_modules');
	echo association_navigation_onglets('titre_onglet_prets', 'ressources');
	// INTRO : resume ressource
	$infos['ressources_libelle_code'] = association_formater_code($ressource['code'], 'spip_asso_ressources');
	$infos['ressources_entete_montant'] = association_formater_prix($ressource['pu'], 'rent');
	$infos['ressources_libelle_unite'] = association_formater_duree(1, $ressource['ud']);
	$infos['ressources_entete_caution'] = association_formater_prix($ressource['prix_caution'], 'guarantee');
	if ( is_numeric($ressource['statut']) ) { // utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires)
		if ($ressource['statut']>0) {
			$puce = 'verte';
			$type = 'ok';
		} elseif ($ressource['statut']<0) {
			$puce = 'orange';
			$type = 'suspendu';
		} else {
			$puce = 'rouge';
			$type = 'reserve';
		}
	} else {
		switch($ressource['statut']) { // utilisation des anciens 4+ statuts textuels (etat de reservation)
			case 'ok':
				$puce = 'verte';
				break;
			case 'reserve':
				$puce = 'rouge';
				break;
			case 'suspendu':
				$puce = 'orange';
				break;
			case 'sorti':
			case '':
			case NULL:
				$puce = 'poubelle';
				break;
		}
		$type = $ressource['statut'];
	}
	$infos['statut'] = '<span class="'.(is_numeric($ressource['statut'])?'quanttity':'availability').'">'. association_formater_puce($ressource['statut'], $puce, "ressources_statut_$type") .'</span>';
	$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), )); // indice de popularite
	echo '<div class="hproduct">'.  association_tablinfos_intro('<span class="n">'.$ressource['intitule'].'</span>', 'ressource', $id_ressource, $infos ) .'</div>';
	// STATS sur la duree et le montant des emprunts
	echo association_tablinfos_stats('prets', 'prets', array('entete_duree'=>'duree','entete_montant'=>'duree*prix_unitaire',), "id_ressource=$id_ressource");
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('titre_onglet_prets', 'grille-24.png', array('ressources', "id=$id_ressource"), array('voir_ressources', 'association') ),
	) );
	debut_cadre_association('pret-24.gif', 'ressources_titre_suppression_ressources');
	echo association_form_suppression('ressource', $id_ressource );
	fin_page_association();
}

?>