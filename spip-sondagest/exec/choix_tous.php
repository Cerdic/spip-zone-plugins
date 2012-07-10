<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('sondages_fonctions');


	function exec_choix_tous() {

		if (!autoriser('editer', 'sondages')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$sondage = new sondage($_GET['id_sondage']);

		echo afficher_objets('choix_sondage', _T('sondagesprive:choix'), array('FROM' => 'spip_choix', 'WHERE' => 'id_sondage='.intval($sondage->id_sondage), 'ORDER BY' => 'ordre'));	
		echo http_img_pack("searching.gif", ' ', ' id="searching-choix" style="position: absolute; top: 3px; right: 3px; visibility: hidden;"');
		
	}


?>