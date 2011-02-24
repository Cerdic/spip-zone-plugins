<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

'geoportail'	=> 'G&eacute;oportail',

'cle'				=> 'Cl&eacute; d\'utilisation',
'cles'				=> 'Cl&eacute;s d\'utilisation',
'geoportail_key'	=> 'Pour pouvoir utiliser l\'API G&eacute;oportail, vous devez d\'abord vous inscrire et avoir une cl&eacute; d\'utilisation sur le site <a href="http://api.ign.fr/geoportail/" title="API Geoportail">http://api.ign.fr/geoportail/</a>.',
'cle_yahoo'				=> 'Cl&eacute; d\'utilisation YAHOO',
'geoportail_yahoo_key'	=> 'Pour pouvoir utiliser les service Yahoo! Map, vous devez d\'abord vous inscrire et avoir une cl&eacute; d\'utilisation sur le site <a href="http://developer.yahoo.com/maps/" title="API Yahoo! Map">http://developer.yahoo.com/maps/</a>.',
'cle_google'			=> 'Google Maps',
'geoportail_google_key'	=> 'Le plugin utilise l\'API Google Maps v.3. Celle-ci ne n&eacute;cessite aucune cl&eacute; d\'utilisation. Si vous affichez des cartes Google, vous devez cependant adh&eacute;rer aux conditions g&eacute;n&eacute;rales d\'utilisation de Google Maps : <a href="http://code.google.com/intl/fr/apis/maps/terms.html" title="API Google Maps">http://code.google.com/intl/fr/apis/maps/terms.html</a>.',
'cle_osm'				=> 'OpenStreetMap',
'geoportail_osm_key'	=> 'Le plugin vous permet d\'afficher les cartes du projet OpenStreetMap (<a href="http://wiki.openstreetmap.org/wiki/Slippy_Map" title="OpenStreetMap">http://wiki.openstreetmap.org/</a>).',
'osm_layers'			=> 'Serveurs disponibles :',
'osm_affiche'			=> 'Afficher / Charger',
'osm_osm'				=> 'Mapnik',
'osm_tah'				=> 'Tiles&#064;Home (<a href=\'http://tah.openstreetmap.org\'>http://tah.openstreetmap.org</a>)',
'osm_mquest'			=> 'MapQuest (<a href=\'http://www.mapquest.com/\'>http://www.mapquest.com/</a>)',
'geoportail_print'	=> '<b>Attention :</b> vous devez adh&eacute;rer aux conditions g&eacute;n&eacute;rales d\'utilisation (CGU) de l\'API. En particulier, l\'impression des cartes g&eacute;oportail n\'est autoris&eacute;e que dans le cadre d\'un <i>usage documentaire</i>.<br/>Le plugin active par d&eacute;faut l\'impression des cartes g&eacute;oportail. Vous devez le d&eacute;sactiver dans le fichier /css/geoportail.css dans le cas contraire...', 
'geoportail_services'=> 'Permettre l\'ajout de G&eacute;oservices.',
'geoportail_objet'	=> 'Types d\'objets &agrave; g&eacute;or&eacute;f&eacute;rencer',
'geoobjet_info'		=> 'S&eacute;lectionner les objets pour lesquels vous voulez g&eacute;rer un g&eacute;or&eacute;f&eacute;rencement. <br/><i>En v.2, SPIP peut r&eacute;cup&eacute;rer automatiquement la position des fichers g&eacute;otagg&eacute;, les GPX et les KML.</i>',
'info_documents_auto'	=> 'Extraire depuis le fichier',
'geoportail_sysref'	=> 'Systh&egrave;me de saisie',
'geoportail_sysinfo'=> 'Vous pouvez choisir un systh&egrave;me de coordonn&eacute;es pour la saisie dans les formulaires.<br/>Dans ce cas, vous devrez entrer les deux coordon&eacute;es dans le systh&egrave;me choisi, elles seront transform&eacute;es &agrave; la vol&eacute;e en g&eacute;ographique avant d\'&ecirc;tre envoy&eacute;es au formulaire.<br/>Vous pourrez n&eacute;anmoins continuer &agrave; saisir en g&eacute;od&eacute;sique.',
'system_code'		=> ',IGNF:LAMBE,IGNF:LAMB93',
'system_name'		=> 'vide,Lambert 2e,Lambert 93',
'options'			=> 'Options',

'rgc'			=> 'R&eacute;pertoire G&eacute;ographique des Communes',
'rgc_info'		=> 'Vous pouvez utiliser un r&eacute;pertoire de communes pour associer le num&eacute;ro de d&eacute;partement et de commune aux positions de vos objets.<br/>Vous devez d&eacute;poser les fichiers des r&eacute;pertoires de communes dans le dossier /rgc du plugin.',
'no_rgc'		=> 'Aucun fichier gazetteer disponible...',
'bouton_installer'	=> 'Installer',
'bad_rgc'		=> 'Impossible de charger le r&eacute;pertoire !',
'rgc_by_'			=> '',

'import_rgc'	=> 'Importation du r&eacute;f&eacute;rentiel g&eacute;ographique',
'import_erreur'	=> 'Impossible d\'importer le RG !',
'import_double'	=> 'Le RG est d&eacute;j&agrave; charg&eacute; !',
'import_fin'	=> 'Le r&eacute;f&eacute;rentiel s\'est correctement import&eacute;.',

'rgc_info_ign'		=> 'Le RGC&reg; IGN est sous copyright IGN. Tous les usages de ces fichiers sont autoris&eacute;s, &agrave; l\'exclusion de toute exploitation commerciale.<br/>Pour l\'exploitation commerciale des fichiers, une autorisation pr&eacute;alable doit &ecirc;tre demand&eacute;e aupr&egrave;s du r&eacute;seau commercial de l\'IGN. <br/><i>Vous devez mentionner que votre site utilise le RGC&reg; IGN</i> (<a href="http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5323862">www.ign.fr</a>)',
'rgc_use_ign'		=> 'Vous utilisez le RGC-IGN.',
'rgc_par_ign'		=> 'Powered by <a href=\'http://www.ign.fr\'>IGN-RGC&reg;</a>',
'rgc_info_geonames'	=> 'Le RGC Geonames est issu de la base GeoNames sous licence Creative Commons Attribution 3.0. <br/><i>Vous devez mentionner que votre site utilise la base Geonames</i> (<a href="http://www.geonames.org/">www.geonames.org</a>)',
'rgc_use_geonames'	=> 'Vous utilisez Geonames.',
'rgc_par_geonames'	=> 'Powered by <a href=\'http://www.geonames.org\'>Geonames</a>',

'config'		=> 'Configuration',

// Correspondance code => nom de la zone Geoportail :
'zone'		=> 'Zone g&eacute;ographique',
'fxx'		=> 'France m&eacute;tropolitaine',
'atf'		=> 'Terres Art. Australes',
'glp'		=> 'Guadeloupe',
'guf'		=> 'Guyane',
'mtq'		=> 'Martinique',
'myt'		=> 'Mayotte',
'ncl'		=> 'Nouvelle Cal&eacute;donie',
'pyf'		=> 'Polyn&eacute;sie Fran&ccedil;aise',
'reu'		=> 'R&eacute;union',
'spm'		=> 'St Pierre et Miquelon',
'wlf'		=> 'Wallis et Futuna',
'anf'		=> 'Antilles Française',
'crz'		=> 'Crozet',
'eue'		=> 'Union Européenne',
'ker'		=> 'Kerguelen',
'sba'		=> 'Saint-Barthélémy',
'sma'		=> 'Saint-Martin',
'asp'		=> 'Saint-Paul-Amsterdam (non encore en ligne)',

// Code des zones Geoportail :
'tzone'		=> '"FXX","ATF","GLP","GUF","MTQ","MYT","NCL","PYF","REU","SPM","WLF","SBA","SMA","CRZ","KER"', 

// Gestion des projections courantes
'proj'		=> 'Projections',
'tproj'		=> '"IGNF:LAMB93","IGNF:LAMBE","IGN:RGF93G","IGNF:LAMB1","IGNF:LAMB2","IGNF:LAMB3","IGNF:LAMB4"',
'tprojtxt'	=> '"Lambert 93","Lambert IIe","Reseau geodesique francais 1993","Lambert I","Lambert II","Lambert III","Lambert IV"',

'numero'	=> 'SERVICE NUM&Eacute;RO',
'geoservice'	=> 'G&eacute;oService',
'geoservices'	=> 'G&eacute;oServices',
'no_service'	=> 'Pas de g&eacute;oservice disponible...',
'info_geoservice'	=> 'Vous pouvez ajouter vos propres services cartographiques sur les cartes de votre site.<br/><i>Aujourd\'hui, seuls les services WMS et G&eacute;oportail ont &eacute;t&eacute;s impl&eacute;ment&eacute;.</i>',
'info_geoportail_service'	=> 'Les services <b>G&eacute;oportail</b> permettent de regrouper plusieurs couches G&eacute;oportail en une seule ou d\'enlever certaines couches qu\'on ne veut pas voir appar&icirc;tre (statut=poubelle)',
'statut'		=> 'Statut',
'logo_spip'		=> 'LOGO DES COUCHES SPIP',
'logo_info'		=> 'Vous pouvez d&eacute;finir ci-dessus un logo pour les couches affich&eacute;es par SPIP.',
'logo_service'	=> 'LOGO DU SERVICE',
'voir_services'	=> 'Liste des services',
'icone_modifier_service'	=> 'Modifier ce service',
'geoservice_nom'	=> '<b>Nom du service</b> [Obligatoire]',
'geoservice_type'	=> '<b>Type du service</b>',
'geoservice_url'	=> '<b>Url du service</b> [Obligatoire]',
'geoservice_map'	=> 'Map du service',
'geoservice_layers'	=> 'Liste des couches du service (layers)',
'geoservice_format'	=> 'Format du service',
'niveau'		=> 'Niveau d\'affichage',
'minzoom'		=> 'Zoom min',
'maxzoom'		=> 'Zoom max',
'opacity'		=> 'Opacit&eacute;',
'visibility'	=> 'Visibilit&eacute;',
'geoservice_extent'	=> 'Extension du service',
'logo'			=> 'Association de logo (<i>les logos de m&ecirc;me nom seront associ&eacute;s</i>)',
'link'			=> 'Lien vers le service',
'geoservice_descriptif'	=> 'Descritif du service',
'icone_ajouter_service'	=> 'Ajouter un nouveau service',

'dans_rubrique'		=> 'dans la rubrique',
'toutes_rubriques'	=> 'dans tout le site',

'lien_direct'	=> 'Lien direct',
'info_lien'		=> 'Acc&egrave;s directe &agrave; cette page',
'lien_page'		=> 'Lien direct &grave; la page',
'envoyer_ami'	=> 'Envoyer &agrave; un ami...',
'lien_mail'		=> 'Bonjour,\n<votre message>\n\nLien :',

'mes_coord'			=> 'Mes coordonn&eacute;es',
'geoposition'	=> 'Coordonn&eacute;es g&eacute;ographiques',
'georef'		=> 'G&eacute;or&eacute;f&eacute;rencer',
'bouton_supprimer'	=> 'Suppr.',
'info_supprimer'	=> 'Supprimer',
'bouton_centrer'	=> 'Placer au centre de la carte',
'bouton_geocode'	=> 'Rechercher une adresse',
'verrouiller'		=> 'Verrouiller la destination',
'centrer_doc'	=> 'Utiliser le g&eacute;opositionnement du document',
'centrer_art'	=> 'Utiliser le g&eacute;opositionnement de l&acute;article',
'lon'			=> 'Longitude',
'lat'			=> 'Latitude',

'pas_autoriser'	=> 'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; modifier cet objet !',

'chercher'			=> 'Rechercher',
'selectionner'		=> 'S&eacute;lectionner la commune dans la liste',
'chercher_commune'	=> 'Chercher une commune ou un code postal',
'no_result'			=> 'Impossible de trouver cette destination',
'titre_erreur'		=> 'ERREUR',

'geo_document'	=> 'G&eacute;or&eacute;f&eacute;rencer un document',
'info_numero_document'	=> 'DOCUMENT NUM&Eacute;RO&nbsp;:',

'geoarticles'	=> 'G&eacute;or&eacute;f&eacute;rencement des articles',
'geoauteurs'	=> 'G&eacute;or&eacute;f&eacute;rencement des auteurs',

'icone_geoportail' => 'G&eacute;oportail'

);


?>