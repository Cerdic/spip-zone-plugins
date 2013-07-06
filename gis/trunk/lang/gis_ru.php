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
	'cfg_inf_cloudmade' => 'Для использования карты необходимо создать ключ  <a href=\'@url@\' class="spip_out">на сайте CloudMade</a>.',
	'cfg_inf_geocoder' => 'Включить функцию геопоиска (поиск точки на карте по адресу).',
	'cfg_inf_geolocaliser_user_html5' => 'Новая карта центрируется по расположению пользователя ( если позволяет его браузер).',
	'cfg_inf_google' => 'Для работы с картой необходим API ключ, который  можно создать на <a href=\'@url@\' class="spip_out">сайте  GoogleMaps</a>.',
	'cfg_inf_yandex' => 'Для работы с картой необходим API ключ. <a href=\'@url@\' class="spip_out"> Получить на сайте Yandex</a>.',
	'cfg_lbl_activer_objets' => 'Связывать карту с объектами:',
	'cfg_lbl_adresse' => 'Показать поля для адреса',
	'cfg_lbl_api' => 'Используемое API',
	'cfg_lbl_api_cloudmade' => 'CloudMade',
	'cfg_lbl_api_google' => 'Google Maps v2',
	'cfg_lbl_api_googlev3' => 'Google Maps v3',
	'cfg_lbl_api_key_bing' => 'API ключ  Bing',
	'cfg_lbl_api_key_cloudmade' => 'API ключ CloudMade ',
	'cfg_lbl_api_key_google' => 'API ключ  GoogleMaps',
	'cfg_lbl_api_key_yandex' => 'Yandex API ключ',
	'cfg_lbl_api_mapquest' => 'MapQuest',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_api_openlayers' => 'OpenLayers',
	'cfg_lbl_api_ovi' => 'Ovi Nokia',
	'cfg_lbl_api_yandex' => 'Yandex',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Центрировать карту по месту расположения пользователя, создавшего карту',
	'cfg_lbl_layer_defaut' => 'Слой по умолчанию',
	'cfg_lbl_layers' => 'Предложенные слои',
	'cfg_lbl_maptype' => 'Тип карты',
	'cfg_lbl_maptype_carte' => 'Карта',
	'cfg_lbl_maptype_hybride' => 'Гибрид',
	'cfg_lbl_maptype_relief' => 'Рельеф',
	'cfg_lbl_maptype_satellite' => 'Спутник',
	'cfg_titre_gis' => 'GIS',

	// E
	'editer_gis_editer' => 'Изменить точку',
	'editer_gis_explication' => 'Список всех точек, используемых на вашем сайте.',
	'editer_gis_nouveau' => 'Создать точку',
	'editer_gis_titre' => 'Точки на карте',
	'erreur_geocoder' => 'Aucun résultat pour votre recherche :', # NEW
	'erreur_recherche_pas_resultats' => 'Нет точек, соответствующих поисковому запросу.',
	'erreur_xmlrpc_lat_lon' => 'В качестве аргумента должна быть указанна долгота и широта',
	'explication_api_forcee' => 'На API накладывается другой плагин или шаблон.',
	'explication_import' => 'Импортировать GPX или KML файл.',
	'explication_layer_forcee' => 'На слой накладывается другой плагин или шаблон.',
	'explication_maptype_force' => 'На базовую карту накладывается другой плагин или шаблон.',

	// F
	'formulaire_creer_gis' => 'Создание новой точки :',
	'formulaire_modifier_gis' => 'Изменить точку :',

	// G
	'gis_pluriel' => 'Точки на карте',
	'gis_singulier' => 'Точка на карте',

	// I
	'icone_gis_tous' => 'Точки на карте',
	'info_1_gis' => 'Точка на карте',
	'info_1_objet_gis' => '1 материал связан с точкой',
	'info_aucun_gis' => 'Нет точек на карте',
	'info_aucun_objet_gis' => 'У точки нет связанных материалов',
	'info_geolocalisation' => 'Расположение (Geolocation)',
	'info_id_objet' => 'N°',
	'info_liste_gis' => 'Точки на карте',
	'info_nb_gis' => '@nb@ точек на карте',
	'info_nb_objets_gis' => '@nb@ объектов связано с точкой',
	'info_numero_gis' => 'ID точки',
	'info_objet' => 'Объект',
	'info_recherche_gis_zero' => 'Ничего не найдено по запросу « @cherche_gis@ ».',
	'info_supprimer_lien' => 'Убрать',
	'info_supprimer_liens' => 'Убрать все точки',
	'info_voir_fiche_objet' => 'Перейти на страницу',

	// L
	'label_adress' => 'Адрес',
	'label_code_postal' => 'Индекс',
	'label_import' => 'Импорт',
	'label_inserer_modele_articles' => 'связано со статьями',
	'label_inserer_modele_articles_sites' => 'связано с авторами и сайтами',
	'label_inserer_modele_auteurs' => 'связано с авторами',
	'label_inserer_modele_centrer_auto' => 'Без автоматического центрирования',
	'label_inserer_modele_centrer_fichier' => 'Не центрировать карту по KLM/GPX файлу.',
	'label_inserer_modele_controle' => 'Спрятать управление картой',
	'label_inserer_modele_controle_type' => 'Спрятать выбор типа карты',
	'label_inserer_modele_description' => 'Описание',
	'label_inserer_modele_documents' => 'связано с документами',
	'label_inserer_modele_echelle' => 'Масштаб',
	'label_inserer_modele_fullscreen' => 'Переход в полноэкранный режим',
	'label_inserer_modele_gpx' => 'GPX файл для наложения',
	'label_inserer_modele_hauteur_carte' => 'Высота карта',
	'label_inserer_modele_identifiant' => 'ID',
	'label_inserer_modele_identifiant_opt' => 'ID (не обязательно)',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => 'KML файл для наложения',
	'label_inserer_modele_kml_gpx' => 'id_document или url',
	'label_inserer_modele_largeur_carte' => 'Ширина карты',
	'label_inserer_modele_limite' => 'Максимальное количество точек',
	'label_inserer_modele_localiser_visiteur' => 'Центрировать по посетителю',
	'label_inserer_modele_mini_carte' => 'Мини карта',
	'label_inserer_modele_molette' => 'Отключить прокрутку колесиком мышки',
	'label_inserer_modele_mots' => 'связано с ключами',
	'label_inserer_modele_objets' => 'Виды точек',
	'label_inserer_modele_point_gis' => 'записана одиночная точка',
	'label_inserer_modele_point_libre' => 'свободная точка',
	'label_inserer_modele_points' => 'Спрятать точки',
	'label_inserer_modele_rubriques' => 'связано с разделами',
	'label_inserer_modele_sites' => 'связано с сайтами',
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
	'texte_ajouter_gis' => 'Добавить точку на карте',
	'texte_creer_associer_gis' => 'Создать точку и связать ее',
	'texte_creer_gis' => 'Создать точку',
	'texte_modifier_gis' => 'Изменить точку',
	'texte_voir_gis' => 'Показать точку на карте',
	'titre_bloc_creer_point' => 'Новая точка на карте',
	'titre_bloc_points_lies' => 'Связанные точки',
	'titre_bloc_rechercher_point' => 'Найти существующую точку',
	'titre_nombre_utilisation' => 'Используется 1 раз',
	'titre_nombre_utilisations' => 'используется @nb@ раз',
	'titre_nouveau_point' => 'Новая точка',
	'titre_objet' => 'Название',

	// Z
	'zoom' => 'Zoom'
);

?>
