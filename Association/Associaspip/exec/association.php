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
		echo fin_cadre_formulaire(true);
		
		$coordonnees_actif = test_plugin_actif('COORDONNEES');
		include_spip('inc/association_coordonnees');

		/* on recupere tout dans un tableau php pour pouvoir extraire le tableau des id_auteur a envoyer en parametre aux fonction de recuperation d'emails et telephone */
		$all = sql_allfetsel("id_auteur, statut_interne, fonction, nom_famille, prenom, sexe", 'spip_asso_membres', "fonction <> '' AND statut_interne <> 'sorti'", '',  "nom_famille");
		$tr_class = "pair";
		$id_auteurs = array();
		foreach ($all as $data) {
			$id_auteurs[] = $data['id_auteur'];
		}

		$emails = association_recuperer_emails_string($id_auteurs);

		echo '<br />';
		echo gros_titre(_T('asso:le_bureau'),'',false);		
		echo '<br />';	
		
		echo debut_cadre_relief('', true);
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:nom') . "</th>\n";
		echo '<th>' . _T('asso:fonction') . "</th>\n";
		if ($coordonnees_actif) {
			$telephones = association_recuperer_telephones_string($id_auteurs);
			echo '<th>' . _T('asso:telephone') . "</th>\n";
		}
		echo '<th>' . _T('asso:email') .  "</th>\n";

		echo '</tr>';

		foreach ($all as $data) {
			$id_auteur=$data['id_auteur'];
			$nom_affiche = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
			 
			$auteur = generer_url_ecrire('auteur_infos',"id_auteur=$id_auteur");
			$adh = generer_url_ecrire('voir_adherent',"id=$id_auteur");
			echo "\n<tr class='".$tr_class."'>\n";
			$tr_class = ($tr_class == "pair")?"impair":"pair";

			echo "<td class='arial11 border1'>",
				"<a href='$auteur' title=\"",
				_T('lien_voir_auteur'),
				'">',
				htmlspecialchars($nom_affiche),
				 "</a></td>\n";

			echo "<td class='arial11 border1'>",
				"<a href='$adh' title=\"",
				_T('asso:adherent_label_voir_membre'),
				"\">",
				htmlspecialchars($data['fonction']),
				 "</a></td>\n";
			if ($coordonnees_actif) {
				echo '<td class="arial1 border1">'.$telephones[$id_auteur].'</td>';
			}
			echo '<td class="arial1 border1" style="text-align:center">'.$emails[$id_auteur].'</td>';
			echo "</tr>\n";
		}
		echo '</table>';
		
		echo fin_cadre_relief(true);	
		echo fin_page_association();
		
		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		sql_updateq('spip_asso_membres', 
			array("statut_interne"=> 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE() ");
	}
}
?>
