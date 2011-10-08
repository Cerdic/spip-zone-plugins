<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/simplecal_utils');



function exec_simplecal_demo_dist(){
    global $spip_lang_right;
    
    // autorisation de voir cette page ?
	if (!autoriser('demo', 'evenement')) {
        // Message d'erreur
        include_spip('inc/minipres');
        echo minipres();
        exit;
    }

    // pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'simplecal_demo'),'data'=>'')); 	
	// entetes de la page
    $commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('simplecal:html_title'), "editer", "editer");			
	
    
    // #####################
    // # Colonne de gauche #
	// #####################

	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'simplecal_demo'),'data'=>''));

    // Affichage du bloc d'information
    $boite = "<div class='logo-plugin'><img src='"._DIR_SIMPLECAL_IMG_PACK."simplecal-logo-96.png' alt='"._T('simplecal:alt_img_logo')."' /></div>";
    $boite .= "<p class='logo-plugin-desc'>"._T('simplecal:description_plugin')."</p>";
    echo debut_boite_info(true);
    echo $boite; 
    echo fin_boite_info(true);
    

    // Affichage du bloc des raccourcis
    $raccourcis = "";
    $lien = generer_url_ecrire("evenements", "mode=avenir");
    $racc_tous = icone_horizontale(_T('simplecal:raccourcis_tous_evenements'), $lien, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
    $raccourcis .= $racc_tous;
    echo bloc_des_raccourcis($raccourcis);

	
    // #####################
    // # Contenu central   #
	// #####################

    
	echo debut_droite('', true);
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'simplecal_demo'),'data'=>''));

    
   
    echo gros_titre("simple-calendrier : D&eacute;mo", "", false);
   
	$contexte = array();
	$fond = recuperer_fond("prive/contenu/simplecal-demo",$contexte);    
    echo $fond;
    // ------------
    
    
    
	echo fin_gauche(), fin_page();
}


?>
