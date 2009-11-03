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
	  	echo barre_onglets("config_lang", "tradlang");
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
				tradlang_importermodule1();
				break;
			case "visumodule":
				tradlang_visumodule();
				break;
			case "ajouterlangue":
				tradlang_visumodule();
				break;
			case "creermodule":
				break;
			case "popup":
				ob_clean();
				include("tradlang_popup.php");
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
function tradlang_importermodule1(){   
	debut_cadre_relief("", false, "", _T('tradlang:importer_module_etape',array('etape'=>1)));
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
		
	if (!isset($modules[$module]))
		return;
	
	$modok = $modules[$module];
	
	debut_cadre_relief("", false, "", _T('tradlang:visumodule'));
	
	echo recuperer_fond('prive/contenu/tradlang_ajout_codelangue',array('nom_mod'=>$module));
	
	fin_cadre_relief();
	
	debut_cadre_relief("", false, "", _T('tradlang:traductions'));
	
	// recupere la liste des traductions dans la base
	// et sur le disque
	$getmodules_fics = charger_fonction('tradlang_getmodules_fics','inc');
	$modules2 = $getmodules_fics($modok["dir_lang"]);
	$modok2 = $modules2[$module];
	
	// union entre modok et modok2
	foreach($modok2 as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0){
			$sel = "";
			$langue = substr($cle,7);
			if (!array_key_exists($langue, $modok)){
				$modok["langue_".$langue] = $item;
			}
		}
	}

	// imprime la table des langues
	echo "<table cellspacing='2' cellpadding='3'>\n";
	echo "<tr>";
	echo "<th>&nbsp;</th>\n";
	echo "<th>"._T('tradlang:synchro')."</th>\n";
	echo "<th>"._T('tradlang:traducok')."</th>\n";
	echo "<th>"._T('tradlang:traducnok')."</th>\n";
	echo "</tr>\n";
	
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0){
			$sel = "";
			$langue = substr($cle,7);
			echo "<tr>\n";
			echo "<td>".traduire_nom_langue($langue)." ($langue)</td>";
			
			if (tradlang_testesynchro($modok['idmodule'], $langue))
				echo "<td><img src='"._DIR_PLUGIN_TRADLANG."img_pack/vert.gif' alt='' /></td>\n";
			else
				echo "<td><img src='"._DIR_PLUGIN_TRADLANG."img_pack/rouge.gif' alt='' /></td>\n";
			
			echo "<td>&nbsp;</td>\n";
			echo "<td>&nbsp;</td>\n";
			echo "</tr>\n";	  
		}
	}
	echo "</table>";
	 
	fin_cadre_relief();
}

// verifie si le fichier passe en param
// est bien un fichier de langue
function tradlang_verif($fic){
	include($fic);
	// verifie si c'est un fichier langue
	if (is_array($GLOBALS[$GLOBALS['idx_lang']])){
		unset($GLOBALS[$GLOBALS['idx_lang']]);
		return true;
	}
	return false;
}
?>