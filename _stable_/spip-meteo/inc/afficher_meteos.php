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


	function inc_afficher_meteos($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('meteoprive:meteo');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('arial1', 12), array('arial2'), array('arial1', 200), array('arial1', 80), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_meteo_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_METEO.'prive/images/meteo-24.png');
	}


	function afficher_meteo_boucle($row, $own) {
		$vals = '';

		$id_meteo	= $row['id_meteo'];
		$titre		= $row['ville'];
		$code		= $row['code'];
		$statut		= $row['statut'];
	
		switch ($statut) {
			case 'publie':
				$puce = 'verte';
				break;
			case 'en_erreur':
				$puce = 'orange-anim';
				break;
		}
		$puce = "puce-$puce.gif";
		$vals[] = http_img_pack($puce, '', ' width="8" height="8" style="margin: 1px;"');

		$s = "<a href='" . generer_url_ecrire("meteo","id_meteo=$id_meteo") . "'>";
		$s .= typo(ucfirst($titre));
		$s .= "</a>";
		$vals[] = $s;
	
		if ($statut == 'en_erreur')
			$vals[] = "<font color='red'>"._T('meteo:probleme_de_recuperation_du_flux')." </font>";
		else
			$vals[] = "&nbsp;";

		$vals[] = $code;

		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_meteo</b>";

		return $vals;
	}


?>