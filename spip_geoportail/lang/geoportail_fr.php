<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

'geoportail'	=> 'G&eacute;oportail',

'cles'					=> 'Cl&eacute;s d\'utilisation',
'cle_info'				=> '<b>L\'utilisation des services cartographiques n&eacute;cessitent une cl&eacute; qui g&egrave;re les droits d\'acc&egrave;s aux donn&eacute;es au travers d\'un contrat et l\'acceptation des r&egrave;gles d\'utilisation du service.</b>
							<br/><br/><i>Veuillez vous reporter aux sites des diff&eacute;rents fournisseur pour les modalit&eacute;s d\'acc&egrave;s &agrave; leurs services.</i>',
'cle_geoportail'		=> 'Cl&eacute; d\'utilisation G&eacute;oportail',
'geoportail_key'		=> 'Consultez le site du G&eacute;oportail pour obtenir une cl&eacute; pour votre site : <a href="http://professionnels.ign.fr/api-web">professionnels.ign.fr</a>. Pour une cl&eacute; de d&eacute;veloppement (localhost) : <a href="http://api.ign.fr/" title="API Geoportail">api.ign.fr</a>.',
'local_js'				=> 'Utiliser le code javascript local.',
'gpp3'					=> 'Utiliser une cl&eacute; API v.2.0.0beta.',
'cle_bing'				=> 'Cl&eacute; d\'utilisation BING',
'geoportail_bing_key'	=> 'Consultez le site de Bing Map pour obtenir une cl&eacute; : <a href="http://bingmapsportal.com/" title="API BING Map">http://bingmapsportal.com/</a>.',
'cle_yahoo'				=> 'Cl&eacute; d\'utilisation YAHOO',
'geoportail_yahoo_key'	=> 'Consultez le site Yahoo! pour obtenir une cl&eacute; : <a href="http://developer.yahoo.com/maps/" title="API Yahoo! Map">http://developer.yahoo.com/maps/</a>.',
'cle_google'			=> 'Google Maps',
'geoportail_google_key'	=> 'Le plugin utilise l\'API Google Maps v.3. Celle-ci ne n&eacute;cessite aucune cl&eacute; d\'utilisation. Si vous affichez des cartes Google, vous devez cependant adh&eacute;rer aux conditions g&eacute;n&eacute;rales d\'utilisation de Google Maps : <a href="http://code.google.com/intl/fr/apis/maps/terms.html" title="API Google Maps">http://code.google.com/intl/fr/apis/maps/terms.html</a>.',
'cle_osm'				=> 'OpenStreetMap',
'geoportail_osm_key'	=> 'Le plugin vous permet d\'afficher les cartes du projet OpenStreetMap. OpenStreetMap est un ensemble de donn&eacute;es ouvertes, disponibles sous la licence <a href="http://www.openstreetmap.org/copyright" title="OpenStreetMap">Creative Commons paternit&eacute; – partage à l\'identique 2.0</a> (CC BY-SA).',
'osm_layers'			=> 'Serveurs disponibles :',
'osm_affiche'			=> 'Afficher / Charger',
'osm_osm'				=> 'Mapnik (<a href=\'http://mapnik.org/\'>http://mapnik.org/</a>)',
'osm_tah'				=> 'Tiles&#064;Home (<a href=\'http://wiki.openstreetmap.org/wiki/Tiles@home\'>http://wiki.openstreetmap.org/wiki/Tiles@home</a>)',
'osm_mquest'			=> 'MapQuest (<a href=\'http://www.mapquest.com/\'>http://www.mapquest.com/</a>)',
'geoportail_print'		=> '<b>Attention :</b> vous devez adh&eacute;rer aux conditions g&eacute;n&eacute;rales d\'utilisation (CGU) de l\'API. En particulier, l\'impression des cartes g&eacute;oportail n\'est autoris&eacute;e que dans le cadre d\'un <i>usage documentaire</i>.<br/>Le plugin active par d&eacute;faut l\'impression des cartes g&eacute;oportail. Vous devez le d&eacute;sactiver dans le fichier /css/geoportail.css dans le cas contraire...', 
'geoportail_api'		=> 'API G&eacuteoportail',
'geoportail_api_info'	=> 'Par d&eacute;faut, le plugin utilise le code javascript de l\'API sur le site du G&eacute;oportail sous licence BSD.
						Dans certains cas, pour s\'affranchir d\'effets de bords ou lors de d&eacute;boguage, il peut &ecirc;tre pr&eacute;f&eacute;rable d\'utiliser une version de l\'API en local. 
						<br/>Pour cela, placez les ressources de l\'API (<i>GeoportalExtended.js, etc.</i>) dans le dossier /js de votre squelette.',

'zoom_client'			=> 'Zoom Client',
'zoom_client_info'		=> 'Le technique du zoom client permet d\'afficher les tuiles du G&eacute;oportail aux r&eacute;solutions non suport&eacute;es par le service. 
							Il est alors possible d\'afficher les couches cartes et photo du G&eacute;oportail jusqu\'au zoom "Maison".
							<br/><i>Attention : cette technique est exp&eacute;rimentale, si vous constatez des probl&egrave;mes &agrave; l\'affichage des couches, d&eacute;activez cette option.</i>',
'zclient'				=> 'Activer le zoom client',

'geoportail_services'	=> 'Ajouter un menu G&eacute;oservices.',
'geoportail_defaut'		=> 'Affichage par d&eacute;faut',
'geoportail_provider'	=> 'Fournisseur par d&eacute;faut',
'geoprovider_info'		=> 'Choisissez le fournisseur de cartes et la zone g&eacute;ographique qui seront utilis&eacute;s dans l\'espace priv&eacute; et dans le site public lorsqu\'ils ne sont pas pr&eacute;cis&eacute;s.',
'geoportail_zone'		=> 'Zone g&eacute;ographique par d&eacute;faut',

'geoportail_popup'		=> 'Infobulles',
'popup_anchored'		=> 'postIt!',
'popup_framecloud'		=> 'Bulles (OpenLayers)',
'popup_spip'			=> 'Flottante',
'popup_qtip'			=> 'qTip',
'popup_jbubble'			=> 'Bulles-jQuery',
'popup_classic'			=> 'Classic',
'popup_ombre'			=> 'Ombres',
'popup_pense'			=> 'Pens&eacute;es',
'popup_black'			=> 'Noir',
'geopopup_info'			=> 'Sous quelle forme vont s\'afficher les infobulles sur les cartes&nbsp;?',
'geopopup_forme'		=> 'Forme des infobulles :',
'geoportail_hover'		=> 'Afficher une information au survol.',

'geoportail_objet'		=> 'Types d\'objets &agrave; g&eacute;or&eacute;f&eacute;rencer',
'geoobjet_info'			=> 'S&eacute;lectionner les objets pour lesquels vous voulez g&eacute;rer un g&eacute;or&eacute;f&eacute;rencement. <br/><i>En v.2, SPIP peut r&eacute;cup&eacute;rer automatiquement la position des fichers g&eacute;otagg&eacute;, les GPX et les KML.</i>',
'info_documents_auto'	=> 'Extraire depuis le fichier',
'geoportail_sysref'		=> 'Syst&egrave;me de saisie',
'geoportail_sysinfo'	=> 'Vous pouvez choisir un syst&egrave;me de coordonn&eacute;es pour la saisie dans les formulaires.<br/>Dans ce cas, vous devrez entrer les deux coordon&eacute;es dans le syst&egrave;me choisi, elles seront transform&eacute;es &agrave; la vol&eacute;e en g&eacute;ographique avant d\'&ecirc;tre envoy&eacute;es au formulaire.<br/>Vous pourrez n&eacute;anmoins continuer &agrave; saisir en g&eacute;od&eacute;sique.',
'system_code'			=> ',IGNF:LAMBE,IGNF:LAMB93',
'system_name'			=> 'vide,Lambert 2e,Lambert 93',
'options'				=> 'Options',

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

'publie'		=> 'Publi&eacute;',
'propose'		=> 'Propos&eacute;',
'poubelle'		=> 'A la poubelle',

'config'		=> 'Configuration',

// Correspondance code => nom de la zone Geoportail :
'zone'		=> 'Zone g&eacute;ographique',
'wld'		=> 'Monde',
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
'info_geoservice'	=> 'Vous pouvez ajouter vos propres services cartographiques sur les cartes de votre site.<br/><i>Aujourd\'hui, seuls les services WMS et G&eacute;oportail ont &eacute;t&eacute; impl&eacute;ment&eacute;s.</i>',
'info_geoportail_service'	=> 'Les services <b>G&eacute;oportail</b> permettent de regrouper plusieurs couches G&eacute;oportail en une seule ou d\'enlever certaines couches qu\'on ne veut pas voir appara&icirc;tre (statut=poubelle)',
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
'geoservice_zone'	=> 'Zone du service',
'geoservice_extent'	=> 'Extension du service',
'logo'			=> 'Association de logo (<i>les logos de m&ecirc;me nom seront associ&eacute;s</i>)',
'link'			=> 'Lien vers le service',
'geoservice_descriptif'	=> 'Descritif du service',
'icone_ajouter_service'	=> 'Ajouter un nouveau service',
'selectionnable'	=> 'couche s&eacute;lectionnable <i>(lorsque le service le permet)</i>.',
'kml_info'			=> 'Attention : l\'affichage de fichiers distants n&eacute;cessite l\'utilisation d\'un proxy et la d&eacute;claration du site au plugin.',

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
'centrer_art'	=> 'Utiliser le g&eacute;opositionnement du parent',
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

'gpx_z'			=> 'Altitude : ',
'gpx_zmin'		=> 'Zmin : ',
'gpx_zmax'		=> 'Zmax : ',
'gpx_dist'		=> 'Distance : ',
'gpx_laps'		=> 'Dur&eacute;e : ',
'gpx_tps'		=> 'Temps : ',

'icone_geoportail' => 'G&eacute;oportail'

);


?>