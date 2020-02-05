<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/videos.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description_doc' => 'Documentation',
	'cfg_description_titre' => 'Configuration du plugin Vidéo(s)',
	'cfg_explication_afficher_commandes' => 'Afficher les commandes du lecteur ? (lecture, pause, etc.)',
	'cfg_explication_afficher_suggestions' => 'A la fin de vidéo, des vidéos d’autres Youtubers peuvent être proposées par Youtube. Afficher ces suggestions ?',
	'cfg_explication_afficher_titres_et_actions' => 'Afficher le titre de la vidéo ainsi que les actions du lecteur ? (partage, etc.)',
	'cfg_explication_hauteur' => 'Choisissez la hauteur par défaut pour vos vidéos. Cette option reste surchargeable dans l’appel au modèle.',
	'cfg_explication_largeur' => 'Choisissez la largeur par défaut pour vos vidéos. Cette option reste surchargeable dans l’appel au modèle.',
	'cfg_explication_liste_definition' => 'Encapsuler la vidéo dans un dl/dt/dd comme peut le faire SPIP pour sa gestion de document.',
	'cfg_explication_mode_confidentialite' => 'Utiliser une URL youtube-nocookie plutôt que Youtube classique ?',
	'cfg_explication_responsive' => 'Rendre les largeurs et hauteurs de la vidéo fluides : permet de mieux l’intégrer au sein d’un site "responsive".',
	'cfg_explication_titre_descriptif' => 'Afficher le titre et la description des vidéos. Cette option reste surchargeable dans l’appel au modèle.',
	'cfg_explication_wmode' => 'Choisissez le mode à appliquer pour les objets Flash (fallback Flowplayer, iFrames Dailymotion, Youtube, Vimeo).',
	'cfg_explication_youtube_api_key' => 'Pour pouvoir accéder aux données des vidéos Youtube, vous devez renseigner une API KEY',
	'cfg_explication_youtube_channel' => 'ID de la chaine youtube par défaut que vous souhaitez diffuser',
	'cfg_label_afficher_commandes' => 'Commandes du lecteur',
	'cfg_label_afficher_suggestions' => 'Suggestions de vidéos',
	'cfg_label_afficher_titres_et_actions' => 'Titre de la vidéo et actions du lecteur',
	'cfg_label_enablejsapi' => 'Permettre le contrôle de la vidéo avec "enablejsapi"',
	'cfg_label_hauteur' => 'Hauteur par défaut',
	'cfg_label_largeur' => 'Largeur par défaut',
	'cfg_label_liste_definition' => 'Liste de définition HTML',
	'cfg_label_mode_confidentialite' => 'Mode de confidentialité avancé',
	'cfg_label_responsive' => 'Taille fluide',
	'cfg_label_titre_descriptif' => 'Titre et descriptif',
	'cfg_label_wmode' => 'Wmode par défaut',
	'cfg_label_youtube_api_key' => 'Youtube API Key',
	'cfg_label_youtube_channel' => 'Youtube Channel',
	'cfg_titre_configurations_communes' => 'Configurations communes',
	'cfg_titre_configurations_dailymotion' => 'Configurations Dailymotion',
	'cfg_titre_configurations_html5' => 'Configuration HTML5 / Alternative',
	'cfg_titre_configurations_vimeo' => 'Configurations Viméo',
	'cfg_titre_configurations_youtube' => 'Configurations Youtube',
	'confirmation_ajout' => 'La vidéo "@titre@" a bien été ajoutée. &lt;video@id_document@&gt;',

	// E
	'erreur_adresse_invalide' => 'Cette adresse n’est pas valide. Si vous tentez d’importer un fichier vidéo (MP4 ou autre), merci d’utiliser l’ajout de document classique de SPIP.',
	'explication_ajouter_video' => '(vidéo distante YouTube, Vimeo, DailyMotion)',

	// L
	'label_ajouter_autre_video' => 'Ajouter une autre vidéo',
	'label_ajouter_video' => 'Ajouter une vidéo',
	'label_url' => 'Adresse de la vidéo',

	// T
	'titre_admin' => 'Plugin Vidéo(s)',

	// V
	'valider' => 'Ajouter la vidéo'
);
