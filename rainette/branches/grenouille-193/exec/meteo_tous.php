<?php
	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 * @author Pierre Basson 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
	 if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');
	function exec_meteo_tous() { 
		global $couleur_foncee, $couleur_claire;
		
		if (!autoriser('webmestre')) {
		echo _T('avis_non_acces_page');
		echo fin_page();
		exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('meteo:meteo'), "naviguer", "meteo_tous");

	echo debut_gauche('', true);
		
	echo bloc_des_raccourcis(icone_horizontale(_T('meteo:ajouter_une_meteo'), generer_url_ecrire("meteo_edit","new=oui"), '../'._DIR_PLUGIN_METEO.'/img_meteo/meteo.png', 'creer.gif', false));
	
	echo debut_droite('', true);
	
		include_spip('public/assembler'); 
		$contexte['id_meteo'] = _request('id_meteo');
		$contexte['couleur_foncee'] = $couleur_foncee;
		$contexte['couleur_claire'] = $couleur_claire;
		$contexte['direct_lang'] = $spip_lang_right;
		
		echo recuperer_fond('fonds/liste_meteo', $contexte);
	
	echo fin_gauche(), fin_page();

	}


?>