<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

/** ----- Chargement prealable des fonctions secondaires d'affichage ----- **/

// Boite d'information 'Information'
function cadre_veille_tous_infos() {

	// le contenu de la boite est mise dans une variable...
	$boite = // On affiche le logo du plugin
		"<img class='logo_plugin' src='"._DIR_VU_IMG_PACK."vu_logo_96.png' alt='"._T('vu:alt_img_logo')."' />"
		// On ouvre un paragraphe pour y mettre la description
		."<p class='description_plugin'>"._T('vu:description_plugin')."</p>";

	// ... variable qui est retournee à la fonction appelante
	return debut_boite_info(true) . $boite . fin_boite_info(true);	
	}



/** ----- Fonction principale d'execution (exec_*_dist) de la page 'exec/*.php ----- **/
/**  Pour un exemple type : http://programmer.spip.org/Contenu-d-un-fichier-exec   **/

function exec_veille_tous_dist(){

	// -- Si pas autorise : message d'erreur
	if (!autoriser('voir', 'veille_tous')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


	pipeline('exec_init', array('args'=>array('exec'=>'veille_tous'),'data'=>'')); 	// pipeline d'initialisation
	$commencer_page = charger_fonction('commencer_page', 'inc'); 			// entetes de la page
	echo $commencer_page(_T('vu:html_title'), "editer", "editer");			
	

//
// Colonne de gauche
//

	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'veille_tous'),'data'=>''));

	// Affichage du bloc d'information 'Navigation' (fonction definie au-dessus)
	echo cadre_veille_tous_infos();

	// Preparation des boutons de creation de nouveaux objets
	// CFG court-circuite le contenu du bouton si l'utilisateur le souhaite explicitement.
	if ( (function_exists('lire_config')) && (lire_config('vu/objet_annonce') == "off") ) {
		$boutons_creation_annonce = "";
	} else {
		$boutons_creation_annonce = icone_horizontale(_T('vu:raccourcis_annonce'), generer_url_ecrire("veille_edit","type=annonce&new=oui"), _DIR_VU_IMG_PACK."annonce-24.gif", "creer.gif", false);
	}

	if ( (function_exists('lire_config')) && (lire_config('vu/objet_evenement') == "off") ) {
		$boutons_creation_evenement = "";
	} else {
		$boutons_creation_evenement = icone_horizontale(_T('vu:raccourcis_evenement'), generer_url_ecrire("veille_edit","type=evenement&new=oui"), _DIR_VU_IMG_PACK."evenement-24.gif", "creer.gif", false);
	}

	if ( (function_exists('lire_config')) && (lire_config('vu/objet_publication') == "off") ) {
		$boutons_creation_publication = "";
	} else {
		$boutons_creation_publication = icone_horizontale(_T('vu:raccourcis_publication'), generer_url_ecrire("veille_edit","type=publication&new=oui"), _DIR_VU_IMG_PACK."publication-24.gif", "creer.gif", false);
	}

	// Affichage de tous les boutons de creation (qu'ils soient vides ou non)
	echo bloc_des_raccourcis($boutons_creation_annonce. $boutons_creation_evenement. $boutons_creation_publication);

	
//	
// Contenu central
//

	echo debut_droite('', true);
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'veille_tous'),'data'=>''));

	// Passage en variable des tableaux contenant pour les listes d'objet (simplification de l'écriture)
	$liste_des_annonces = afficher_objets('annonce',_T('vu:liste_annonces'), array("SELECT" => 'id_annonce, date, titre, statut', "FROM" => 'spip_vu_annonces AS annonces', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	$liste_des_evenements = afficher_objets('evenement',_T('vu:liste_evenements'), array("SELECT" => 'id_evenement, date, titre, statut', "FROM" => 'spip_vu_evenements AS evenements', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	$liste_des_publications = afficher_objets('publication',_T('vu:liste_publications'),	array("SELECT" => 'id_publication, date, titre, statut', "FROM" => 'spip_vu_publications AS publications', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);

	// On affiche la liste des annonces
	if ( (function_exists('lire_config')) && (lire_config('vu/objet_annonce') == "off") )
		// Si CFG est installe et qu'il nous dit explicitement de ne pas afficher l'objet,
		// alors on court-circuite l'affichage
		$liste_des_annonces = "";
	echo $liste_des_annonces;

	// On affiche la liste des evenements
	if ( (function_exists('lire_config')) && (lire_config('vu/objet_evenement') == "off") )
		// Si CFG est installe et qu'il nous dit explicitement de ne pas afficher l'objet,
		// alors on court-circuite l'affichage
		$liste_des_evenements = "";
	echo $liste_des_evenements;

	// On affiche la liste des publications
	if ( (function_exists('lire_config')) && (lire_config('vu/objet_publication') == "off") )
		// Si CFG est installe et qu'il nous dit explicitement de ne pas afficher l'objet,
		// alors on court-circuite l'affichage
		$liste_des_publications = "";
	echo $liste_des_publications;

	// Cas (très) particulier : CFG est installe, et aucun objet n'est selectionne.
	if (function_exists('lire_config')) {
		if( lire_config('vu/objet_annonce') == "off" && lire_config('vu/objet_evenement') == "off" && lire_config('vu/objet_publication') == "off" )
			echo "<div style='background-color: white; border: 1px solid black; padding: 10px; text-align: center;'>"._T('vu:cfg_zero_objet')."</div>";
	}


	echo fin_gauche(), fin_page();
}
?>
