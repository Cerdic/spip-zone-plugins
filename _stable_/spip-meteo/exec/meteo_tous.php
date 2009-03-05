<?php


	/**
	 * SPIP-Météo
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
	include_spip('meteo_fonctions');


	function exec_meteo_tous() {

		if (!autoriser('voir', 'meteo')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('meteoprive:meteo'), "naviguer", "meteo_tous");

		echo debut_gauche('', true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));

		echo bloc_des_raccourcis(icone_horizontale(_T('meteoprive:ajouter_une_meteo'), generer_url_ecrire("meteo_edit", "id_meteo=-1"), _DIR_PLUGIN_METEO."prive/images/meteo-24.png", 'creer.gif', false));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));

		echo debut_droite('', true);
		echo afficher_objets('meteo', _T('meteoprive:liste_des_meteos'), array('FROM' => 'spip_meteo', 'ORDER BY' => 'maj DESC'));

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));

		echo fin_gauche();
		echo fin_page();
	}


?>