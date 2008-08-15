<?php


	/**
	 * SPIP-M&eacute;t&eacute;o : pr&eacute;visions m&eacute;t&eacute;o dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Art&eacute;go http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	$GLOBALS[$GLOBALS['idx_lang']] = array(

		# partie publique
		'meteo' => "M&eacute;t&eacute;o",

		// Liste des resumes meteo
		'meteo_1'	=> 'Pluie',
		'meteo_2'	=> 'Pluie',
		'meteo_3'	=> 'Orage',
		'meteo_4'	=> 'Orage',
		'meteo_5'	=> 'Pluie',
		'meteo_6'	=> 'Neige',
		'meteo_7'	=> 'Verglas',
		'meteo_8'	=> 'Pluie',
		'meteo_9'	=> 'Pluie',
		'meteo_10'	=> 'Pluie',
		'meteo_11'	=> 'Pluie',
		'meteo_12'	=> 'Pluie',
		'meteo_13'	=> 'Neige',
		'meteo_14'	=> 'Neige',
		'meteo_15'	=> 'Neige',
		'meteo_16'	=> 'Neige',
		'meteo_17'	=> 'Orage',
		'meteo_18'	=> 'Neige',
		'meteo_19'	=> 'Brouillard',
		'meteo_20'	=> 'Brouillard',
		'meteo_21'	=> 'Brouillard',
		'meteo_22'	=> 'Brouillard',
		'meteo_23'	=> 'Vent',
		'meteo_24'	=> 'Vent',
		'meteo_25'	=> 'Vent',
		'meteo_26'	=> 'Nuages',
		'meteo_27'	=> 'Pleine-lune et nuages',
		'meteo_28'	=> 'Soleil et nuages',
		'meteo_29'	=> 'Pleine-lune et nuages &eacute;pars',
		'meteo_30'	=> 'Soleil et nuages &eacute;pars',
		'meteo_31'	=> 'Pleine-lune',
		'meteo_32'	=> 'Soleil',
		'meteo_33'	=> 'Pleine-lune et nuages &eacute;pars',
		'meteo_34'	=> 'Soleil et nuages &eacute;pars',
		'meteo_35'	=> 'Orage',
		'meteo_36'	=> 'Soleil',
		'meteo_37'	=> 'Orage',
		'meteo_38'	=> 'Orage',
		'meteo_39'	=> 'Pluie',
		'meteo_40'	=> 'Pluie',
		'meteo_41'	=> 'Neige',
		'meteo_42'	=> 'Neige',
		'meteo_43'	=> 'Neige',
		'meteo_44'	=> 'Soleil et nuages &eacute;pars',
		'meteo_45'	=> 'Pluie',
		'meteo_46'	=> 'Neige',
		'meteo_47'	=> 'Orage',
		'meteo_na'	=> 'Inconnu',

		// Liste des unites suivant le systeme choisi
		'unite_temperature_metrique' => '&deg;C',
		'unite_vitesse_metrique' => 'km/h',
		'unite_distance_metrique' => 'km',
		'unite_pression_metrique' => 'mbar',
		'unite_precipitation_metrique' => 'mm',
		'unite_humidite_metrique' => '%',
		'unite_angle_metrique' => '&deg;',
		'unite_temperature_standard' => '&deg;F',
		'unite_vitesse_standard' => 'mph',
		'unite_distance_standard' => 'miles',
		'unite_pression_standard' => 'pouces',
		'unite_precipitation_standard' => 'pouces',
		'unite_humidite_standard' => '%',
		'unite_angle_standard' => '&deg;',
		
		// Liste des directions du vent
		'direction_N' => 'nord',
		'direction_NNE' => 'nord nord-est',
		'direction_NE' => 'nord-est',
		'direction_ENE' => 'est nord-est',
		'direction_E' => 'est',
		'direction_ESE' => 'est sud-est',
		'direction_SE' => 'sud-est',
		'direction_SSE' => 'sud sud-est',
		'direction_S' => 'sud',
		'direction_SSW' => 'sud sud-ouest',
		'direction_SW' => 'sud-ouest',
		'direction_WSW' => 'ouest sud-ouest',
		'direction_W' => 'ouest',
		'direction_WNW' => 'ouest nord-ouest',
		'direction_NW' => 'nord-ouest',
		'direction_NNW' => 'nord nord-ouest',

		// 'meteo_pluie' => "Pluie",
		// 'meteo_orage' => "Orage",
		// 'meteo_neige' => "Neige",
		// 'meteo_verglas' => "Verglas",
		// 'meteo_brouillard' => "Brouillard",
		// 'meteo_vent' => "Vent",
		// 'meteo_nuages' => "Nuages",
		// 'meteo_lune' => "Pleine-lune",
		// 'meteo_lune-nuage' => "Pleine-lune et nuages &eacute;parses",
		// 'meteo_lune-nuages' => "Pleine-lune et nuages",
		// 'meteo_soleil' => "Soleil",
		// 'meteo_soleil-nuage' => "Soleil et nuages &eacute;parses",
		// 'meteo_soleil-nuages' => "Soleil et nuages",
		// 'meteo_inconnu' => "Inconnu",
		// 'meteo_NA' => "Inconnu",
		// 'temperature_inconnue' => "Temp&eacute;rature inconnue",
/* 		'ajouter_une_meteo' => "Ajouter une m&eacute;t&eacute;o",
		'numero_meteo' => "M&Eacute;T&Eacute;O NUM&Eacute;RO",
		'liste_des_meteos' => "Liste des m&eacute;t&eacute;os",
		'nouvelle_ville' => "Nouvelle ville",
		'editer_meteo' => "Editer les r&eacute;glages d'une m&eacute;t&eacute;o",
		'ville' => "Ville",
		'ville_note' => "Entrez le nom de la ville, sans accents. Vous devez &ecirc;tre connect&eacute;s &agrave; internet pour r&eacute;cup&eacute;rer le code m&eacute;t&eacute;o via le formulaire suivant.",
		'chercher' => "Chercher",
		'code_ville' => "Code de la ville",
		'enregistrer' => "Enregistrer",
		'retour_liste_meteo' => "Retour &agrave; la liste des m&eacute;t&eacute;os",
		'modifier_meteo' => "Modifier les r&eacute;glages de cette m&eacute;t&eacute;o",
		'action' => "Actions sur cette m&eacute;t&eacute;o",
		'action_aucune' => "Aucune",
		'action_poubelle' => "Supprimer",
		'changer' => "Changer",
		'texte_probleme_recuperation_flux' => "V&eacute;rifier la connexion &agrave; internet car le plugin n'arrive pas &agrave; r&eacute;cup&eacute;rer le flux m&eacute;t&eacute;o depuis Weather.com",
		'probleme_de_recuperation_du_flux' => "Impossible de r&eacute;cup&eacute;rer le flux m&eacute;t&eacute;o",
		'previsions_meteo' => 'Pr&eacute;vision m&eacute;t&eacute;o',
		'date_derniere_maj' => "Dernière mise &agrave; jour",
		'en_ligne' => "En ligne",
		'en_erreur' => "En erreur",
		'meteos_trouvees' => 'M&eacute;t&eacute;os trouv&eacute;es',
		

		'Z' => 'ZZzZZzzz'
 */
	);

?>
