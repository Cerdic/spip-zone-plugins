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

function exec_association_autorisations() {
	sinon_interdire_acces(autoriser('gerer_autorisations', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$type = _request('type');
	if ($type!=0) { // quand la restriction n'est pas explicitement sur les groupes...
		$type = intval($type); // ...s'assurer qu'on a une valeur numerique (histoire de ne pas planter la requete)
		if (!$type OR $type<0) { // un "0" serait alors qu'on a passe n'importe quoi...
			$type = ''; // ...on n'en tiendra pas compte...
			$active = 1;
		} else {
			$active = 0;
		}
	}
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('gerer_les_autorisations', 'association');
/// AFFICHAGES_LATERAUX : INFOS : notice
	echo _T('asso:aide_gerer_autorisations');
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('association_infos_contacts', 'assoc_qui.png', array('association'), array('voir_profil', 'association') ),
	), 10);
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('annonce.gif', 'les_groupes_dacces');
/// AFFICHAGES_CENTRAUX : FILTRES
	$lt = array(
		0 => 'groupes',
		2 => 'menu2_titre_association',
		3 => 'menu2_titre_gestion_membres',
	);
	if ( $GLOBALS['association_metas']['comptes'] )
		$lt[1] = 'menu2_titre_gestion_comptes';
	if ( $GLOBALS['association_metas']['dons'] )
		$lt[4] = 'menu2_titre_gestion_dons';
	if ( $GLOBALS['association_metas']['comptes'] )
		$lt[5] = 'menu2_titre_gestion_ventes';
	if ( $GLOBALS['association_metas']['ventes'] )
		$lt[6] = 'menu2_titre_gestion_ressources';
	if ( $GLOBALS['association_metas']['activites'] )
		$lt[7] = 'menu2_titre_gestion_activites';
	$filtre_type = "<select name='type' onchange='form.submit()'>\n";
	$filtre_type .= "<optgroup label='-----'>\n";
	foreach ($lt as $k=>$t) {
		$filtre_type .= '<option value="'.$k.'"';
		if ($k==$type)
			$filtre_type .= ' selected="selected"';
		$filtre_type .= '> '. _T('asso:'.$t) ."</option>\n";
	}
	$filtre_type .= "</optgroup>\n<optgroup label='-----'>\n";
	$filtre_type .= '<option value=""';
	if (!$active AND $type=='')
		$filtre_type .= ' selected="selected"';
	$filtre_type .= '> '. _T('asso:entete_tous') ."</option>\n";
	$filtre_type .= '<option value="-1"';
	if ($active)
		$filtre_usage .= ' selected="selected"';
	$filtre_type .= '> '. _T('asso:entete_utilise') ."</option>\n";
	$filtre_type .= "</optgroup>\n";
	$filtre_type .= "</select>\n";
	echo association_form_filtres(array(
	), 'association_autorisations', array(
		'type' => $filtre_type,
	));
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo "<table width='100%' class='asso_tablo' id='liste_asso_plan'>\n";
	$thd = '<tr class="row_first">';
	$thd .= "\n<th scope='col'>". _T('asso:entete_nom') .'</th>';
	$thd .= "\n<th scope='col'>". _T('asso:entete_nombre') .'</th>';
	$thd .= "\n<th scope='col'>". _T('asso:entete_commentaire') .'</th>';
	$thd .= '<th colspan="2" class="actions">' . _T('asso:entete_actions') .'</th>';
	$thd .= "</tr>\n";
	echo $thd;
	$lc = sql_allfetsel('FLOOR(id_groupe/10) AS type_groupe', 'spip_asso_'.($active?'fonctions':'groupes'), 'id_groupe<100'.($type?" AND FLOOR(id_groupe/10)=$type":''), 'type_groupe', 'type_groupe' );
	foreach ($lc as $r) {
		if ( $lt[$r['type_groupe']] ) {
			echo '<tr style="border:0;">';
			echo '<th class="text">  '. _T('asso:'.$lt[$r['type_groupe']]) .'</th>';
			echo '<th colspan="4"><hr class="spip" /></th>';
			echo "</tr>\n";
			$sql = sql_select('a_g.*, COUNT(a_f.id_auteur) AS nbr', 'spip_asso_groupes AS a_g LEFT JOIN spip_asso_fonctions AS a_f ON a_g.id_groupe=a_f.id_groupe', 'FLOOR(a_g.id_groupe/10)='.$r['type_groupe'], 'id_groupe', 'id_groupe DESC');
			while ($groupe = sql_fetch($sql)) {
				echo '<tr>';
				echo '<td class="text">'. _T('asso:groupe_'.$groupe['id_groupe']) .'</td>';
				echo '<td class="integer">'. $groupe['nbr'] .'</td>';
				echo '<td class="text">'. $groupe['commentaire'] .'</td>';
				echo association_bouton_edit('groupe_autorisations', 'id='.$groupe['id_groupe']);
				echo association_bouton_list('membres_groupe', $groupe['id_groupe']);
				echo "</tr>\n";
			}
		}
	}
	echo "$thd</table>\n";
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>