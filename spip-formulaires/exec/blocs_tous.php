<?php


	/**
	 * SPIP-Formulaires
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
	include_spip('formulaires_fonctions');


	function exec_blocs_tous() {

		$id_formulaire	= intval($_GET['id_formulaire']);
		if (!autoriser('editer', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$formulaire = new formulaire($id_formulaire);

		echo $formulaire->afficher();
		echo http_img_pack("searching.gif", ' ', ' id="searching-formulaire" style="position: absolute; top: 3px; right: 3px; visibility: hidden;"');
		
	}


?>