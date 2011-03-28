<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");
include_spip("tradlang_fonctions");

function exec_tradlang() {

	if (!autoriser('configurer', 'tradlang')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		pipeline('exec_init',array('args'=>array('exec'=>'tradlang'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('tradlang:tradlang'),"configuration","langues");
	
		echo gros_titre(_T('tradlang:tradlang'), '', false);
		echo debut_gauche('', true);
		
		echo debut_boite_info(true);
			echo propre(_T('tradlang:readme'));  	
		echo fin_boite_info(true);
		
		debut_cadre_relief("", false, "", _T('tradlang:moduletitre'));
		
		$mods = tradlang_getmodules_base();
		if (count($mods)){
			echo "<ul>";
			foreach($mods as $mod){
				echo "<li><a href='".generer_url_ecrire("tradlang","operation=visumodule&module=".$mod["nom_mod"])."'>".$mod["nom_module"]."</a></li>";
			}
			echo "</ul>";
		}
		else
			echo propre(_T('tradlang:aucunmodule'));  	
	
		echo "<form action='".generer_url_ecrire("tradlang")."' method='post' class='formulaire_spip'>\n";
		echo "<p class='boutons'>";
		echo "<input type='hidden' name='operation' value='importermodule' />\n";
		echo "<input type='submit' class='submit' value='"._T("tradlang:importermodule")."' />";
		echo "</p>";
		echo "</form>";
	
		fin_cadre_relief();
		
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'tradlang'),'data'=>''));
	  
	  	echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'tradlang'),'data'=>''));
		echo debut_droite('', true);
		
		$operation = _request("operation") ? _request("operation") : "importermodule";

		switch ($operation){
			case "importermodule":
				tradlang_importermodule();
				break;
			case "visumodule":
				tradlang_visumodule();
				break;
			exit;
		}
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'tradlang'),'data'=>''));
		echo fin_gauche(), fin_page();
	}
}

/**
 * Phase d'importation de module de langue
 * 
 * @return 
 */
function tradlang_importermodule(){   
	debut_cadre_relief("", false, "", _T('tradlang:importer_module'));
	echo recuperer_fond('prive/contenu/tradlang_importer_module',$_GET);
	fin_cadre_relief();
}

/**
 * Phase de visualisation de module de langue
 * 
 * @return 
 */
function tradlang_visumodule(){
	$module = _request('module');
	
	if (!isset($module) || empty($module))
		return false;
	
	$modules = tradlang_getmodules_base();
	
	debut_cadre_relief("", false, "", _T('tradlang:visumodule'));
	
	echo recuperer_fond('prive/contenu/tradlang_modifier_module',array('nom_mod'=>$module));
	
	fin_cadre_relief();
	
	if (isset($modules[$module])){
	debut_cadre_relief("", false, "", _T('tradlang:infos_trad_module'));
		echo recuperer_fond('prive/infos/tradlang_infos_module',array('module'=>$modules[$module]['nom_mod']));
	fin_cadre_relief();
	}
}
?>