<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_association() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));  	
		echo fin_boite_info(true);
		
		$res=association_icone(_T('asso:profil_de_lassociation'),  '?exec=configurer_association', 'assoc_qui.png');
		$res.=association_icone(_T('asso:categories_de_cotisations'),  generer_url_ecrire("categories"), 'cotisation.png',  '');
		$res.=association_icone(_T('asso:plan_comptable'),  generer_url_ecrire("plan"), 'plan_compte.png',  '');
		if ($GLOBALS['association_metas']['destinations']=="on") $res.=association_icone(_T('asso:destination_comptable'),  generer_url_ecrire("destination"), 'plan_compte.png',  '');

		echo bloc_des_raccourcis($res);
		echo debut_droite("",true);	
		echo debut_cadre_formulaire("",true);
#		echo gros_titre(_T('asso:votre_asso'),'',false);
#		echo "<br />\n";		
		echo '<strong>'.$GLOBALS['association_metas']['nom'].'</strong><br/>';
		echo $GLOBALS['association_metas']['rue']."<br />\n";
		echo $GLOBALS['association_metas']['cp'].'&nbsp;';
		echo $GLOBALS['association_metas']['ville']."<br />\n";
		echo $GLOBALS['association_metas']['telephone']."<br />\n";
		echo $GLOBALS['association_metas']['email']."<br />\n";
		echo $GLOBALS['association_metas']['siret']."<br />\n";
		echo $GLOBALS['association_metas']['declaration']."<br />\n";
		echo $GLOBALS['association_metas']['prefet']."<br />\n";
		/* afficher les metas definies par l'utilisateur si il y en a */
		$query = sql_select('nom,valeur', 'spip_association_metas', "nom LIKE 'meta_utilisateur_%'");
		while ($row = sql_fetch($query)) {
			echo ucfirst(str_replace('_', ' ', str_replace('meta_utilisateur_', '', $row['nom']))).'&nbsp;:&nbsp;'.$row['valeur'].'<br>';
		}
		echo fin_cadre_formulaire(true);
		
		/* affiche tous les groupes devant l'etre */
		$queryGroupesAffiches = sql_select('id_groupe, nom', 'spip_asso_groupes', 'affichage>0', '', 'affichage');
		while ($row = sql_fetch($queryGroupesAffiches)) {
			echo '<br/><a title="'._T('asso:editer_groupe').'" href="'.generer_url_ecrire('edit_groupe', 'id='.$row['id_groupe']).'">'.gros_titre($row['nom'], '', false).'</a>';
			echo debut_cadre_relief('', true);
			echo recuperer_fond('prive/contenu/voir_membres_groupe', array('id_groupe' => $row['id_groupe']));
			echo fin_cadre_relief(true);
		}
		
		echo fin_page_association();
		
		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		sql_updateq('spip_asso_membres', 
			array("statut_interne"=> 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE() ");
	}
}
?>
