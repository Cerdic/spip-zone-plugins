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


	function exec_naviguer_export() {
		global $spip_lang_right, $spip_lang_left;
		global $champs_extra, $id_rubrique;

		if (!autoriser('exporter', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:export_abonnes'), "naviguer", "abonnes_tous");

	
		debut_gauche();

		debut_boite_info();
		echo _T('lettresprive:aide_naviguer_export');
		echo '<ol>';
		echo '<li>'._T('lettresprive:email').'</li>';
		echo '<li>'._T('lettresprive:nom').'</li>';
		if ($champs_extra['abonnes']) {
			foreach ($champs_extra['abonnes'] as $cle => $valeur) {
				list($style, $filtre, $prettyname, $choix, $valeurs) = explode("|", $valeur);
				echo '<li>'.$prettyname.'</li>';
			}
		}
		echo '</ol>';
		fin_boite_info();

		debut_raccourcis();	
		icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');
		icone_horizontale(_T('lettresprive:exporter_tous_desabonnes'), generer_url_action('export_desabonnes'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/desabonne.png');
		if ($id_rubrique)
			icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-24.png');
		fin_raccourcis();	
	
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/export.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettresprive:export_abonnes'));
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";

		echo '<form action="'.generer_url_action('export_abonnes').'" method="post">';
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/export.png', false, "", _T('lettresprive:depuis_rubrique'));
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique($id_rubrique, 'rubrique', false);
		fin_cadre_enfonce();	
		echo '<div align="right">';
		echo "<INPUT TYPE='submit' NAME='telecharger' CLASS='fondo' VALUE='"._T('lettresprive:telecharger')."' STYLE='font-size:10px'>";
		echo "</div>";
		echo '</form>';

		fin_cadre_relief();

		echo fin_gauche();

		echo fin_page();
	}

?>