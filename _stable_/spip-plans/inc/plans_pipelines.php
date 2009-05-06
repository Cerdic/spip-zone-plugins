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


	function plans_header_prive($texte) {
		global $couleur_foncee, $couleur_claire;
		$js.= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAN.'/prive/javascript/jquery.plan.js"></script>'."\n";
		$css.= '<style type="text/css" media="screen">';
		$css.= recuperer_fond("css_plan_prive", array('couleur_foncee' => str_replace('#', '', $couleur_foncee), 'couleur_claire' => str_replace('#', '', $couleur_claire)));
		$css.= '<!--[if lte IE 6]>';
		$css.= recuperer_fond("css_plan_prive_ie");
		$css.= '<![endif]-->';
		$css.= '</style>';
		$texte.= $js;
		$texte.= $css;
		return $texte;
	}


	function plans_insert_head($texte) {
		$texte.= '<link rel="stylesheet" href="'.generer_url_public('css_plan').'" type="text/css" media="screen" />'."\n";
		$texte.= '<!--[if lte IE 6]><link rel="stylesheet" href="'.generer_url_public('css_plan_ie').'" type="text/css" media="screen" /><![endif]-->'."\n";
		$texte.= '<script type="text/javascript" src="'.generer_url_public('jquery.plan.js').'"></script>'."\n";
		return $texte;
	}


	function plans_editer_contenu_objet($flux){
		if ($flux['args']['type'] == 'groupe_mot'){
			$checked = in_array('plans', $flux['args']['contexte']['tables_liees']);
			$checked = $checked ? ' checked="checked"' : '';
			$input = '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="plans" id="plans"'.$checked.' /><label for="plans">'._T('plans:item_mots_cles_association_plans').'</label></div>';
			$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);
		}
		return $flux;
	}


	function plans_libelle_association_mots($libelles){
		$libelles['plans'] = 'plans:plans';
		return $libelles;
	}


	function plans_declarer_tables_objets_surnoms($surnoms) {
		$surnoms['plan'] = 'plans';
		return $surnoms;
	}
	
	
	function plans_rechercher_liste_des_champs($tables) {
		$tables['plan']['titre']		= 8;
		$tables['plan']['descriptif']	= 4;
		return $tables;
	}
	

?>