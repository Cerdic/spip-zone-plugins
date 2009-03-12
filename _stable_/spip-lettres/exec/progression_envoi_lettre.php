<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('lettres_fonctions');


	function exec_progression_envoi_lettre() {

		if (!autoriser('editer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$lettre = new lettre($_GET['id_lettre']);

		echo '<div style="padding: 10px; border: 1px solid red; margin-bottom: 15px; background: #fff; color: red; font-weight: bold; text-align: center;">';
		echo _T('lettresprive:aide_lettres_envoi_en_cours');
		$nb_envois = $lettre->calculer_nb_envois();
		if ($nb_envois) {
			$pourcentage = intval($lettre->calculer_nb_envois('envoye') / $nb_envois * 100);
			echo '<br />';
			echo http_img_pack("jauge-vert.gif", ' ', 'height="10" width="'.($pourcentage * 2).'"');
			echo http_img_pack("jauge-rouge.gif", ' ', 'height="10" width="'.((100 - $pourcentage) * 2).'"');
			echo '&nbsp;'.$pourcentage.'%&nbsp;';
			echo http_img_pack("searching.gif", ' ', '');
		}
		echo '</div>';
		

	}


?>