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

		// D'abord un 'bloc des raccourcis' pour les boutons de creation de nouveaux objets
		echo bloc_des_raccourcis(icone_horizontale(_T('vu:raccourcis_annonce'), generer_url_ecrire("veille_edit","type=annonce&new=oui"), _DIR_VU_IMG_PACK."annonce-24.gif", "creer.gif", false)
			. icone_horizontale(_T('vu:raccourcis_evenement'), generer_url_ecrire("veille_edit","type=evenement&new=oui"), _DIR_VU_IMG_PACK."evenement-24.gif", "creer.gif", false)
			. icone_horizontale(_T('vu:raccourcis_publication'), generer_url_ecrire("veille_edit","type=publication&new=oui"), _DIR_VU_IMG_PACK."publication-24.gif", "creer.gif", false)
		);

	
//	
// Contenu central
//

	echo debut_droite('', true);
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'veille_tous'),'data'=>''));

	// Liste des annonces
	echo afficher_objets('annonce',_T('vu:liste_annonces'), array("SELECT" => 'id_annonce, date, titre, statut', "FROM" => 'spip_vu_annonces AS annonces', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	// Liste des evenements
	echo afficher_objets('evenement',_T('vu:liste_evenements'), array("SELECT" => 'id_evenement, date, titre, statut', "FROM" => 'spip_vu_evenements AS evenements', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	// Liste des publications
	echo afficher_objets('publication',_T('vu:liste_publications'),	array("SELECT" => 'id_publication, date, titre, statut', "FROM" => 'spip_vu_publications AS publications', 'WHERE' => "statut='publie' OR statut='refuse' OR statut='prop'", 'ORDER BY' => "date DESC"),'',true);

	


	echo fin_gauche(), fin_page();
}
?>
