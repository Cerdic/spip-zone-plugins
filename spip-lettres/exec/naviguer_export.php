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
	include_spip('inc/presentation');
	include_spip('lettres_fonctions');


	function exec_naviguer_export() {
		$id_rubrique = $_REQUEST['id_rubrique'];

		if (!autoriser('exporterabonnes', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:export_abonnes'), "naviguer", "abonnes_tous");

		echo '<br /><br /><br />';
		echo gros_titre(_T('lettresprive:export_abonnes'),'',false);

		echo debut_gauche('', true);

		echo debut_boite_info(true);
		echo _T('lettresprive:aide_naviguer_export');
		echo '<ol>';
		echo '<li>'._T('lettresprive:email').'</li>';
		echo '<li>'._T('lettresprive:nom').'</li>';
/*
TODO
		if ($champs_extra['abonnes']) {
			foreach ($champs_extra['abonnes'] as $cle => $valeur) {
				list($style, $filtre, $prettyname, $choix, $valeurs) = explode("|", $valeur);
				echo '<li>'.$prettyname.'</li>';
			}
		}
*/
		echo '</ol>';
		echo fin_boite_info(true);

		$raccourcis = icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'rien.gif', false);
		$raccourcis.= icone_horizontale(_T('lettresprive:exporter_tous_desabonnes'), generer_url_action('export_desabonnes', '', false, true), _DIR_PLUGIN_LETTRES.'prive/images/desabonne.png', 'rien.gif', false);
		if ($id_rubrique)
			$raccourcis.= icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), _DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', 'rien.gif', false);
		echo bloc_des_raccourcis($raccourcis);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_export'),'data'=>''));

   		echo debut_droite('', true);

		echo '<form action="'.generer_url_action('export_abonnes', '', false, true).'" method="post">';
		echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/export.png', true, "", _T('lettresprive:depuis_rubrique'));
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique($id_rubrique, 'rubrique', false);
		echo '<div align="right">';
		echo '<input type="submit" name="telecharger" class="fondo" value="'._T('lettresprive:telecharger').'" />';
		echo '</div>';
		echo fin_cadre_enfonce(true);	
		echo '</form>';

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'naviguer_export'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>