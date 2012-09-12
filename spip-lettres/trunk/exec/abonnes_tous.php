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


	function exec_abonnes_tous() {

		if (!autoriser('voir', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		pipeline('exec_init',array('args'=>array('exec'=>'abonnes_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:abonnes'), "naviguer", "abonnes");

		echo debut_gauche('', true);
		echo afficher_objets('nb_abonne', _T('lettresprive:evolution_nb_abonnes'), array(
			'FROM' => 'spip_abonnes_statistiques',
			'WHERE' => '',
			'ORDER BY' => 'periode DESC'));
		echo bloc_des_raccourcis(
				icone_horizontale(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit'), 'abonne-24.png', 'creer.gif', false).
				icone_horizontale(_T('lettresprive:aller_liste_lettres'), generer_url_ecrire("lettres_tous"), 'lettre-24.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:import_abonnes'), generer_url_ecrire("naviguer_import"),'import_abonnes.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:export_abonnes'), generer_url_ecrire("naviguer_export"), 'export_abonnes.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:purge_abonnes'), generer_url_ecrire("naviguer_purge"), 'purge_abonnes.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:configurer_formulaire_abonnement'), generer_url_ecrire('config_lettres_formulaire'), 'lettres-config_formulaire-24.png', 'rien.gif', false)
			);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'abonnes_tous'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'abonnes_tous'),'data'=>''));

   		echo debut_droite('', true);
		echo afficher_objets('abonne', _T('lettresprive:abonnes'), array(
			'FROM' => 'spip_abonnes',
			'WHERE' => '',
			'ORDER BY' => 'maj DESC'));
		echo afficher_objets('abonnement', _T('lettresprive:thematiques'), array(
			'SELECT' => 'id_rubrique, COUNT(id_abonne) AS total',
			'FROM' => 'spip_abonnes_rubriques',
			'WHERE' => 'statut="valide"',
			'ORDER BY' => 'total DESC',
			'GROUP BY' => 'id_rubrique'));

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'abonnes_tous'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>
