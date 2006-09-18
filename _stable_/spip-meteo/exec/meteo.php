<?php


	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


 	include_spip('inc/presentation');
 	include_spip('meteo_fonctions');


	/**
	 * exec_meteo
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_meteo() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		debut_page(_T('meteo:meteo'), "naviguer", "meteo");

		debut_gauche();

		debut_raccourcis();
		icone_horizontale(_T('meteo:ajouter_une_meteo'), generer_url_ecrire("meteo_edition","new=oui"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', 'creer.gif');
		fin_raccourcis();

    	debut_droite();
		echo '<br />';
		meteo_afficher_meteos(_T('meteo:liste_des_meteos'), array("FROM" => 'spip_meteo', 'ORDER BY' => "ville"));

		fin_page();

	}


?>