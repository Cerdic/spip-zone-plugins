<?php


	/**
	 * SPIP-Sondages
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
	include_spip('sondages_fonctions');


	function exec_sondages_tous() {

		if (!autoriser('voir', 'sondages')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'sondages_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('sondagesprive:sondages'), "naviguer", "sondages");

		echo debut_gauche('', true);
		echo bloc_des_raccourcis(icone_horizontale(_T('sondagesprive:creer_nouveau_sondage'), generer_url_ecrire("sondages_edit"), _DIR_PLUGIN_SONDAGES."/prive/images/sondage-24.png", 'creer.gif', false));
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'sondages_tous'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'sondages_tous'),'data'=>''));

   		echo debut_droite('', true);
		echo afficher_objets('sondage', _T('sondagesprive:sondages_en_cours_de_redaction'), array('FROM' => 'spip_sondages', 'WHERE' => 'statut="prepa"', 'ORDER BY' => 'maj DESC'));
		echo afficher_objets('sondage', _T('sondagesprive:sondages_publies'), array('FROM' => 'spip_sondages', 'WHERE' => 'statut="publie"', 'ORDER BY' => 'maj DESC'));
		echo afficher_objets('sondage', _T('sondagesprive:sondages_termines'), array('FROM' => 'spip_sondages', 'WHERE' => 'statut="termine"', 'ORDER BY' => 'maj DESC'));

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'sondages_tous'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>