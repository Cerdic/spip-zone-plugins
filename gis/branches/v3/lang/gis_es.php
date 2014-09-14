<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/gis?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'Ningún punto',
	'aucun_objet' => 'Ningún objeto',

	// B
	'bouton_lier' => 'Asociar este punto',
	'bouton_supprimer_gis' => 'Suprimir este punto de manera definitiva', # MODIF
	'bouton_supprimer_lien' => 'Suprimir está asociación',

	// C
	'cfg_descr_gis' => 'Sistema de Información Geográfica.<br /><a href="http://contrib.spip.net3887">Ir a la documentación</a>.', # MODIF
	'cfg_inf_adresse' => 'Mostrar campos adicionales de dirección (país, ciudad, región, dirección...)', # MODIF
	'cfg_inf_cloudmade' => 'Está API necesita una clave a generar en <a href=\'@url@\'>el sitio de CloudMade</a>.', # MODIF
	'cfg_inf_geocoder' => 'Activar las funciones del geocoder (búsqueda desde una dirección, recuperación de la dirección a partir de las coordenadas).', # MODIF
	'cfg_inf_geolocaliser_user_html5' => 'Si el navegador del usuario lo permite, su ubicación geográfica aproximada esta recuperada y será la posición por omisión cuando se crearó un nuevo punto.', # MODIF
	'cfg_inf_google' => 'Esta API necesita una clave a generar en <a href=\'@url@\'>el sitio de GoogleMaps</a>.', # MODIF
	'cfg_inf_yandex' => 'Esta API necesita una clave a generar en <a href=\'@url@\'>el sitio de Yandex</a>.', # MODIF
	'cfg_lbl_activer_objets' => 'Activer la géolocalisation sur les contenus :', # NEW
	'cfg_lbl_adresse' => 'Mostrar los campos de dirección',
	'cfg_lbl_api' => 'API de cartográfia',
	'cfg_lbl_api_cloudmade' => 'CloudMade',
	'cfg_lbl_api_google' => 'Google Maps v2',
	'cfg_lbl_api_googlev3' => 'Google Maps v3',
	'cfg_lbl_api_key_cloudmade' => 'Clave CloudMade', # MODIF
	'cfg_lbl_api_key_google' => 'Clave GoogleMaps', # MODIF
	'cfg_lbl_api_key_yandex' => 'Clave Yandex', # MODIF
	'cfg_lbl_api_mapquest' => 'MapQuest',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_api_openlayers' => 'OpenLayers',
	'cfg_lbl_api_ovi' => 'Ovi Nokia',
	'cfg_lbl_api_yandex' => 'Yandex',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Centrar el mapa sobre la ubicación del usuario a la creación', # MODIF
	'cfg_lbl_maptype' => 'Fond cartographique', # NEW
	'cfg_lbl_maptype_carte' => 'Carte', # NEW
	'cfg_lbl_maptype_hybride' => 'Hybride', # NEW
	'cfg_lbl_maptype_relief' => 'Relief', # NEW
	'cfg_lbl_maptype_satellite' => 'Satellite', # NEW
	'cfg_titre_gis' => 'GIS',

	// E
	'editer_gis_editer' => 'Modificar este punto',
	'editer_gis_explication' => 'Está página lista todos los puntos geolocalizados del sitio.', # MODIF
	'editer_gis_nouveau' => 'Crear un nuevo punto', # MODIF
	'editer_gis_titre' => 'Los puntos geolocalizados', # MODIF
	'erreur_recherche_pas_resultats' => 'Ningún punto corresponde a la búsqueda.', # MODIF
	'erreur_xmlrpc_lat_lon' => 'La latitude et la longitude doivent être passées en argument', # NEW
	'explication_api_forcee' => 'La API esta impuesta por un otro plugin o esqueleto.',
	'explication_maptype_force' => 'Le fond cartographique est imposé par un autre plugin ou squelette.', # NEW

	// F
	'formulaire_creer_gis' => 'Crear un punto geolocalizado:', # MODIF
	'formulaire_modifier_gis' => 'Modificar el punto geolocalizado:', # MODIF

	// G
	'gis_pluriel' => 'Points géolocalisés', # NEW
	'gis_singulier' => 'Point géolocalisé', # NEW

	// I
	'icone_gis_tous' => 'Puntos geolocalizados', # MODIF
	'info_1_gis' => 'Un point géolocalisé', # NEW
	'info_1_objet_gis' => '1 objet lié à ce point', # NEW
	'info_aucun_gis' => 'Aucun point géolocalisé', # NEW
	'info_aucun_objet_gis' => 'Aucun objet lié à ce point', # NEW
	'info_geolocalisation' => 'Géolocalisation', # NEW
	'info_id_objet' => 'N°', # MODIF
	'info_liste_gis' => 'Puntos geolocalizados',
	'info_nb_gis' => '@nb@ points géolocalisés', # NEW
	'info_nb_objets_gis' => '@nb@ objets liés à ce point', # NEW
	'info_numero_gis' => 'Punto número', # MODIF
	'info_objet' => 'Objeto',
	'info_recherche_gis_zero' => 'Aucun résultat pour « @cherche_gis@ ».', # NEW
	'info_supprimer_lien' => 'Suprimir la asociación', # MODIF
	'info_supprimer_liens' => 'Détacher tous les points', # MODIF
	'info_voir_fiche_objet' => 'Ver la ficha',

	// L
	'label_adress' => 'Dirección',
	'label_code_postal' => 'Código postal',
	'label_pays' => 'País',
	'label_rechercher_address' => 'Buscar una dirección',
	'label_rechercher_point' => 'Buscar un punto',
	'label_region' => 'Región', # MODIF
	'label_ville' => 'Ciudad',
	'lat' => 'Latitud',
	'libelle_logo_gis' => 'LOGOTIPO DEL PUNTO',
	'lien_ajouter_gis' => 'Ajouter ce point', # NEW
	'lon' => 'Longitud',

	// T
	'texte_ajouter_gis' => 'Ajouter un point géolocalisé', # NEW
	'texte_creer_associer_gis' => 'Créer et associer un point géolocalisé', # NEW
	'texte_creer_gis' => 'Créer un point géolocalisé', # NEW
	'texte_modifier_gis' => 'Modifier le point géolocalisé', # NEW
	'texte_voir_gis' => 'Voir le point géolocalisé', # NEW
	'titre_bloc_creer_point' => 'Asociar un nuevo punto',
	'titre_bloc_points_lies' => 'Puntos asociados',
	'titre_bloc_rechercher_point' => 'Buscar un punto',
	'titre_nombre_utilisation' => 'Una utilización',
	'titre_nombre_utilisations' => '@nb@ utilizaciones',
	'titre_nouveau_point' => 'Nouveau point', # NEW
	'titre_objet' => 'Titre', # NEW

	// Z
	'zoom' => 'Zoom'
);

?>
