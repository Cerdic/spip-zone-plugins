<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip('formulaires_fonctions');


	function exec_formulaires_tous() {

		if (!autoriser('voir', 'formulaires')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		echo debut_gauche('', true);
		echo bloc_des_raccourcis(
				icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire('formulaires_edit', 'id_formulaire=-1'), _DIR_PLUGIN_FORMULAIRES."/prive/images/formulaire-24.png", 'creer.gif', false)
			);
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

   		echo debut_droite('', true);
		echo afficher_objets('formulaire', _T('formulairesprive:formulaires_hors_ligne'), array("FROM" => 'spip_formulaires', "WHERE" => 'statut="hors_ligne"', 'ORDER BY' => "maj DESC"));
		echo afficher_objets('formulaire', _T('formulairesprive:formulaires_en_ligne'), array("FROM" => 'spip_formulaires', "WHERE" => 'statut="en_ligne"', 'ORDER BY' => "maj DESC"));

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>