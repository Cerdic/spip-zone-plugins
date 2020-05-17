<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/gis?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'Kein Punkt',
	'aucun_objet' => 'Kein Objekt',

	// B
	'bouton_annuler_title' => 'Bearbeitung abbrechen, Änderungen werden nicht übernommen.',
	'bouton_enregistrer_title' => 'Änderungen speichern.',
	'bouton_lier' => 'Diesen Punkt verknüpfen',
	'bouton_supprimer_gis' => 'Diesen Punkt endgültig löschen',
	'bouton_supprimer_lien' => 'Diese Verknüpfung löschen',

	// C
	'cfg_descr_gis' => 'Geoinformationssystem<br /><a href="http://contrib.spip.net/4189" class="spip_out">zur Dokumentation</a>.', # MODIF
	'cfg_inf_adresse' => 'Adressfelder anzeigen (Land, Stadt, Region, Adresse ...)',
	'cfg_inf_bing' => 'Der Layer Bing Aerial benötigt einen Schlüssel zu erzeugen auf der  <a href=\'@url@\' class="spip_out">Bing Website</a>.',
	'cfg_inf_geocoder' => 'Geocoding aktivieren (Suche nach einer Adresse, Anzeige von Adressen zu den Koordinaten).',
	'cfg_inf_geolocaliser_user_html5' => 'Wenn es der Browser des Nutzers erlaubt, wird sein ungefährer Standort als Voreinstellung für die Position von Punkten genommen.',
	'cfg_inf_google' => 'Diese API benötigt einen Schlüssel. Zu erzeugen auf der <a href=\'@url@\' class="spip_out">der GoogleMaps Seite </a>.',
	'cfg_inf_styles' => 'Zusätzliche Felder für den Stil anzeigen (Farbe, Transparenz, Dicke...)',
	'cfg_lbl_activer_objets' => 'Geotargeting für folgende Inhalte aktivieren:',
	'cfg_lbl_adresse' => 'Adressfelder anzeigen',
	'cfg_lbl_api' => 'Karten-API ',
	'cfg_lbl_api_key_bing' => 'Bing Schlüssel',
	'cfg_lbl_api_key_google' => 'GoogleMaps Schlüssel',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Karte beim Anlegen auf den Standort des Nutzers zentrieren.',
	'cfg_lbl_layer_defaut' => 'Standardlayer',
	'cfg_lbl_layers' => 'Vorgeschlagene Layer',
	'cfg_lbl_maptype' => 'Kartentyp',
	'cfg_lbl_styles' => 'Felder für Stil anzeigen',
	'cfg_titre_gis' => 'GIS Einstellungen',

	// E
	'editer_gis_editer' => 'Punkt bearbeiten',
	'editer_gis_nouveau' => 'Neuen Punkt anlegen',
	'editer_gis_titre' => 'GIS Punkte',
	'erreur_geocoder' => 'Ihre Suche ergab kein Ergebnis:',
	'erreur_recherche_pas_resultats' => 'Kein Punkt passt zu Ihrer Suche.',
	'erreur_xmlrpc_lat_lon' => 'Länge und Breite müssen als Argumente übergeben werden.',
	'explication_api_forcee' => 'Die API wird durch ein Plugin oder Skelett erzwungen.',
	'explication_color' => 'Linienfarbe im CSS-Format (Standardwert: #0033FF)',
	'explication_fillcolor' => 'Füllfarbe im CSS-Format (Standardwert: vererbt von der Linienfarbe)',
	'explication_fillopacity' => 'Transparenz der Füllung von 0 bis 1 (Standardwert: 0.2)',
	'explication_import' => 'Eine Datei im GPX oder KML - Format importieren.',
	'explication_layer_forcee' => 'Der Layer wird durch ein anderes Plugin oder Skelett erzwungen.',
	'explication_maptype_force' => 'Der Kartentyp wird durch ein anderes Plugin oder Skelett erzwungen.',
	'explication_opacity' => 'Transparenz der Linien von 0 bis 1 (Standardwert: 0.5)',
	'explication_weight' => 'Linienstärke (Standardwert: 5)',

	// F
	'formulaire_creer_gis' => 'GIS-Punkt anlegen:',
	'formulaire_modifier_gis' => 'Diesen GIS-Punkt bearbeiten:',

	// G
	'gis_pluriel' => 'GIS-Punkte',
	'gis_singulier' => 'GIS Punkt',

	// I
	'icone_gis_tous' => 'GIS Punkte',
	'info_1_gis' => 'Ein GIS Punkt',
	'info_1_objet_gis' => '1 verknüpftes Objekt zu diesem Punkt',
	'info_aucun_gis' => 'Kein GIS Punkt',
	'info_aucun_objet_gis' => 'Kein verknüpftes Objekt zu diesem Punkt',
	'info_geolocalisation' => 'Geolokalisation',
	'info_id_objet' => 'Nr',
	'info_liste_gis' => 'GIS Punkte',
	'info_nb_gis' => '@nb@ GIS Punkte',
	'info_nb_objets_gis' => '@nb@ verknüpfte Objekte zu diesem Punkt',
	'info_numero_gis' => 'Punkt Nummer',
	'info_objet' => 'Objekt',
	'info_recherche_gis_zero' => 'Kein Resultat für « @cherche_gis@ ».',
	'info_supprimer_lien' => 'Entfernen',
	'info_supprimer_liens' => 'Alle Punkte entfernen',
	'info_voir_fiche_objet' => 'Seite anzeigen',

	// L
	'label_adress' => 'Adresse',
	'label_code_pays' => 'Länderkürzel',
	'label_code_postal' => 'Postleitzahl',
	'label_color' => 'Farbe',
	'label_departement' => 'Bundesland',
	'label_fillcolor' => 'Füllfarbe',
	'label_fillopacity' => 'Transparenz der Füllung',
	'label_import' => 'Importieren',
	'label_inserer_modele_articles' => 'verknüpft mit Artikeln',
	'label_inserer_modele_articles_sites' => 'verknüpft mit Artikeln und Websites',
	'label_inserer_modele_auteurs' => 'verknüpft mit Autoren',
	'label_inserer_modele_centrer_auto' => 'Nicht automatisch Zentrieren',
	'label_inserer_modele_centrer_fichier' => 'Karte nicht auf KLM/GPX-Dateien zentrieren',
	'label_inserer_modele_controle' => 'Bedienelemente verbergen',
	'label_inserer_modele_controle_type' => 'Typen verbergen',
	'label_inserer_modele_description' => 'Beschreibung',
	'label_inserer_modele_documents' => 'verknüpft mit Dokumenten',
	'label_inserer_modele_echelle' => 'Maßstab',
	'label_inserer_modele_fullscreen' => 'Gesamter Bildschirm Button',
	'label_inserer_modele_gpx' => 'GPX Datei Overlay',
	'label_inserer_modele_hauteur_carte' => 'Höhe der Karte',
	'label_inserer_modele_identifiant' => 'ID',
	'label_inserer_modele_identifiant_opt' => 'ID (optional)',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => 'KML-Datei als Overlay',
	'label_inserer_modele_kml_gpx' => 'id_document oder URL',
	'label_inserer_modele_largeur_carte' => 'Breite der Karte',
	'label_inserer_modele_limite' => 'Maximale Anzahl Punkte',
	'label_inserer_modele_localiser_visiteur' => 'Auf den Besucher zentrieren',
	'label_inserer_modele_mini_carte' => 'Mini-Karte der Lage',
	'label_inserer_modele_molette' => 'Scrollrad desaktovieren',
	'label_inserer_modele_mots' => 'verknüpft mit Schlagwörtern',
	'label_inserer_modele_objets' => 'Punktekategorie',
	'label_inserer_modele_point_gis' => 'Einzelpunkt angelegt',
	'label_inserer_modele_point_libre' => 'Einzelpunkt',
	'label_inserer_modele_points' => 'Punkte verbergen',
	'label_inserer_modele_rubriques' => 'verknüpft mit Rubriken',
	'label_inserer_modele_sites' => 'verknüpft mit Websites',
	'label_inserer_modele_titre_carte' => 'Titel der Karte',
	'label_opacity' => 'Transparenz',
	'label_pays' => 'Land',
	'label_rechercher_address' => 'Adresse suchen',
	'label_rechercher_point' => 'Punkt suchen',
	'label_region' => 'Region',
	'label_ville' => 'Stadt',
	'label_weight' => 'Stärke',
	'lat' => 'Breite',
	'libelle_logo_gis' => 'LOGO DES PUNKTS',
	'lien_ajouter_gis' => 'Deisen Punkt hinzufügen',
	'lon' => 'Länge',

	// M
	'message_limite_atteinte' => 'Es gibt mehr GIS-Punkte als das aktuelle Anzeigelimit.<br /> Wenn Sie alle anzeigen wollen <a href="@url@">hier klicken</a>.',

	// O
	'onglet_carte' => 'Karte',
	'onglet_liste' => 'Liste',

	// P
	'placeholder_geocoder' => 'Eine Adresse, eine Stadt, ein Land, eine Sehenswürdigkeit...',

	// T
	'telecharger_gis' => 'Im Format @format@ herunterladen',
	'texte_ajouter_gis' => 'GIS Punkt hinzufügen',
	'texte_creer_associer_gis' => 'GIS Punkt anlegen und hinzufügen',
	'texte_creer_gis' => 'GIS Punkt anlegen',
	'texte_modifier_gis' => 'Diesen GIS Punkt bearbeiten',
	'texte_voir_gis' => 'GIS Punkt anzeigen',
	'titre_bloc_creer_point' => 'Neuen Punkt verknüpfen',
	'titre_bloc_points_lies' => 'Verknüpfte Punkte',
	'titre_bloc_rechercher_point' => 'Punkt suchen',
	'titre_limite_atteinte' => 'Anzeigelimit erreicht (@limite@)',
	'titre_nombre_utilisation' => 'Eine Verwendung',
	'titre_nombre_utilisations' => '@nb@ Verwendungen',
	'titre_nouveau_point' => 'Neuer Punkt',
	'titre_objet' => 'Ttiel',
	'toolbar_actions_title' => 'Zeichnen abbrechen',
	'toolbar_buttons_circle' => 'Einen Kreis zeichnen',
	'toolbar_buttons_marker' => 'Einen Punkt zeichnen',
	'toolbar_buttons_polygon' => 'Ein Polygon zeichnen',
	'toolbar_buttons_polyline' => 'Eine Linie zeichnen',
	'toolbar_buttons_rectangle' => 'Ein Rechteck zeichnen',
	'toolbar_edit_buttons_edit' => 'Objekt bearbeiten',
	'toolbar_edit_buttons_editdisabled' => 'Kein bearbeitbares Objekt',
	'toolbar_edit_buttons_remove' => 'Objekt löschen',
	'toolbar_edit_buttons_removedisabled' => 'Kein löschbares Objekt',
	'toolbar_edit_handlers_edit_tooltip_subtext' => 'Abbrechen klicken um die Änderungen zu verwerfen.',
	'toolbar_edit_handlers_edit_tooltip_text' => 'Die Marker verschieben, um das Objekt zu ändern.',
	'toolbar_edit_handlers_remove_tooltip_text' => 'Zum Löschen auf das Objekt klicken',
	'toolbar_handlers_marker_tooltip_start' => 'Klicken um Marker zu setzen',
	'toolbar_handlers_polygon_tooltip_cont' => 'Klicken um das Polygonzeichnen   fortzusetzen',
	'toolbar_handlers_polygon_tooltip_end' => 'Auf den ersten Punkt klicken um das Polygon abzuschliessen',
	'toolbar_handlers_polygon_tooltip_start' => 'Klicken um mit dem Polygon zu beginnen',
	'toolbar_handlers_polyline_tooltip_cont' => 'Klicken um die Linie fortzusetzen',
	'toolbar_handlers_polyline_tooltip_end' => 'Letzten Punkt klicken um Linie abzuschließen',
	'toolbar_handlers_polyline_tooltip_start' => 'Klicken um Linie zu beginnen',
	'toolbar_handlers_rectangle_tooltip_start' => 'Klicken und ziehen um ein Rechteck zu zeichnen',
	'toolbar_handlers_simpleshape_tooltip_end' => 'Maustaste loslassen um das Design abzuschliessen',
	'toolbar_undo_text' => 'Letzten Punkt löschen',
	'toolbar_undo_title' => 'Letzten Punkt löschen',

	// Z
	'zoom' => 'Zoom'
);
