<?php


	/**
	 * SPIP-Météo
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('meteo_fonctions');


	/**
	 * balise_TEMPS
	 *
	 * @param p est un objet SPIP
	 * @return float pourcentage
	 * @author Pierre Basson
	 **/
	function balise_TEMPS($p) {
		$_id_prevision = champ_sql('id_prevision',$p);
		$p->code = "meteo_calculer_temps($_id_prevision)";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * meteo_calculer_icone_temps
	 *
	 * @param int id_prevision
	 * @return string image correspondant à la prévision
	 * @author Pierre Basson
	 **/
	function meteo_calculer_temps($id_prevision) {
		global $tableau_meteo;
		$temps = sql_select("id_temps", "spip_previsions", "id_prevision=$id_prevision");
		if (sql_count($temps) == 0) {
			return '';
		} else {
			$arr = sql_fetch($temps);
			$id_temps = $arr['id_temps'];
			return $tableau_meteo[$id_temps];
		}
	}


?>