<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

/** ----- Chargement prealable des fonctions secondaires d'affichage ----- **/

// Boite d'information 'Information'
function cadre_actualites_tous_infos() {

	// le contenu de la boite est mise dans une variable...
	$boite = // On affiche le logo du plugin
		"<img class='logo_plugin' src='"._DIR_ACTUALITES_IMG_PACK."actualites_96.png' alt='"._T('actualites:alt_img_logo')."' />"
		// On ouvre un paragraphe pour y mettre la description
		."<p class='description_plugin'>"._T('actualites:description_plugin')."</p>";

	// ... variable qui est retournee à la fonction appelante
	return debut_boite_info(true) . $boite . fin_boite_info(true);	
	}



/** ----- Fonction principale d'execution (exec_*_dist) de la page 'exec/ *.php' ----- **/
/**  Pour un exemple type : http://programmer.spip.org/Contenu-d-un-fichier-exec   **/

function exec_actualites_tous_dist(){

	// -- Si pas autorise : message d'erreur
	if (!autoriser('voir', 'actualites_tous')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


	pipeline('exec_init', array('args'=>array('exec'=>'actualites_tous'),'data'=>'')); 	// pipeline d'initialisation
	$commencer_page = charger_fonction('commencer_page', 'inc'); 			// entetes de la page
	echo $commencer_page(_T('actualites:html_title'), "editer", "editer");			
	

//
// Colonne de gauche
//

	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'actualites_tous'),'data'=>''));

	// Affichage du bloc d'information 'Navigation' (fonction definie au-dessus)
	echo cadre_actualites_tous_infos();

	// Preparation des boutons de creation de nouveaux objets
	// CFG court-circuite le contenu du bouton si l'utilisateur le souhaite explicitement.
	if ( (function_exists('lire_config')) && (lire_config('actualites/objet_actualite') == "off") ) {
		$boutons_creation_actualite = "";
	} else {
		$boutons_creation_actualite = icone_horizontale(_T('actualites:raccourcis_actualite'), generer_url_ecrire("actualites_edit","type=actualite&new=oui"), _DIR_ACTUALITES_IMG_PACK."actualite-24.gif", "creer.gif", false);
	}

	// Affichage de tous les boutons de creation (qu'ils soient vides ou non)
	echo bloc_des_raccourcis($boutons_creation_actualite);

	
//	
// Contenu central
//

	echo debut_droite('', true);
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'actualites_tous'),'data'=>''));

	// Passage en variable des tableaux contenant pour les listes d'objet (simplification de l'écriture)
	$liste_des_actualites = afficher_objets('actualite',_T('actualites:liste_actualites'), array("SELECT" => 'id_actualite, date, titre, statut', "FROM" => 'spip_actualites AS actualites', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);

	// On affiche la liste des actualites
	if ( (function_exists('lire_config')) && (lire_config('actualites/objet_actualite') == "off") )
		// Si CFG est installe et qu'il nous dit explicitement de ne pas afficher l'objet,
		// alors on court-circuite l'affichage
		$liste_des_actualites = "";
	echo $liste_des_actualites;

	// Cas (très) particulier : CFG est installe, et aucun objet n'est selectionne.
	if (function_exists('lire_config')) {
		if( lire_config('actualites/objet_actualite') == "off")
			echo "<div style='background-color: white; border: 1px solid black; padding: 10px; text-align: center;'>"._T('actualites:cfg_zero_objet')."</div>";
	}


	echo fin_gauche(), fin_page();
}
?>
