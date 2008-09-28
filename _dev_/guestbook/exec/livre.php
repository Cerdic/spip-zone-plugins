<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
	 include_spip('inc/presentation');


	function exec_livre() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		debut_page(_T('livre:livre'), "naviguer", "livre");

		debut_gauche();

		debut_raccourcis();
		icone_horizontale(_T('livre:creer_tables_mysql'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/sql.png', 'creer.gif');
		icone_horizontale(_T('livre:effacer_les_tables'), generer_url_ecrire("efface"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/nosql.png', 'creer.gif');
		icone_horizontale(_T('livre:repondre_aux_messages'), generer_url_ecrire("livre_edition"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png', 'creer.gif');
		fin_raccourcis();

    	debut_droite();
		echo '<br />';
		

		fin_page();

	}


?>