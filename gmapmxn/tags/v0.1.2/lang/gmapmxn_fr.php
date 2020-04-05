<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// langue / language = fr

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'api_mapstraction' => "Mapstraction",
	'api_mapstraction_desc' => "La librairie Mapstraction permet de manipuler des cartes de diff&eacute;rents fournisseurs. En contrepartie de cette ouverture, certaines fonctionnalit&eacute;s de GMap sont absentes, comme le regroupement des info-bulles ou la possibilit&eacute;s de g&eacute;rer l'affichage des fichiers KML. Les fonctionnalit&eacute;s ne sont pas homog&egrave;nes pour chaque fournisseurs.",
	'api_provider' => "Fournisseur du service de cartographie&nbsp;:",
	'api_provider_cloudmade' => "Cloudmade",
	'api_provider_google' => "Google Maps V2",
	'api_provider_googlev3' => "Google Maps V3",
	'api_provider_mapquest' => "MapQuest",
	'api_provider_microsoft' => "Microsoft Bing",
	'api_provider_openlayers' => "Open Layers",
	'api_provider_ovi' => "Ovi Nokia",
	'api_provider_yahoo' => "Yahoo! Maps",
	'api_provider_yandex' => "Yandex",
	
	// C
	'choix_overview_control' => "Aper&ccedil;u global",
	'choix_pan_control' => "D&eacute;placement de la carte",
	'choix_scale_control' => "Affichage de l'&eacute;chelle",
	'choix_types_control' => "Choix du type de carte",
	'choix_zoom_control' => "Zoom",
	'choix_zoom_control_none' => "Non",
	'choix_zoom_control_small' => "Petit",
	'choix_zoom_control_large' => "Grand",
	'controls_no_update' => "Vous devez enregistrer les modifications pour qu'elles soient visibles sur la carte.",
	'controls_special_info_cloudmade' => "nop", // code, do not translate
	'controls_special_info_google' => "L'API Google Maps ne distingue pas les commandes de zoom et de d&eacute;placement, le zoom contient donc les deux.",
	'controls_special_info_googlev3' => "L'API Google Maps ne distingue pas les commandes de zoom et de d&eacute;placement, le zoom contient donc les deux. L'affichage de l'&eacute;chelle ne fonctionne que si la &quot;grande&quot; commande de zoom est affich&eacute;e.",
	'controls_special_info_mapquest' => "nop", // code, do not translate
	'controls_special_info_microsoft' => "Le param&eacute;trage des commandes fonctionne tr&egrave;s peu avec ce fournisseur&nbsp;: on peut seulement afficher ou cacher le menu. Il n'y a donc aucune diff&eacute;rence entre la &quot;petite&quot; et la &quot;grande&quot; commande de zoom. L'&eacute;chelle est affich&eacute;e automatiquement si la commande de zoom est affich&eacute;e. IMPORTANT : Le positionnement des marqueurs n'est pas coh&eacute;rent par rapport aux autres fournisseurs, il semble que ce soit d&ucirc; &agrave; une erreur dans la version 6 de Bing que Microsoft n'a jamais corrig&eacute;e.",
	'controls_special_info_openlayers' => "La commande de zoom inclut le d&eacute;placement lorsqu'elle est en mode &quot;Grand&quot;, on ne peut donc pas la supprimer.",
	'controls_special_info_ovi' => "L'API ne propose pas deux tailles de zoom, on peut donc seulement l'afficher ou le cacher.",
	'controls_special_info_yahoo' => "L'&eacute;chelle apparait automatiquement quand la &quot;grande&quot; commande de zoom est affich&eacute;e.",
	'controls_special_info_yandex' => "nop", // code, do not translate

	// P
	'provider_caps' => "Possibilit&eacute;s offertes par ce fournisseur&nbsp;:",
	'provider_cap_markers' => "Marqueurs",
	'provider_cap_bubbles' => "Bulles d'information",
	'provider_cap_kml' => "Fichiers KML",
	'provider_cap_geocoder' => "Recherche par adresse",
	'provider_key' => "Ce fournisseur exige une clef d'enregistrement, veuillez la saisir ici&nbsp;:",

	// W
	'warning_cloudmade' => "nop", // code, do not translate
	'warning_google' => "nop", // code, do not translate
	'warning_googlev3' => "nop", // code, do not translate
	'warning_mapquest' => "nop", // code, do not translate
	'warning_microsoft' => "L'API Virtual Earth impose de centrer l'ancrage des marqueurs sur l'image.",
	'warning_openlayers' => "nop", // code, do not translate
	'warning_ovi' => "Il y a une erreur lors de la r&eacute;cup&eacute;ration du clic sur la carte, l'interface de g&eacute;olocalisation ne peut donc pas &ecirc;tre op&eacute;rationnelle avec ce fournisseur.",
	'warning_yahoo' => "L'API Yahoo impose que l'ancrage des marqueurs soit en bas &agrave; gauche de l'image. Par ailleurs, l'interface de g&eacute;olocalisation souffre d'un probl&egrave;me de dimensionnement.",
	'warning_yandex' => "Ce fournisseur n'a pas &eacute;t&eacute; test&eacute; &agrave; cause de la &quot;barri&egrave;re de la langue&quot;... SVP, signalez les erreurs.",

);

?>