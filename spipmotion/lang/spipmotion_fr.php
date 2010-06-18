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
	'bitrate' => 'Bitrate vid&eacute;o de la sortie en Kbps : ',
	'bitrate_audio' => 'Bitrate audio de la sortie en Kbps : ',
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
	'explication_encodeur' => 'Si vous choisissez l\'encodeur ffmpeg2theora pour vos fichiers Ogg vid&eacute;os, les choix de codecs vid&eacute;o et audios deviennent inutiles.',
	'explications_extensions_prises_en_charge' => 'Les formats pris en charge sont l\'ensemble des formats de fichiers qui seront trait&eacute;s par SPIPmotion pour r&eacute;cup&eacute;rer diverses informations (longueur, taille, vignette...)',
	'explications_extensions_prises_en_charge_encodage' => 'Ces formats sont ceux qui seront transcod&eacute;s automatiquement ou &agrave; la demande',
	'explications_extensions_sortie' => 'Ces extensions sont les formats vers lesquels les fichiers seront encod&eacute;s, vous pouvez en choisir plusieurs par type (attention &agrave; la lourdeur pour le serveur cependant).',
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
	'info_ffmpeg2theora_libtheora_version' => 'Version de libtheora utilis&eacute;e par ffmpeg2theora',
	'info_ffmpeg2theora_version' => 'Version de ffmpeg2theora',
	'info_format' => 'Format : ',
	'info_framecount' => 'Nombre de frames : ',
	'info_framerate' =>'Framerate : ',
	'info_infos_techniques' => 'Informations techniques...',
	'info_installer_encoder_ftp' => 'En tant qu\'administrateur, vous pouvez installer (par FTP) des fichiers de type .AVI, .MPG, .MP4 ou .MOV dans le dossier "tmp/upload" pour ensuite les encoder au format FLV ici.',
	'info_mime' => 'Type mime : ',
	'info_nom_fichier' => 'Nom du fichier : ',
	'info_page_ffmpeg_infos' => 'Cette page n\'est qu\'informative. Elle indique la configuration compl&egrave;te de l\'installation de FFMPEG sur votre syst&egrave;me.',
	'info_pixelformat' =>'Format de pixel : ',
	'info_selectionner_fichier_encoder' => 'Vous pouvez encoder un des fichiers suivants',
	'info_videobitrate' => 'Bitrate vid&eacute;o : ',
	'info_videocodec' => 'Codec vid&eacute;o : ',
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
	'lien_ffmpeg_mac'=>'Installer FFmpeg sur Mac OSX',
	'lien_ffmpeg_linux'=>'Installer FFmpeg sur Linux',
	'lien_flvtool'=>'Installer FLVTool2',
	'lien_forcer_ffmpeg_infos' => 'Forcer la mise &agrave; de ces informations',

	// M

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
	'titre_page_ffmpeg_infos' => 'Informations sur FFMpeg',
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
	'verifier_droits' => 'V&eacute;rifiez les droits du script "script_bash/spipmotion.sh". Les droits sur ce fichier doivent &ecirc;tre en 755.',
	'version_encodee_de' => 'Ce document est une version encod&eacute;e du document @id_orig@',

	//W
	'width_video' => 'Largeur de la sortie en px : ',
);
?>
