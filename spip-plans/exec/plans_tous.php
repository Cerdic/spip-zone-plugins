<?php


	/**
	 * SPIP-Plans
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
	include_spip('plans_fonctions');


	function exec_plans_tous() {

		if (!autoriser('voir', 'plans')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'plans_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('plans:plans'), "naviguer", "plans_tous");

		echo debut_gauche('', true);
		echo bloc_des_raccourcis(
				icone_horizontale(_T('plans:creer_nouveau_plan'), generer_url_ecrire('plans_edit', 'id_plan=-1'), _DIR_PLUGIN_PLAN."/prive/images/plan-24.png", 'creer.gif', false)
			);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'plans_tous'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'plans_tous'),'data'=>''));

   		echo debut_droite('', true);
		echo afficher_objets('plan', _T('plans:plans_hors_ligne'), array('FROM' => 'spip_plans', 'WHERE' => 'statut="hors_ligne"', 'ORDER BY' => 'maj DESC'));
		echo afficher_objets('plan', _T('plans:plans_en_ligne'), array('FROM' => 'spip_plans', 'WHERE' => 'statut="en_ligne"', 'ORDER BY' => 'maj DESC'));

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'plans_tous'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>