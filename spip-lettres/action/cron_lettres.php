<?php


	set_time_limit(0);


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


	include_spip('lettres_fonctions');


	/**
	 * action_cron_lettres
	 *
	 * @author Pierre BASSON
	 **/
	function action_cron_lettres() {

		$code = $_GET['code'];
		
		if (($GLOBALS['meta']['spip_lettres_envois_recurrents'] == 'oui') and (strcmp($GLOBALS['meta']['spip_lettres_cron'], $code) == 0)) {
			$res = sql_select('RC.id_rubrique, R.titre, R.descriptif, R.texte', 'spip_rubriques_crontabs AS RC INNER JOIN spip_rubriques AS R ON R.id_rubrique=RC.id_rubrique');
			while ($arr = sql_fetch($res)) {
				$lettre = new lettre();
				$lettre->id_rubrique	= $arr['id_rubrique'];
				$lettre->titre			= $arr['titre'];
				$lettre->descriptif		= $arr['descriptif'];
				$lettre->texte			= $arr['texte'];
				$lettre->statut			= 'brouillon';
				$lettre->enregistrer();
				// programmer un nouvel envoi
				$lettre->enregistrer_statut('envoi_en_cours');
			}
		}

	}
	

?>