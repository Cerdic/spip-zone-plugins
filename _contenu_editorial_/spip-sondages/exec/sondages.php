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


	include_spip('inc/sondages_fonctions');
	include_spip('inc/sondages_admin');
 	include_spip('inc/presentation');


	/**
	 * exec_sondages
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_sondages() {

		sondages_verifier_droits();

		debut_page(_T('sondages:sondages'), "naviguer", "sondages");


#		debut_gauche();


#		debut_raccourcis();
#		fin_raccourcis();


    	debut_droite();
		echo "<br />";
		gros_titre(_T('sondages:sondages'));
		echo '<br />';
	

		fin_page();

	}


?>