<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('lettres_fonctions');
	include_spip('inc/presentation');


	function exec_naviguer_purge() {
		global $spip_lang_right, $spip_lang_left;
		global $id_rubrique, $purger, $id_parent;

		if (!autoriser('purger', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

		if (!empty($purger)) {
			$abonnes = spip_query('SELECT id_abonne FROM spip_abonnes_rubriques WHERE id_rubrique='.intval($id_parent));
			$nb_abonnements_supprimes = 0;
			while ($arr = spip_fetch_array($abonnes)) {
				$abonne = new abonne($arr['id_abonne']);
				$abonne->valider_desabonnement($id_parent);
				$abonne->supprimer_si_zero_abonnement();
				$nb_abonnements_supprimes++;
			}
		}
			

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:purge_abonnes'), "naviguer", "abonnes_tous");
	
		debut_gauche();
		
		debut_boite_info();
		echo _T('lettresprive:aide_naviguer_purge');
		fin_boite_info();

		debut_raccourcis();	
		icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');
		if (isset($id_rubrique))
			icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-24.png');
		fin_raccourcis();	
	
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/purge.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettresprive:purge_abonnes'));
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";

		echo "<form method='post' action='".generer_url_ecrire('naviguer_purge')."' method='get'>";

		if (!empty($purger)) {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/purge.png', false, "", _T('lettresprive:resultat'));
			echo "<br />";
			echo "<span class='verdana1'><B>"._T('lettresprive:nb_abonnements_supprimes')."</B> ".$nb_abonnements_supprimes."</span><br />";
			echo "<br />";
			echo '<div align="right">';
			echo "<INPUT TYPE='submit' NAME='retour' CLASS='fondo' VALUE='"._T('lettresprive:retour')."' STYLE='font-size:10px'>";
			echo "</div>";
			fin_cadre_enfonce();
		} else {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/purge.png', false, "", _T('lettresprive:selectionnez_rubrique'));
			$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
			echo $selecteur_rubrique($id_rubrique, 'rubrique', false);
			fin_cadre_enfonce();	
			echo '<input type="hidden" name="id_rubrique" value="'.$id_rubrique.'" />';
			echo '<div align="right">';
			echo "<INPUT TYPE='submit' NAME='purger' CLASS='fondo' VALUE='"._T('lettresprive:purger')."' STYLE='font-size:10px'>";
			echo "</div>";
		}

		echo '</form>';

		fin_cadre_relief();

		echo fin_gauche();

		echo fin_page();
	}

?>