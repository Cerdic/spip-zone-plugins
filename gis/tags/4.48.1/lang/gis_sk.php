<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/gis?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'Žiaden bod',
	'aucun_objet' => 'Žiaden objekt',

	// B
	'bouton_lier' => 'Prepojiť tento bod',
	'bouton_supprimer_gis' => 'Natrvalo odstrániť tento bod',
	'bouton_supprimer_lien' => 'Odstrániť tento odkaz',

	// C
	'cfg_descr_gis' => 'Geografický informačný systém.<br /><a href="http://contrib.spip.net/4189" class="spip_out">Prejsť na dokumentáciu.</a>', # MODIF
	'cfg_inf_adresse' => 'Zobrazí ďalšie polia adresy (krajinu, mesto, štát, adresu a pod.)',
	'cfg_inf_bing' => 'Vrstva Bing Aerial si vyžaduje, aby ste <a href=\'@url@\' class="spip_out">na stránke vyhľadávača Bing</a> vytvorili kľúč.',
	'cfg_inf_geocoder' => 'Aktivovať funkciu geokódera (vyhľadávanie z jednej adresy, zistenie adresy zo súradníc).',
	'cfg_inf_geolocaliser_user_html5' => 'Ak to povoľuje prehliadač používateľa, na určenie predvolenej polohy pri vytváraní nového bodu sa ukladá približná geografická poloha používateľa.',
	'cfg_inf_google' => 'Táto aplikácia potrebuje kľúč, ktorý si treba vytvoriť na <a href=\'@url@\' class="spip_out">stránke GoogleMaps.</a>', # MODIF
	'cfg_lbl_activer_objets' => 'Aktivovať geolokalizáciu obsahu:',
	'cfg_lbl_adresse' => 'Zobraziť polia adresy',
	'cfg_lbl_api' => 'Geolokačná API',
	'cfg_lbl_api_key_bing' => 'Kľúč pre Bing',
	'cfg_lbl_api_key_google' => 'Kľúč GoogleMaps',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Pri vytváraní vycentrujte mapu na polohe používateľa',
	'cfg_lbl_layer_defaut' => 'Predvolená vrstva',
	'cfg_lbl_layers' => 'Navrhované vrstvy',
	'cfg_lbl_maptype' => 'Základná mapa',
	'cfg_titre_gis' => 'GIS', # MODIF

	// E
	'editer_gis_editer' => 'Upraviť tento bod',
	'editer_gis_nouveau' => 'Vytvoriť nový bod',
	'editer_gis_titre' => 'Geolokalizované body',
	'erreur_geocoder' => 'Žiaden výsledok k vášmu vyhľadávaniu:', # MODIF
	'erreur_recherche_pas_resultats' => 'Vyhľadávania sa netýka žiaden bod.',
	'erreur_xmlrpc_lat_lon' => 'Zemepisná šírka a dĺžka musia byť odovzdané ako parameter',
	'explication_api_forcee' => 'Túto aplikáciu používa iný zásuvný modul alebo iná šablóna.',
	'explication_import' => 'Nahrá súbor vo formáte GPX alebo KML.',
	'explication_layer_forcee' => 'Vrstvu zaviedol iný zásuvný modul alebo iná šablóna.',
	'explication_maptype_force' => 'Základnú mapu si vyžaduje iný zásuvný modul alebo šablóna.',

	// F
	'formulaire_creer_gis' => 'Vytvoriť geolokalizovaný bod:',
	'formulaire_modifier_gis' => 'Upraviť geolokalizovaný bod:',

	// G
	'gis_pluriel' => 'Geolokalizované body',
	'gis_singulier' => 'Geolokalizovaný bod',

	// I
	'icone_gis_tous' => 'Geolokalizované body',
	'info_1_gis' => 'Jeden geolokalizovaný bod',
	'info_1_objet_gis' => '1 objekt prepojený s týmto bodom',
	'info_aucun_gis' => 'Žiaden geolokalizovaný bod',
	'info_aucun_objet_gis' => 'Žiaden objekt prepojený s týmto bodom',
	'info_geolocalisation' => 'Geolokalizácia',
	'info_id_objet' => 'Č.',
	'info_liste_gis' => 'Geolokalizované body',
	'info_nb_gis' => '@nb@ geolokalizovaných bodov',
	'info_nb_objets_gis' => '@nb@ objektov prepojených s týmto bodom',
	'info_numero_gis' => 'Bod číslo',
	'info_objet' => 'Objekt',
	'info_recherche_gis_zero' => 'Žiadne výsledky pre "@cherche_gis@".',
	'info_supprimer_lien' => 'Zrušiť prepojenie',
	'info_supprimer_liens' => 'Zrušiť všetky body',
	'info_voir_fiche_objet' => 'Prejsť na stránku',

	// L
	'label_adress' => 'Adresa',
	'label_code_pays' => 'Kód krajiny',
	'label_code_postal' => 'PSČ',
	'label_departement' => 'Kraj',
	'label_import' => 'Nahrať',
	'label_inserer_modele_articles' => 'prepojené s článkami',
	'label_inserer_modele_articles_sites' => 'prepojené s článkami a stránkami',
	'label_inserer_modele_auteurs' => 'prepojené s autormi',
	'label_inserer_modele_centrer_auto' => 'Nevystreďovať automaticky',
	'label_inserer_modele_centrer_fichier' => 'Nedávať mapu na súbory KLM alebo GPX',
	'label_inserer_modele_controle' => 'Schovať ovládacie prvky',
	'label_inserer_modele_controle_type' => 'Skryť typy',
	'label_inserer_modele_description' => 'Opis',
	'label_inserer_modele_documents' => 'prepojené s dokumentmi',
	'label_inserer_modele_echelle' => 'Mierka',
	'label_inserer_modele_fullscreen' => 'Tlačidlo "Na celú obrazovka"',
	'label_inserer_modele_gpx' => 'Prekryť súborom GPX',
	'label_inserer_modele_hauteur_carte' => 'Výška mapy',
	'label_inserer_modele_identifiant' => 'Identifikátor',
	'label_inserer_modele_identifiant_opt' => 'Identifikátor (nepovinné)',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => 'Prekryť súborom KML',
	'label_inserer_modele_kml_gpx' => 'id_document alebo url',
	'label_inserer_modele_largeur_carte' => 'Šírka mapy',
	'label_inserer_modele_limite' => 'Maximálny počet bodov',
	'label_inserer_modele_localiser_visiteur' => 'Zacieliť na návštevníka',
	'label_inserer_modele_mini_carte' => 'Malá situačná mapa',
	'label_inserer_modele_molette' => 'Vypnúť koliesko',
	'label_inserer_modele_mots' => 'prepojené so slovami',
	'label_inserer_modele_objets' => 'Typ bodov',
	'label_inserer_modele_point_gis' => 'jeden zaregistrovaný bod',
	'label_inserer_modele_point_libre' => 'jeden voľný bod',
	'label_inserer_modele_points' => 'Schovať body',
	'label_inserer_modele_rubriques' => 'prepojené s rubrikami',
	'label_inserer_modele_sites' => 'prepojené so stránkami',
	'label_inserer_modele_titre_carte' => 'Názov mapy',
	'label_pays' => 'Krajina',
	'label_rechercher_address' => 'Vyhľadať adresu',
	'label_rechercher_point' => 'Vyhľadať bod',
	'label_region' => 'Región (kraj)',
	'label_ville' => 'Mesto',
	'lat' => 'Zemepisná šírka',
	'libelle_logo_gis' => 'LOGO BODU',
	'lien_ajouter_gis' => 'Pridať tento bod',
	'lon' => 'Zemepisná dĺžka',

	// T
	'telecharger_gis' => 'Stiahnuť vo formáte @format@',
	'texte_ajouter_gis' => 'Pridať geolokalizovaný bod',
	'texte_creer_associer_gis' => 'Vytvoriť a prepojiť geolokalizovaný bod',
	'texte_creer_gis' => 'Vytvoriť geolokalizovaný bod',
	'texte_modifier_gis' => 'Upraviť geolokalizovaný bod',
	'texte_voir_gis' => 'Zobraziť geolokalizovaný bod',
	'titre_bloc_creer_point' => 'Prepojiť nový bod',
	'titre_bloc_points_lies' => 'Prepojené body',
	'titre_bloc_rechercher_point' => 'Vyhľadať bod',
	'titre_nombre_utilisation' => 'Jedno použitie',
	'titre_nombre_utilisations' => '@nb@ použití',
	'titre_nouveau_point' => 'Nový bod',
	'titre_objet' => 'Názov',
	'toolbar_actions_title' => 'Zrušiť trasu',
	'toolbar_buttons_marker' => 'Nakresliť bod',
	'toolbar_buttons_polygon' => 'Nakresliť mnohouholník',
	'toolbar_buttons_polyline' => 'Nakresliť čiaru',
	'toolbar_handlers_marker_tooltip_start' => 'Ak chcete vložiť značku, kliknite sem',
	'toolbar_handlers_polygon_tooltip_cont' => 'Kliknite, ak chcete pokračovať v kreslení mnohouholníka',
	'toolbar_handlers_polygon_tooltip_end' => 'Kliknite na prvý bod, aby bol mnohouholník uzavretý',
	'toolbar_handlers_polygon_tooltip_start' => 'Ak chcete začať kresliť mnohouholník, kliknite sem',
	'toolbar_handlers_polyline_tooltip_cont' => 'Ak chcete pokračovať v kreslení čiary, kliknite sem',
	'toolbar_handlers_polyline_tooltip_end' => 'Kliknite na posledný bod, aby bola čiara ukončená',
	'toolbar_handlers_polyline_tooltip_start' => 'Ak chcete začať kresliť čiaru, kliknite sem',
	'toolbar_undo_text' => 'Vymazať posledný bod',
	'toolbar_undo_title' => 'Vymazať posledný nakreslený bod',

	// Z
	'zoom' => 'Lupa'
);
