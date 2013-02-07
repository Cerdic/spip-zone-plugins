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
	if (!autoriser('voir_profil', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('', 'association');
		// presentation du plugin
		echo propre(_T('asso:association_info_doc'));
		// datation et raccourcis
		$raccourcis_actifs = array(
			'profil_de_lassociation' => array('assoc_qui.png', array('configurer_association'), array('editer_profil', 'association'), ),
			'editer_asso_metas_utilisateur_lien' => array('assoc_qui.png', array('editer_asso_metas_utilisateur'), array('editer_profil', 'association')),
			'categories_de_cotisations' => array('cotisation.png', array('categories'), array('editer_profil', 'association')),
			'gerer_les_autorisations' => array('annonce.gif', array('association_autorisations'), array('gerer_autorisations', 'association')),
			'plan_comptable' => array('plan_compte.png', array('plan_comptable'), array('gerer_compta', 'association') ),
			'destination_comptable' => array('euro-39.gif', array('destination'), $GLOBALS['association_metas']['destinations'] ? array('gerer_compta', 'association') : ''),
			'exercices_budgetaires_titre' => array('calculatrice.gif', array('exercices'), array('gerer_compta', 'association') ),
		); // racourcis natifs
		$modules_externes = pipeline('associaspip', array()); // Tableau des modules ajoutes par d'autres plugins : 'prefixe_plugin'=> array( 0=>array(bouton,onglet,actif), 1=>array(bouton,config,actif) )
		foreach ( $modules_externes as $plugin=>$boutons ) {
			if ( test_plugin_actif($plugin) )
				$raccourcis_actifs[] = $boutons[1];
		}
		echo association_navigation_raccourcis('', $raccourcis_actifs);
		debut_cadre_association('assoc_qui.png', 'association_infos_contacts');
		// Profil de l'association
		echo debut_cadre_enfonce('', TRUE);
		if (!$GLOBALS['association_metas']['nom'] && autoriser('editer_profil', 'association')) { // c'est surement une nouvelle installation (vu que le nom est obligatoire)
			echo '<a href="'.generer_url_ecrire('configurer_association').'">'. gros_titre(_T('asso:profil_de_lassociation'), '', FALSE).'</a>';
		} else {
			echo recuperer_fond('modeles/asso_profil', array());
		}
		echo fin_cadre_enfonce(TRUE);
		$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
		while ($row = sql_fetch($queryGroupesAffiches)) { // affiche tous les groupes devant l'etre
			echo '<div class="vcard"><a class="include" href="#vcard-asso-adr"></a><div class="org" id="vcard-group'.$row['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr>'; //!\ inclusion de fragments :  http://microformats.org/wiki/include-pattern
			echo debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', TRUE, '', '<a class="organization-unit"'. (autoriser('editer_groupe', 'association') ? (' title="'. _T('asso:editer_groupe') .'" href="'. generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']) ):'') .'">'.$row['nom'].'</a>');
//			echo '<a class="org organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', FALSE).'</a>';
			echo '</div></div>';
			echo recuperer_fond('modeles/membres_groupe', array(
				'id_groupe' => $row['id_groupe']
			));
			echo fin_cadre_relief(TRUE);
		}
		fin_page_association();
		// Petite routine pour mettre a jour les statuts de cotisation "echu".
		// Possible http://programmer.spip.net/Declarer-une-tache http://contrib.spip.net/Ajouter-une-tache-CRON-dans-un-plugin-SPIP ?
		sql_updateq('spip_asso_membres',
			array('statut_interne' => 'echu'),
			"statut_interne='ok' AND date_validite<CURRENT_DATE() ");
	}
}

?>
