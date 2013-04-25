<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/gis?lang_cible=ru
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'Нет ни одной точки на карте',
	'aucun_objet' => 'Нет связанных объектов',

	// B
	'bouton_lier' => 'Связать точку',
	'bouton_supprimer_gis' => 'Удалить точку',
	'bouton_supprimer_lien' => 'Удалить связь',

	// C
	'cfg_descr_gis' => 'Географическая Информационная Система (GIS).<br /><a href="http://www.spip-contrib.net/3887" class="spip_out">Документация</a>.',
	'cfg_inf_adresse' => 'Показываются дополнительные поля для ввода адреса (страна, город, область, адрес...)',
	'cfg_inf_bing' => 'Для использования карты Bing Aerial необходимо создать ключ  <a href=\'@url@\' class="spip_out">на сайте Bing</a>.',
	'cfg_inf_cloudmade' => 'Cette API nécessite une clé à créer sur <a href=\'@url@\' class="spip_out">le site de CloudMade</a>.', # NEW
	'cfg_inf_geocoder' => 'Включить функцию геопоиска (поиск точки на карте по адресу).',
	'cfg_inf_geolocaliser_user_html5' => 'Si le navigateur de l\'utilisateur le permet, son emplacement géographique approximatif est récupéré pour donner la position par défaut lors de la création d\'un point.', # NEW
	'cfg_inf_google' => 'Cette API nécessite une clé à créer sur <a href=\'@url@\' class="spip_out">le site de GoogleMaps</a>.', # NEW
	'cfg_inf_yandex' => 'Для работы с картой необходим API ключ. <a href=\'@url@\' class="spip_out"> Получить на сайте Yandex</a>.',
	'cfg_lbl_activer_objets' => 'Связывать карту с объектами:',
	'cfg_lbl_adresse' => 'Показать поля для адреса',
	'cfg_lbl_api' => 'Используемое API',
	'cfg_lbl_api_cloudmade' => 'CloudMade',
	'cfg_lbl_api_google' => 'Google Maps v2',
	'cfg_lbl_api_googlev3' => 'Google Maps v3',
	'cfg_lbl_api_key_bing' => 'Clé Bing', # NEW
	'cfg_lbl_api_key_cloudmade' => 'Clé CloudMade', # NEW
	'cfg_lbl_api_key_google' => 'Clé GoogleMaps', # NEW
	'cfg_lbl_api_key_yandex' => 'Yandex API ключ',
	'cfg_lbl_api_mapquest' => 'MapQuest',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_api_openlayers' => 'OpenLayers',
	'cfg_lbl_api_ovi' => 'Ovi Nokia',
	'cfg_lbl_api_yandex' => 'Yandex',
	'cfg_lbl_geocoder' => 'Geocoder', # NEW
	'cfg_lbl_geolocaliser_user_html5' => 'Centrer la carte sur l\'emplacement de l\'utilisateur à la création', # NEW
	'cfg_lbl_layer_defaut' => 'Couche par défaut', # NEW
	'cfg_lbl_layers' => 'Couches proposées', # NEW
	'cfg_lbl_maptype' => 'Тип карты',
	'cfg_lbl_maptype_carte' => 'Карта',
	'cfg_lbl_maptype_hybride' => 'Гибрид',
	'cfg_lbl_maptype_relief' => 'Рельеф',
	'cfg_lbl_maptype_satellite' => 'Спутник',
	'cfg_titre_gis' => 'GIS',

	// E
	'editer_gis_editer' => 'Изменить точку',
	'editer_gis_explication' => 'Cette page liste l\'ensemble des points géolocalisés du site.', # NEW
	'editer_gis_nouveau' => 'Новая точка на карте',
	'editer_gis_titre' => 'Les points géolocalisés', # NEW
	'erreur_recherche_pas_resultats' => 'Aucun point ne correspond à la recherche.', # NEW
	'erreur_xmlrpc_lat_lon' => 'La latitude et la longitude doivent être passées en argument', # NEW
	'explication_api_forcee' => 'L\'API est imposée par un autre plugin ou squelette.', # NEW
	'explication_import' => 'Importer un fichier au format GPX ou KML.', # NEW
	'explication_layer_forcee' => 'La couche est imposée par un autre plugin ou un squelette.', # NEW
	'explication_maptype_force' => 'Le fond cartographique est imposé par un autre plugin ou squelette.', # NEW

	// F
	'formulaire_creer_gis' => 'Создание новой точки :',
	'formulaire_modifier_gis' => 'Изменить точку :',

	// G
	'gis_pluriel' => 'Points géolocalisés', # NEW
	'gis_singulier' => 'Point géolocalisé', # NEW

	// I
	'icone_gis_tous' => 'Points géolocalisés', # NEW
	'info_1_gis' => 'Un point géolocalisé', # NEW
	'info_1_objet_gis' => '1 objet lié à ce point', # NEW
	'info_aucun_gis' => 'Aucun point géolocalisé', # NEW
	'info_aucun_objet_gis' => 'Aucun objet lié à ce point', # NEW
	'info_geolocalisation' => 'Géolocalisation', # NEW
	'info_id_objet' => 'N°',
	'info_liste_gis' => 'Points géolocalisés', # NEW
	'info_nb_gis' => '@nb@ points géolocalisés', # NEW
	'info_nb_objets_gis' => '@nb@ объектов связано с точкой',
	'info_numero_gis' => 'ID точки',
	'info_objet' => 'Объект',
	'info_recherche_gis_zero' => 'Aucun résultat pour « @cherche_gis@ ».', # NEW
	'info_supprimer_lien' => 'Убрать',
	'info_supprimer_liens' => 'Убрать все точки',
	'info_voir_fiche_objet' => 'Voir la fiche', # NEW

	// L
	'label_adress' => 'Адрес',
	'label_code_postal' => 'Индекс',
	'label_import' => 'Импорт',
	'label_inserer_modele_articles' => 'liés aux articles', # NEW
	'label_inserer_modele_articles_sites' => 'liés aux articles + sites', # NEW
	'label_inserer_modele_auteurs' => 'liés aux auteurs', # NEW
	'label_inserer_modele_centrer_auto' => 'Pas de centrage auto', # NEW
	'label_inserer_modele_centrer_fichier' => 'Ne pas centrer la carte sur les fichiers KLM/GPX', # NEW
	'label_inserer_modele_controle' => 'Cacher les contrôles', # NEW
	'label_inserer_modele_controle_type' => 'Cacher les types', # NEW
	'label_inserer_modele_description' => 'Описание',
	'label_inserer_modele_documents' => 'liés aux documents', # NEW
	'label_inserer_modele_echelle' => 'Масштаб',
	'label_inserer_modele_fullscreen' => 'Bouton plein écran', # NEW
	'label_inserer_modele_gpx' => 'Fichier GPX à superposer', # NEW
	'label_inserer_modele_hauteur_carte' => 'Высота карта',
	'label_inserer_modele_identifiant' => 'ID',
	'label_inserer_modele_identifiant_opt' => 'Identifiant (optionnel)', # NEW
	'label_inserer_modele_identifiant_placeholder' => 'id_gis', # NEW
	'label_inserer_modele_kml' => 'Fichier KML à superposer', # NEW
	'label_inserer_modele_kml_gpx' => 'id_document ou url', # NEW
	'label_inserer_modele_largeur_carte' => 'Ширина карты',
	'label_inserer_modele_limite' => 'Максимальное количество точек',
	'label_inserer_modele_localiser_visiteur' => 'Centrer sur le visiteur', # NEW
	'label_inserer_modele_mini_carte' => 'Mini carte de situation', # NEW
	'label_inserer_modele_molette' => 'Désactiver la molette', # NEW
	'label_inserer_modele_mots' => 'liés aux mots', # NEW
	'label_inserer_modele_objets' => 'Type de point(s)', # NEW
	'label_inserer_modele_point_gis' => 'point unique enregistré', # NEW
	'label_inserer_modele_point_libre' => 'point unique libre', # NEW
	'label_inserer_modele_points' => 'Спрятать точки',
	'label_inserer_modele_rubriques' => 'liés aux rubriques', # NEW
	'label_inserer_modele_sites' => 'liés aux sites', # NEW
	'label_inserer_modele_titre_carte' => 'Название карты',
	'label_pays' => 'Страна',
	'label_rechercher_address' => 'Искать по адресу',
	'label_rechercher_point' => 'Найти точку',
	'label_region' => 'Область',
	'label_ville' => 'Город',
	'lat' => 'Широта',
	'libelle_logo_gis' => 'Лого точки',
	'lien_ajouter_gis' => 'Добавить точку',
	'lon' => 'Долгота',

	// T
	'telecharger_gis' => 'Скачать в @format@ формате',
	'texte_ajouter_gis' => 'Ajouter un point géolocalisé', # NEW
	'texte_creer_associer_gis' => 'Créer et associer un point géolocalisé', # NEW
	'texte_creer_gis' => 'Créer un point géolocalisé', # NEW
	'texte_modifier_gis' => 'Modifier le point géolocalisé', # NEW
	'texte_voir_gis' => 'Показать точку на карте',
	'titre_bloc_creer_point' => 'Новая точка на карте',
	'titre_bloc_points_lies' => 'Связанные точки',
	'titre_bloc_rechercher_point' => 'Найти существующую точку',
	'titre_nombre_utilisation' => 'Une utilisation', # NEW
	'titre_nombre_utilisations' => '@nb@ utilisations', # NEW
	'titre_nouveau_point' => 'Новая точка',
	'titre_objet' => 'Название',

	// Z
	'zoom' => 'Zoom'
);

?>
