<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'configurer_rssconfig' => 'Configurer le flux RSS',
	
	// E
	'explication_activation' => 'Par défaut, SPIP propose un flux rss des derniers articles publiés. Vous pouvez ici désactiver la production de ce flux (un fichier vide sera néanmoins proposé; si vous utilisez le plugin zvide, le lien ne sera pas inclu dans l\'entête des pages html).',
	'explication_age_art' => 'Indiquez un nombre de jours. Les articles ayant un âge inférieur à ce critère seront inclus dans le flux. Mettez 0 pour ne pas utiliser ce critère.',
	'explication_age_modif' => 'Inclure également les articles plus anciens mais modifiés récemment ? Indiquez l\'âge de la modification en jours, 0 si vous ne souhaitez pas inclure les articles modifiés récemment.',
	'explication_articles_a_inclure' => 'Par défaut, SPIP inclut dans le flux les 10 derniers articles publiés ainsi que les articles publiés il y a moins de trois jours. Vous pouvez modifier ci-dessous ces deux critères.',
	'explication_rubriques' => 'Par défaut, SPIP inclut dans le flux les articles publiés de toutes les rubriques du site. Vous pouvez modifier ci-dessous le choix des rubriques à traiter. L\'appel au fichier backend avec un id_rubrique spécifique reste inchangé si le paramétrage ci-dessous est renseigné.',
	'explication_rubriques_a_inclure' => 'Saisir les numéros des rubriques à inclure séparés par une virgule (les sous-rubriques seront automatiquement rajoutées). Ne rien saisir pour que toutes les rubriques soient traitées.',
	'explication_rubriques_a_inclure_selecteur' => 'Sélectionner les rubriques à inclure (les sous-rubriques seront automatiquement rajoutées). Ne rien choisir pour que toutes les rubriques soient traitées.',
	'explication_creator' => 'Auteur(s) de l\'article affichés dans le flux RSS.',
	'explication_diffuser_documents' => 'Diffuser dans le flux l\'URL des documents joints à l\'articles ?',
	'explication_diffuser_mots' => 'Diffuser les mots-clés associés à l\'article dans le flux comme tags de l\'article ?',
	'explication_diffuser_rubrique' => 'Diffuser la rubrique dans le flux comme catégorie de l\'article ?',
	'explication_nb_art' => 'Nombre d\'articles à afficher parmi les plus récents. Mettez 0 pour ne pas utiliser ce critère.',
	'explication_syndication_integrale' => 'Diffuser les articles dans leur intégralité ? (Si non, seul un résumé sera diffusé.)',
	
	// L
	'label_activation' => 'Activation',
	'label_activer' => 'Activer ?',
	'label_age_art' => 'Critère d\'âge',
	'label_age_modif' => 'Modifiés récemment',
	'label_articles_a_inclure' => 'Articles à inclure',
	'label_choix_creator_aucun' => 'Aucun',
	'label_choix_creator_auteurs' => 'Auteurs de l\'article dans SPIP',
	'label_choix_creator_nom_site_spip' => 'Nom du site SPIP',
	'label_creator' => 'Auteur(s)',
	'label_diffuser_documents' => 'Documents joints',
	'label_diffuser_mots' => 'Mots-clés',
	'label_diffuser_rubrique' => 'Rubrique',
	'label_nb_art' => 'Critère de nombre',
	'label_options_articles' => 'Options des articles',
	'label_rubriques' => 'Rubriques',
	'label_rubriques_a_inclure' => 'Rubriques à inclure',
	'label_syndication_integrale' => 'Syndication intégrale',
	
	// R
	'rssconfig' => 'Flux RSS',

);

?>