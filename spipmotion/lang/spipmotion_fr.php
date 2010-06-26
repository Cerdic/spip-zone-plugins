<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bitrate' => 'Bitrate vid&eacute;o de la sortie en kb/s : ',
	'bitrate_audio' => 'Bitrate audio de la sortie en kb/s : ',
	'bouton_encoder' => 'Encoder',

	// C
	'caption_avfilter_geres' => 'Filtres vid&eacute;os (avfilters) g&eacute;r&eacute;s',
	'caption_codecs_geres' => 'Codecs pris en charge',
	'caption_formats_geres' => 'Formats pris en charge',
	'chemin_executable' => 'Chemin de l\'ex&eacute;cutable FFmpeg sur le serveur :',
	'codec_type_audio' => 'Audio',
	'codec_type_soustitre' => 'Sous-titre',
	'codec_type_video' => 'Vid&eacute;o',
	'choix_debug_mode' => 'En mode debug, le webmestre du site re&ccedil;oit par mail des informations &agrave; chaque encodage r&eacute;ussi ou rat&eacute;',
	'choix_encodage_auto' => 'Les fichiers seront ajout&eacute;s automatiquement dans la file d\'attente d\'encodage d&egrave;s leur insertion dans la base de donn&eacute;e',

	// D
	'document_dans_file_attente' => 'Ce document est dans la file d\'attente pour l\'encodage',

	// E
	'encodage_en_cours' => 'ENCODAGE EN COURS...',
	'encoder_video' => '(R&eacute;)encoder cette vid&eacute;o',
	'encoder_son' => '(R&eacute;)encoder ce son',
	'erreur_chemin_ffmpeg' => 'Aucun encodage ne pourra &ecirc;tre effectu&eacute; car le chemin ne fonctionne pas.',
	'erreur_verifier_droits' => 'Le script d\'encodage "script_bash/spipmotion.sh" n\'est pas executable.',
	'explication_encodeur' => 'Si vous choisissez l\'encodeur ffmpeg2theora pour vos fichiers Ogg vid&eacute;os, les choix de codecs vid&eacute;o et audios deviennent inutiles.',
	'explications_extensions_prises_en_charge' => 'Les formats pris en charge sont l\'ensemble des formats de fichiers qui seront trait&eacute;s par SPIPmotion pour r&eacute;cup&eacute;rer diverses informations (longueur, taille, vignette...)',
	'explications_extensions_prises_en_charge_encodage' => 'Ces formats sont ceux qui seront transcod&eacute;s automatiquement ou &agrave; la demande',
	'explications_extensions_sortie' => 'Ces extensions sont les formats vers lesquels les fichiers seront encod&eacute;s, vous pouvez en choisir plusieurs par type (attention &agrave; la lourdeur pour le serveur cependant).',
	'explication_presets_qualite' => 'Ce r&eacute;glage permet de d&eacute;terminer avec quel preset de qualit&eacute; le codec libx264 va &ecirc;tre utilis&eacute;. Ce r&eacute;glage est d&eacute;terminant pour la qualit&eacute; du r&eacute;sultat final mais aussi sur la lenteur de l\'encodage.',
	'explication_vorbis_qualite' => 'Le codec vorbis (ou libvorbis) n\'utilisent pas de bit rate constant mais variable, il est donc pr&eacute;f&eacute;rable de sp&eacute;cifier la qualit&eacute; non pas par un bit rate mais par un niveau de qualit&eacute; souhait&eacute;.',
	'extensions_audio' => 'Formats audios',
	'extensions_video' => 'Formats vid&eacute;os',

	// F
	'ffmpeg2theora_install' => 'FFMpeg2Theora sur votre serveur :',
	'framerate' => 'Nombre d\'images par seconde : ',
	'frequence_audio' => 'Fr&eacute;quence d\'&eacute;chantillonage audio : ',

	// G
	'gestion_format_entree' => 'Gestion des formats d\'entr&eacute;e',
	'gestion_format_sortie' => 'Gestion des formats de sortie',

	// H
	'height_video' => 'Hauteur de la sortie en px : ',

	// I
	'info_aucun_resultat_attente' => 'Aucun document n\'est en attente d\'encodage.',
	'info_audiobitrate' => 'Bitrate audio : ',
	'info_audiocodec' => 'Codec audio : ',
	'info_audiochannels' => 'Nombre de canaux audios : ',
	'info_audiosamplerate' => 'Fr&eacute;quence d\'&eacute;chantillonnage audio : ',
	'info_bitrate' => 'Bitrate total : ',
	'info_compiler_avfilter-support' => 'Compil&eacute; avec le support des filtres vid&eacute;os (anciennement vhook)',
	'info_compiler_build_date_timestamp' => 'Date de compilation',
	'info_compiler_configuration' => 'Variables de compilation',
	'info_compiler_ffmpeg-php' => 'PHP dispose de l\'extension FFMPEG',
	'info_compiler_ffmpeg-php-builddate' => 'Date de compilation',
	'info_compiler_ffmpeg-php-gdenabled' => 'Support de GD dans FFMPEG-php',
	'info_compiler_ffmpeg-php-infos' => 'Informations de compilation de FFMPEG-php',
	'info_compiler_ffmpeg-php-version' => 'Version',
	'info_compiler_ffmpeg_version' => 'Version de FFMpeg',
	'info_compiler_gcc' => 'Version du compilateur gcc',
	'info_compiler_libavcodec_build_number' => 'Num&eacute;ro de version de libavcodec &agrave; la compilation',
	'info_compiler_libavcodec_version_number' => 'Num&eacute;ro de version de libavcodec utilis&eacute;',
	'info_compiler_vhook-support' => 'Compil&eacute; avec le support des vhooks',
	'info_duree' => 'Dur&eacute;e : ',
	'info_erreurs' => 'Erreurs',
	'info_ffmpeg2theora_libtheora_version' => 'Version de libtheora utilis&eacute;e par ffmpeg2theora',
	'info_ffmpeg2theora_version' => 'Version de ffmpeg2theora',
	'info_format' => 'Format : ',
	'info_framecount' => 'Nombre de frames : ',
	'info_framerate' =>'Framerate : ',
	'info_infos_techniques' => 'Informations techniques...',
	'info_installer_encoder_ftp' => 'En tant qu\'administrateur, vous pouvez installer (par FTP) des fichiers de type .AVI, .MPG, .MP4 ou .MOV dans le dossier "tmp/upload" pour ensuite les encoder au format FLV ici.',
	'info_mime' => 'Type mime : ',
	'info_nom_fichier' => 'Nom du fichier : ',
	'info_nombre_encodage_attente' => 'Il y a @nb@ document(s) en attente d\'encodage',
	'info_nombre_encodes' => '@nb@ fichiers ont &eacute;t&eacute; correctement encod&eacute;s.',
	'info_objet_non_publie' => 'Non publi&eacute;',
	'info_page_ffmpeg_infos' => 'Cette page n\'est qu\'informative. Elle indique la configuration compl&egrave;te de l\'installation de FFMPEG sur votre syst&egrave;me.',
	'info_pixelformat' =>'Format de pixel : ',
	'info_profils_son' => 'Profil(s) d\'encodage son',
	'info_profils_video' => 'Profil(s) d\'encodage vid&eacute;o',
	'info_selectionner_fichier_encoder' => 'Vous pouvez encoder un des fichiers suivants',
	'info_statut_encode_en_cours' => 'En cours',
	'info_statut_encode_erreur' => 'En erreur',
	'info_statut_encode_oui' => 'Encod&eacute;',
	'info_statut_encode_non' => 'En attente',
	'info_statut_non_publie' => 'Cet objet n\'est pas publi&eacute;',
	'info_videobitrate' => 'Bitrate vid&eacute;o : ',
	'info_videocodec' => 'Codec vid&eacute;o : ',
	'info_voir_log_erreur' => 'Voir le log',
	'install_ajout_champs_documents' => 'Ajout des champs sur spip_documents',
	'install_creation_base' => 'Cr&eacute;ation de la base de spipmotion',
	'install_maj_base' => 'Mise &agrave; jour de la base de spipmotion &agrave la version @version@',

	// L
	'label_codec_son' => 'Codec audio &agrave; utiliser',
	'label_codec_video' => 'Codec vid&eacute;o &agrave; utiliser',
	'label_debug_mode' => 'Mode debug',
	'label_encodage_auto' => 'Encoder automatiquement',
	'label_encodeur' => 'Encodeur',
	'label_extensions_prises_en_charge_encodage' => 'Formats pris en charge pour l\'encodage',
	'label_extensions_prises_en_charge' => 'Formats pris en charge de base',
	'label_extensions_sortie' => 'Formats d\'encodage',
	'label_format_final' => 'Format d&eacute;sir&eacute;',
	'label_passes_1_encodage' => 'Une passe',
	'label_passes_2_encodage' => 'Deux passes',
	'label_passes_encodage' => 'Nombre de passes pour l\'encodage',
	'label_presets_qualite' => 'Qualit&eacute; d\'encodage (libx264 uniquement)',
	'label_relancer_encodage' => 'Relancer l\'encodage',
	'label_verifier_logiciels' => 'Rev&eacute;rifier les logiciels',
	'label_vorbis_qualite' => 'Qualit&eacute; d\'encodage (Vorbis uniquement)',
	'lien_enlever_previsu' => 'Enlever la pr&eacute;visualisation',
	'lien_ffmpeg_mac'=>'Installer FFmpeg sur Mac OSX',
	'lien_ffmpeg_linux'=>'Installer FFmpeg sur Linux',
	'lien_flvtool'=>'Installer FLVTool2',
	'lien_forcer_ffmpeg_infos' => 'Forcer la mise &agrave; de ces informations',

	// M
	'message_encodage_erreur_log' => 'Le fichier de log est le suivant :',
	'message_encodage_objet_lie' => 'Il est associ&eacute; &agrave; l\'objet @objet@ #@id_objet@ :',
	'message_encodage_objet_lie_plusieurs' => 'Ces documents sont associ&eacute;s  &agrave; l\'objet @objet@ #@id_objet@ :',
	'message_encodage_oui' => 'Le document @id_document@ a &eacute;t&eacute; correctement encod&eacute;.',
	'message_encodage_sujet_erreur' => 'Encodage en erreur',
	'message_encodage_sujet_oui' => 'Encodage r&eacute;ussi',
	'message_encodage_sujet_termine' => 'Encodage termin&eacute;',
	'message_encodage_unique_oui' => 'Le document @id_document@ a &eacute;t&eacute; correctement encod&eacute; en @extension@.',
	'message_encodage_unique_erreur' => 'Le document @id_document@ n\'a pas &eacute;t&eacute; correctement encod&eacute; en @extension@.',
	'message_texte_binaire_manquant' => 'Un logiciel n&eacute;cessaire n\'est pas disponible sur votre serveur :',
	'message_texte_binaires_manquant' => 'Plusieurs logiciels n&eacute;cessaires ne sont pas disponibles sur votre serveur :',
	'message_texte_binaires_informer' => 'Veuillez en informer votre administrateur.',
	'message_titre_binaire_manquant' => 'Un logiciel manquant',
	'message_titre_binaires_manquant' => 'Plusieurs logiciels manquant',

	// N
	'non' => 'non',

	// O
	'options_config' => 'Configuration de l\'encodage',
	'oui' => 'oui',

	// P
	'profil_encodage' => 'Profil d\'encodage pour le format : @format@',
	'profils_encodage_son' => 'Profil(s) d\'encodage (Son)',
	'profils_encodage_video' => 'Profil(s) d\'encodage (Vid&eacute;o)',

	// R
	'recuperer_infos' => 'R&eacute;cup&eacute;rer les donn&eacute;es techniques',
	'recuperer_logo' => 'R&eacute;cup&eacute;rer une vignette',

	// S
	'select_all' => 'Tout s&eacute;lectionner',
	'spipmotion_boite' => 'Configuration de SPIPmotion',
	'spipmotion_descriptif' => 'Ce plugin permet d"encoder à la vol&eacute;e des documents audios et vid&eacute;os.',
	'spipmotion_liens'=>'Liens compl&eacute;mentaires : ',

	// T
	'thead_date' => 'Date',
	'thead_duree_encodage' => 'Dur&eacute;e d\'encodage',
	'thead_format' => 'Format',
	'thead_id_auteur' => 'Utilisateur',
	'thead_id_file' => 'Id dans la file',
	'thead_id_origine_doc' => 'Id du document original',
	'thead_nombre' => 'Nombre',
	'thead_objet_attache' => 'Attach&eacute; &agrave; l\'objet',
	'thead_statut' => 'Statut',
	'titre_fichier_log' => 'Contenu du fichier de log de l\'id #@id@ dans la file d\'attente',
	'titre_page_ffmpeg_infos' => 'Informations sur FFMpeg',
	'titre_page_file' => 'File d\'attente d\'encodage',
	'titre_page_file_attente' => 'Documents en attente d\'encodage',
	'titre_page_file_encodes' => 'Liste des encodages termin&eacute;s',
	'titre_page_file_encodes_jour' => 'Encodages par date',
	'titre_previsu' => 'Pr&eacute;visualisation',
	'th_avfilter_description' => 'Description',
	'th_avfilter_nom' => 'Nom',
	'th_format_decode' => 'D&eacute;code',
	'th_format_encode' => 'Encode',
	'th_format_nom_complet' => 'Nom complet',
	'th_format_nom_court' => 'Nom court',
	'th_format_type' => 'Type',

	// U
	'unselect_all' => 'Tout d&eacute;s&eacute;lectionner',

	// V
	'version_encodee_de' => 'Ce document est une version encod&eacute;e du document @id_orig@',

	//W
	'width_video' => 'Largeur de la sortie en px : ',
);
?>
