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
		raccourcis_association(array(), array(
			'profil_de_lassociation' => array('assoc_qui.png', 'configurer_association', array('editer_profil', 'association'), ),
			'editer_asso_metas_utilisateur_lien' => array('assoc_qui.png', 'editer_asso_metas_utilisateur', array('editer_profil', 'association'), ),
			'categories_de_cotisations' => array('cotisation.png', 'categories', array('editer_profil', 'association'), ),
			'gerer_les_autorisations' => array('annonce.gif', 'association_autorisations', array('gerer_autorisations', 'association'), ),
			'plan_comptable' => array('plan_compte.png', 'plan_comptable', array('gerer_compta', 'association') ),
			'destination_comptable' => array('euro-39.gif', 'destination', array('gerer_compta', 'association') && $GLOBALS['association_metas']['destinations'] ),
			'exercices_budgetaires_titre' => array('calculatrice.gif', 'exercices', array('gerer_compta', 'association') ),
		));
		debut_cadre_association('assoc_qui.png', 'association_infos_contacts');
		// Profil de l'association
		echo debut_cadre_enfonce('', TRUE);
		if (!$GLOBALS['association_metas']['nom'] && autoriser('editer_profil', 'association')) { // c'est surement une nouvelle installation (vu que le nom est obligatoire)
			echo '<a href="'.generer_url_ecrire('configurer_association').'">'. gros_titre(_T('asso:profil_de_lassociation'), '', FALSE).'</a>';
		} else {
			echo '<div class="vcard">';
			echo '<h3 class="fn org"><strong class="organization-name">'.$GLOBALS['association_metas']['nom']."</strong></h3>\n";
			$pays = $GLOBALS['association_metas']['pays'];
			echo '<p class="adr" id="vcard-asso-adr">'. recuperer_fond('modeles/coordonnees_adresse', array(
				'voie' => '<span class="street-address">'.$GLOBALS['association_metas']['rue'].'</span>',
//				'complement'
//				'boite_postale'
				'code_postal' => '<span class="postal-code">'.$GLOBALS['association_metas']['cp'].'</span>',
				'ville' => '<span class="locality">'.$GLOBALS['association_metas']['ville'].'</span>',
//				'region'
				'nom_pays' => '<abbr class="country" title="'. ( (test_plugin_actif('PAYS')) ? propre(sql_getfetsel('nom', 'spip_pays', (is_numeric($pays)?"id_pays=$pays":"code='$pays'") )) : $pays ) .'"></abbr>',
				'_ht' => '&nbsp;',
				'_nl' => '<br />',
			)) ."</p>\n";
			if ($GLOBALS['association_metas']['telephone'])
				echo '<p class="tel">'. recuperer_fond('modeles/coordonnees_telephone', array(
					'numero' => $GLOBALS['association_metas']['telephone'],
				)) ."</p>\n";
			if ($GLOBALS['association_metas']['email'])
				echo '<p class="email">'.$GLOBALS['association_metas']['email']."</p>\n";
			if ($GLOBALS['association_metas']['infofiscal'])
				echo  '<p class="bday">'. _T('asso:config_libelle_infofiscal') .association_formater_date($GLOBALS['association_metas']['infofiscal'], 'bday') ."</p>\n";
			echo '<dl class="note">';
			if ($GLOBALS['association_metas']['declaration'])
				echo '<dt>'. _T('asso:config_libelle_declaration') .'</dt>'
				. '<dd>'.$GLOBALS['association_metas']['declaration']."</dd>\n";
			if ($GLOBALS['association_metas']['prefet'])
				echo '<dt>'. _T('asso:config_libelle_prefet') .'</dt>'
				. '<dd>'.$GLOBALS['association_metas']['prefet']."</dd>\n";
			if ($GLOBALS['association_metas']['objet'])
				echo '<dt>'. _T('asso:config_libelle_objet') .'</dt>'
				. '<dd>'.$GLOBALS['association_metas']['declaration']."</dd>\n";
			$query = sql_select('nom,valeur', 'spip_association_metas', "nom LIKE 'meta_utilisateur_%'");
			while ($row = sql_fetch($query)) { // afficher les metas definies par l'utilisateur si il y en a
				echo '<dt>'. _T('perso:'. str_replace('meta_utilisateur_', '', $row['nom'])) .'</dt>'
				.'<dd>'.$row['valeur']."</dd>\n";
			}
			echo "</dl>";
			echo "</div>\n";
		}
		echo fin_cadre_enfonce(TRUE);
		$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
		while ($row = sql_fetch($queryGroupesAffiches)) { // affiche tous les groupes devant l'etre
			echo '<div class="vcard"><a class="include" href="#vcard-asso-adr"></a><div class="org" id="vcard-group'.$row['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr>'; //!\ inclusion de fragments :  http://microformats.org/wiki/include-pattern
			echo debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', TRUE, '', '<a class="organization-unit"'. (autoriser('editer_groupe', 'association') ? (' title="'. _T('asso:editer_groupe') .'" href="'. generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']) ):'') .'">'.$row['nom'].'</a>');
//			echo '<a class="org organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', FALSE).'</a>';
			echo '</div></div>';
			echo recuperer_fond('modeles/asso_membres', array(
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
