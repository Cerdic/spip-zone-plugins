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


	/**
	 * meteo_convertir_fahrenheit_celsius
	 *
	 * @param int temperature en fahrenheit
	 * @return int temperature en celcius
	 * @author Pierre Basson
	 **/
	function meteo_convertir_fahrenheit_celsius($f) {
		return round( ($f - 32) * 5 / 9 );
	}


	/**
	 * meteo_convertir_celsius_fahrenheit
	 *
	 * @param int temperature en celcius
	 * @return int temperature en fahrenheit
	 * @author Pierre Basson
	 **/
	function meteo_convertir_celsius_fahrenheit($c) {
		return round( ($c * 9 / 5) + 32 );
	}


	/**
	 * filtre icone_meteo
	 *
	 * @param string temps
	 * @param string chemin
	 * @param string extension
	 * @return string image correspondant à la prévision
	 * @author Pierre Basson
	 **/
	function icone_meteo($temps, $chemin='', $extension="png") {
		if (empty($chemin))
			$chemin = _DIR_PLUGIN_METEO.'prive/images/';
		$img = $chemin.$temps.'.'.$extension;
		if (file_exists($img)) {
			include_spip('inc/logos');
			list ($h,$l) = taille_image($img);
			return '<img src="'.$img.'" alt="'.$temps.'" title="'.traduire_meteo($temps).'" width="'.$l.'" height="'.$h.'" />';
		} else {
			return '';
		}
		
	}


	/**
	 * filtre traduire_meteo
	 *
	 * @param string temps
	 * @return string traduction
	 * @author Pierre Basson
	 **/
	function traduire_meteo($temps) {
		if (empty($temps))
			return '';
		return _T('meteo:meteo_'.$temps);
	}


?>