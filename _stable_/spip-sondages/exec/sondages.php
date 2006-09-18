<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


 	include_spip('inc/presentation');
	include_spip('sondages_fonctions');


	/**
	 * exec_sondages
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_sondages() {

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			$url = generer_url_ecrire('accueil');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		debut_page(_T('sondages:sondages'), "naviguer", "sondages");

		debut_gauche();

		sondages_afficher_statistiques_globales();
#		sondages_afficher_recherche('sondages');

		debut_raccourcis();
		sondages_afficher_raccourci_creer_sondage();
		fin_raccourcis();

    	debut_droite();
		echo '<br />';
		sondages_afficher_sondages(_T('sondages:sondages_hors_ligne'), _DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', 'non', 'brouillon', $recherche, 'position_hors_ligne');
		sondages_afficher_sondages(_T('sondages:sondages_en_attente'), _DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', 'oui', 'en_attente', $recherche, 'position_en_attente');
		sondages_afficher_sondages(_T('sondages:sondages_publies'), _DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', 'oui', 'publie', $recherche, 'position_publie');
		sondages_afficher_sondages(_T('sondages:sondages_termines'), _DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', 'oui', 'termine', $recherche, 'position_termine');

		fin_page();

	}


?>