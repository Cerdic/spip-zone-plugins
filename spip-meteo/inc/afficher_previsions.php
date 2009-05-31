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


	function inc_afficher_previsions($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('arial1', 30), array('arial2'), array('arial1'), array('arial1'), array('arial1'));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_prevision_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_METEO.'prive/images/meteo-24.png');
	}


	function afficher_prevision_boucle($row, $own) {
		global $tableau_meteo;
		
		$vals = '';

		$date		= $row['date'];
		$id_temps	= $row['id_temps'];
		$minima		= $row['minima'];
		$maxima		= $row['maxima'];
	
		$vals[] = icone_meteo($tableau_meteo[$id_temps]);
		
		$vals[] = nom_jour($date).' '.affdate_jourcourt($date);
	
		$vals[] = _T('meteo:meteo_'.$tableau_meteo[$id_temps]);

		if ($minima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $minima.'&nbsp;&deg;C';

		if ($maxima == 'NA')
			$vals[] = _T('meteo:temperature_inconnue');
		else
			$vals[] = $maxima.'&nbsp;&deg;C';

		return $vals;
	}


?>