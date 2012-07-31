<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_association()
{
	if (!autoriser('voir_profil', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association();
		// presentation du plugin
		echo propre(_T('asso:association_info_doc'));
		// datation et raccourcis
		if (autoriser('editer_profil', 'association')) {
			$res['profil_de_lassociation'] = array('assoc_qui.png', 'configurer_association');
			$res['editer_asso_metas_utilisateur_lien'] = array('assoc_qui.png', 'editer_asso_metas_utilisateur');
		}
		$res['categories_de_cotisations'] = array('cotisation.png', 'categories');
		if (autoriser('gerer_autorisations', 'association')) {
			$res['gerer_les_autorisations'] = array('annonce.gif', 'association_autorisations');
		}
		$res['plan_comptable'] = array('plan_compte.png', 'plan_comptable');
		if ($GLOBALS['association_metas']['destinations']) {
				$res['destination_comptable'] = array('plan_compte.png', 'destination');
		}
		$res['exercices_budgetaires_titre'] = array('plan_compte.png', 'exercices');

		icones_association(array(), $res);

		debut_cadre_association('assoc_qui.png', 'association_infos_contacts');
		echo '<div class="vcard" id="vcard-asso">';
		// Profil de l'association
		echo debut_cadre_enfonce('',true);
		if (!$GLOBALS['association_metas']['nom'] && autoriser('editer_profil', 'association')) { // c'est surement une nouvelle installation (vu que le nom est obligatoire)
			echo '<a href="'.generer_url_ecrire('configurer_association').'">'. gros_titre(_T('asso:profil_de_lassociation'),'',false).'</a>';
		}
		echo '<h3 class="fn org"><strong class="organization-name">'.$GLOBALS['association_metas']['nom']."</strong></h3>\n";
		echo '<p class="adr">';
		echo '<span class="street-address">'.$GLOBALS['association_metas']['rue']."</span><br />\n";
		echo '<span class="postal-code">'.$GLOBALS['association_metas']['cp'].'</span>&nbsp;';
		echo '<span class="locality">'.$GLOBALS['association_metas']['ville']."</span><br />\n";
		echo '<abbr class="country" title="';
		$pays = $GLOBALS['association_metas']['pays'];
		if (test_plugin_actif('PAYS')) {
			$pays = sql_getfetsel('nom', 'spip_pays', (is_numeric($pays)?"id_pays=$pays":"code='$pays'") );
			echo propre($row['nom']);
		} else {
			echo $pays;
		}
		echo '"></abbr>';
		echo "</p>\n";
		echo '<p class="tel">'.$GLOBALS['association_metas']['telephone']."</p>\n";
		echo '<p class="email">'.$GLOBALS['association_metas']['email']."</p>\n";
		echo '<ul class="note">';
		if ($GLOBALS['association_metas']['declaration'])
			echo '<li>'.$GLOBALS['association_metas']['declaration']."</li>\n";
		if ($GLOBALS['association_metas']['prefet'])
			echo '<li>'.$GLOBALS['association_metas']['prefet']."</li>\n";
		// afficher les metas definies par l'utilisateur si il y en a
		$query = sql_select('nom,valeur', 'spip_association_metas', "nom LIKE 'meta_utilisateur_%'");
		while ($row = sql_fetch($query)) {
			echo '<li>'. ucfirst(_T(str_replace('meta_utilisateur_', '', $row['nom']))).'&nbsp;:&nbsp;'.$row['valeur']."</li>\n";
		}
		echo "</ul>";
		echo fin_cadre_enfonce(true);
		echo "</div>\n";
		// affiche tous les groupes devant l'etre
		$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
		while ($row = sql_fetch($queryGroupesAffiches)) {
			echo '<div class="vcard" id="vcard-group'.$row['id_groupe'].'"><a class="include" href="#vcard-asso"></a>',
			'<span class="fn org"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr>'; //!\ l'inclusion de fragments (class=include cf. http://microformats.org/wiki/include-pattern) est la bonne methode, mais n'est pas encore prise en compte partout, donc on duplique quand meme le nom
			echo debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', true, '', '<a class="org organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.$row['nom'].'</a>');
//			echo '<a class="org organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif', false).'</a>';
			echo recuperer_fond('prive/contenu/voir_membres_groupe', array('id_groupe' => $row['id_groupe']));
			echo fin_cadre_relief(true);
			echo '</span></div>';
		}
		fin_page_association();
		//Petite routine pour mettre a jour les statuts de cotisation "echu"
		sql_updateq('spip_asso_membres',
			array('statut_interne' => 'echu'),
			"statut_interne='ok' AND validite<CURRENT_DATE() ");
	}
}

?>
