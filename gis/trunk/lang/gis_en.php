<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/gis?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'No point',
	'aucun_objet' => 'No object',

	// B
	'bouton_lier' => 'Link this point',
	'bouton_supprimer_gis' => 'Delete this point permanently',
	'bouton_supprimer_lien' => 'Remove this link',

	// C
	'cfg_descr_gis' => 'Geographic Information System.<br /><a href="http://contrib.spip.net/4189" class="spip_out">Link to the documentation</a>.',
	'cfg_inf_adresse' => 'Displays additional address fields (country, city, state, address ...)',
	'cfg_inf_bing' => 'The Bing Aerial layer needs a key you can create on <a href=\'@url@\' class="spip_out">the Bing website</a>.',
	'cfg_inf_geocoder' => 'Enable geocoder functions (search from an address, recovery of the address from the coordinates).',
	'cfg_inf_geolocaliser_user_html5' => 'If the user’s browser allows it, its approximate geographic location is retrieved to give the default position when creating a new point.',
	'cfg_inf_google' => 'This API needs a key you can create on <a href=\'@url@\' class="spip_out">the GoogleMaps website</a>.', # MODIF
	'cfg_lbl_activer_objets' => 'Enable geotagging of content:',
	'cfg_lbl_adresse' => 'Show address fields',
	'cfg_lbl_api' => 'Geolocation API',
	'cfg_lbl_api_key_bing' => 'Bing key',
	'cfg_lbl_api_key_google' => 'GoogleMaps API key',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Center the map on the location of the user at the creation step',
	'cfg_lbl_layer_defaut' => 'Default layer',
	'cfg_lbl_layers' => 'Proposed layers',
	'cfg_lbl_maptype' => 'Base map',
	'cfg_titre_gis' => 'GIS configuration',

	// E
	'editer_gis_editer' => 'Edit this point',
	'editer_gis_nouveau' => 'Create a new point',
	'editer_gis_titre' => 'The location-based points',
	'erreur_geocoder' => 'No results for your search:', # MODIF
	'erreur_recherche_pas_resultats' => 'No point corresponds to the searched text.',
	'erreur_xmlrpc_lat_lon' => 'Latitude and longitude should be set as arguments',
	'explication_api_forcee' => 'The is imposed by another plugin or skeleton.',
	'explication_import' => 'Import a file in GPX or KML format.',
	'explication_layer_forcee' => 'The layer is imposed by another plugin or skeleton.',
	'explication_maptype_force' => 'The base map is imposed by another plugin or skeleton.',

	// F
	'formulaire_creer_gis' => 'Create a new location-based point:',
	'formulaire_modifier_gis' => 'Modify the location-based point:',

	// G
	'gis_pluriel' => 'Location-based points',
	'gis_singulier' => 'Location-based point',

	// I
	'icone_gis_tous' => 'Location-based points',
	'info_1_gis' => 'A location-based point',
	'info_1_objet_gis' => '1 object linked to that point',
	'info_aucun_gis' => 'No location-based point',
	'info_aucun_objet_gis' => 'No object linked to that point',
	'info_geolocalisation' => 'Geolocation',
	'info_id_objet' => 'N°',
	'info_liste_gis' => 'Location-based points',
	'info_nb_gis' => '@nb@ location-based points',
	'info_nb_objets_gis' => '@nb@ objects linked to that point',
	'info_numero_gis' => 'Point number',
	'info_objet' => 'Object',
	'info_recherche_gis_zero' => 'No result for « @cherche_gis@ ».',
	'info_supprimer_lien' => 'Unlink',
	'info_supprimer_liens' => 'Unlink all the points',
	'info_voir_fiche_objet' => 'Go to page',

	// L
	'label_adress' => 'Address',
	'label_code_pays' => 'Country code',
	'label_code_postal' => 'Postal code',
	'label_departement' => 'Department',
	'label_import' => 'Import',
	'label_inserer_modele_articles' => 'linked to articles',
	'label_inserer_modele_articles_sites' => 'linked to articles + websites',
	'label_inserer_modele_auteurs' => 'linked to authors',
	'label_inserer_modele_centrer_auto' => 'No automatic centring',
	'label_inserer_modele_centrer_fichier' => 'Do not center the map on the KLM/GPX files',
	'label_inserer_modele_controle' => 'Hide controls',
	'label_inserer_modele_controle_type' => 'Hide types',
	'label_inserer_modele_description' => 'Description',
	'label_inserer_modele_documents' => 'linked to documents',
	'label_inserer_modele_echelle' => 'Scale',
	'label_inserer_modele_fullscreen' => 'Full screen button',
	'label_inserer_modele_gpx' => 'GPX file to overlay',
	'label_inserer_modele_hauteur_carte' => 'Map height',
	'label_inserer_modele_identifiant' => 'ID',
	'label_inserer_modele_identifiant_opt' => 'ID (optionnal)',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => 'KML files to overlay',
	'label_inserer_modele_kml_gpx' => 'id_document or url',
	'label_inserer_modele_largeur_carte' => 'Map width',
	'label_inserer_modele_limite' => 'Maximum number of points',
	'label_inserer_modele_localiser_visiteur' => 'Center on the visitor',
	'label_inserer_modele_mini_carte' => 'Mini situation map',
	'label_inserer_modele_molette' => 'Disable the scroll wheel',
	'label_inserer_modele_mots' => 'Linked to words',
	'label_inserer_modele_objets' => 'Point(s) category',
	'label_inserer_modele_point_gis' => 'single point recorded',
	'label_inserer_modele_point_libre' => 'free single point',
	'label_inserer_modele_points' => 'Hide points',
	'label_inserer_modele_rubriques' => 'linked to sections',
	'label_inserer_modele_sites' => 'linked to websites',
	'label_inserer_modele_titre_carte' => 'Map title',
	'label_pays' => 'Country',
	'label_rechercher_address' => 'Search for an address',
	'label_rechercher_point' => 'Search for a point',
	'label_region' => 'Region',
	'label_ville' => 'Town',
	'lat' => 'Latitude',
	'libelle_logo_gis' => 'POINT\\’S LOGO',
	'lien_ajouter_gis' => 'Add this point',
	'lon' => 'Longitude',

	// T
	'telecharger_gis' => 'Download in @format@ format',
	'texte_ajouter_gis' => 'Add a location-based point',
	'texte_creer_associer_gis' => 'Create and link a location-based point',
	'texte_creer_gis' => 'Create a location-based point',
	'texte_modifier_gis' => 'Modify the location-based point',
	'texte_voir_gis' => 'Show the location-based point',
	'titre_bloc_creer_point' => 'Link a new point',
	'titre_bloc_points_lies' => 'Linked points',
	'titre_bloc_rechercher_point' => 'Search for a point',
	'titre_nombre_utilisation' => 'One use',
	'titre_nombre_utilisations' => '@nb@ uses',
	'titre_nouveau_point' => 'New point',
	'titre_objet' => 'Title',
	'toolbar_actions_title' => 'Cancel the drawing',
	'toolbar_buttons_marker' => 'Plot a point',
	'toolbar_buttons_polygon' => 'Draw a polygon',
	'toolbar_buttons_polyline' => 'Draw a line',
	'toolbar_handlers_marker_tooltip_start' => 'Click to set marker',
	'toolbar_handlers_polygon_tooltip_cont' => 'Click to continue drawing the polygon',
	'toolbar_handlers_polygon_tooltip_end' => 'Click the first point to close the polygon',
	'toolbar_handlers_polygon_tooltip_start' => 'Click to start drawing the polygon',
	'toolbar_handlers_polyline_tooltip_cont' => 'Click to continue to draw the line',
	'toolbar_handlers_polyline_tooltip_end' => 'Click the last point to end line',
	'toolbar_handlers_polyline_tooltip_start' => 'Click to start drawing the line',
	'toolbar_undo_text' => 'Delete last point',
	'toolbar_undo_title' => 'Delete last point drawn',

	// Z
	'zoom' => 'Zoom'
);
