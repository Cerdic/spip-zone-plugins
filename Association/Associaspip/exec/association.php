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

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_association() {
	include_spip('inc/autoriser');
	if (!autoriser('associer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:profil_de_lassociation'),  '?exec=configurer_association', 'assoc_qui.png');
		$res .= association_icone(_T('asso:categories_de_cotisations'),  generer_url_ecrire('categories'), 'cotisation.png',  '');
		$res .= association_icone(_T('asso:plan_comptable'),  generer_url_ecrire('plan'), 'plan_compte.png',  '');
		if ($GLOBALS['association_metas']['destinations']=='on')
			$res .= association_icone(_T('asso:destination_comptable'),  generer_url_ecrire('destination'), 'plan_compte.png',  '');
		$res.=association_icone(_T('asso:exercices_budgetaires_titre'),  generer_url_ecrire('exercices'), 'plan_compte.png',  '');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		echo '<div class="vcard">';
		echo debut_cadre_formulaire('',true);
		// Profil de l'association
//		echo '<div class="vcard">';
		echo '<p class="org"><strong class="organization-name">'.$GLOBALS['association_metas']['nom']."</strong></p>\n";
		echo "<p class='adr'>";
		echo '<span class="street-address">'.$GLOBALS['association_metas']['rue']."</span><br />\n";
		echo '<span class="postal-code">'.$GLOBALS['association_metas']['cp'].'</span>&nbsp;';
		echo '<span class="locality">'.$GLOBALS['association_metas']['ville']."</span><br />\n";
		echo '<abbr class="country" title="';
		$pays = $GLOBALS['association_metas']['pays'];
		if (test_plugin_actif('pays')) {
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
		echo '<li>'.$GLOBALS['association_metas']['declaration']."</li>\n";
		echo '<li>'.$GLOBALS['association_metas']['prefet']."</li>\n";
//		echo "</div>\n";
		/* afficher les metas definies par l'utilisateur si il y en a */
		$query = sql_select('nom,valeur', 'spip_association_metas', "nom LIKE 'meta_utilisateur_%'");
		while ($row = sql_fetch($query)) {
			echo '<li>'. ucfirst(_T(str_replace('meta_utilisateur_', '', $row['nom']))).'&nbsp;:&nbsp;'.$row['valeur']."</li>\n";
		}
		echo "</ul>";
		echo fin_cadre_formulaire(true);
		/* affiche tous les groupes devant l'etre */
		$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
		while ($row = sql_fetch($queryGroupesAffiches)) {
			echo '<br/><a class="organization-unit" title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], '', false).'</a>';
			echo debut_cadre_relief('', true);
			echo recuperer_fond('prive/contenu/voir_membres_groupe', array('id_groupe' => $row['id_groupe']));
			echo fin_cadre_relief(true);
		}
		echo "</div>\n";
		echo fin_page_association();
		//Petite routine pour mettre a jour les statuts de cotisation "echu"
		sql_updateq('spip_asso_membres',
			array('statut_interne' => 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE() ");
	}
}

?>