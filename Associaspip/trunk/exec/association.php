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

function exec_association() {
	sinon_interdire_acces( autoriser('voir_profil', 'association') );
	include_spip ('association_modules');
/// INITIALISATIONS : rien a faire
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('', 'association');
/// AFFICHAGES_LATERAUX : INFOS : presentation du plugin
	echo propre(_T('asso:association_info_doc'));
/// AFFICHAGES_LATERAUX : RACCOURCIS
	$raccourcis = array(
		array('profil_de_lassociation', 'assoc_qui.png', array('configurer_association'), array('editer_profil', 'association'), ),
		array('editer_asso_metas_utilisateur_lien', 'assoc_qui.png', array('editer_asso_metas_utilisateur'), array('editer_profil', 'association'), ),
		array('categories_de_cotisations', 'cotisation.png', array('categories'), array('editer_profil', 'association'), ),
		array('gerer_les_autorisations', 'annonce.gif', array('association_autorisations'), array('gerer_autorisations', 'association'), ),
		array('plan_comptable', 'plan_compte.png', array('plan_comptable'), array('gerer_compta', 'association'), ),
	);
	if ($GLOBALS['association_metas']['destinations'])
		$raccourcis[] = array('destination_comptable', 'euro-39.gif', array('destination_comptable'), array('gerer_compta', 'association'), );
	if ($GLOBALS['association_metas']['exercices'])
		$raccourcis[] = array('exercices_budgetaires_titre', 'calculatrice.gif', array('exercice_comptable'), array('gerer_compta', 'association'), );
	echo association_navigation_raccourcis($raccourcis, 1);
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('assoc_qui.png', 'association_infos_contacts');
/// AFFICHAGES_CENTRAUX : Profil de l'association
	echo debut_cadre_enfonce('', TRUE);
	if (!$GLOBALS['association_metas']['nom'] && autoriser('editer_profil', 'association')) { // c'est surement une nouvelle installation (vu que le nom est obligatoire)
		echo '<a href="'.generer_url_ecrire('configurer_association').'">'. gros_titre(_T('asso:profil_de_lassociation'), '', FALSE).'</a>';
	} else {
		echo recuperer_fond('modeles/asso_profil', array());
	}
	echo fin_cadre_enfonce(TRUE);
/// AFFICHAGES_CENTRAUX : Groupes persos de l'association
	$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
	while ($row = sql_fetch($queryGroupesAffiches)) { // affiche tous les groupes devant l'etre
		echo '<div class="vcard"><a class="include" href="#vcard-asso-adr"></a><div class="org" id="vcard-group'.$row['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr>'; //!\ inclusion de fragments :  http://microformats.org/wiki/include-pattern
		echo debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', TRUE, '', '<a class="organization-unit"'. (autoriser('editer_groupe', 'association') ? (' title="'. _T('asso:editer_groupe') .'" href="'. generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']) ):'') .'">'.$row['nom'].'</a>');
//		echo '<a class="org organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', FALSE).'</a>';
		echo '</div></div>';
		echo recuperer_fond('modeles/membres_groupe', array(
			'id_groupe' => $row['id_groupe']
		));
		echo fin_cadre_relief(TRUE);
	}
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>