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
	'bitrate' => 'Video bitrate of the output in kb/s : ',
	'bitrate_audio' => 'Audio bitrate of the output in kb/s : ',
	'bouton_encoder' => 'Encode',
	'bouton_reencoder_tout' => 'Encode again all files',
	'bouton_reencoder_tout_format' => 'Encode again all files in @format@',
	'bouton_reencoder_tout_message' => 'Are you sure you want to encode again all files. This can be very long.',
	'bouton_reencoder_tout_message_format' => 'Are you sure you want to encode again all files in @format@. This can be very long.',

	// C
	'caption_avfilter_geres' => 'Video filters (avfilters) managed',
	'caption_codecs_geres' => 'Supported Codecs',
	'caption_formats_geres' => 'Supported formats',
	'chemin_executable' => 'Path to FFmpeg on the server :',
	'codec_type_audio' => 'Audio',
	'codec_type_soustitre' => 'Subtitles',
	'codec_type_video' => 'Video',
	'choix_debug_mode' => 'In debug mode, the webmaster receives mail information to each succeeded or failed encoding',
	'choix_encodage_auto' => 'The files will be automatically added in the encoding queue when they are inserted to the database',

	// D
	'document_dans_file_attente' => 'This document is in the encoding queue',

	// E
	'encoder_son' => '(Re)encode this sound',
	'encoder_son_version' => '(Re)encode only the @version@ version of this sound',
	'encoder_video' => '(Re)encode this video',
	'encoder_video_version' => '(Re)encode only the @version@ version of this video',
	'erreur_binaire' => 'One or several softwares needed for the plugin are not available on the system.',
	'erreur_chemin_ffmpeg' => 'No encoding can be performed because the path does not work.',
	'erreur_document_plus_disponible' => 'This document is not available on the website anymore',
	'erreur_script_spipmotion_non_executable' => 'The script spipmotion.sh was not found or is not executable.',
	'erreur_verifier_droits' => 'The encoding script "script_bash/spipmotion.sh" is not executable.',
	'explication_encodeur' => 'If you choose the ffmpeg2theora encoder to your Ogg video, the choice of audio and video codecs become useless.',
	'explication_framerate' => 'Leave this field blank to keep the same framerate than the original',
	'explications_extensions_prises_en_charge' => 'The supported formats are all file formats which will be treated by SPIPmotion to retrieve various information (length, size, screenshots ...)',
	'explications_extensions_prises_en_charge_encodage' => 'These formats are those who will be transcoded automatically or on demand',
	'explications_extensions_sortie' => 'These extensions are the formats to which the files are encoded, you can choose several types (attention to the use of the server though).',
	'explication_presets_qualite' => 'This setting allows you to determine what quality preset libx264 codec will use. This setting is critical to the quality of the final result but also on the slow encoding.',
	'explication_qualite_video_ffmpeg2theora' => 'This setting allows you to determine the general quality level used for video. It dynamically changes the video bitrate used.',
	'explication_vorbis_qualite' => 'Vorbis codec (or libvorbis) don\'t use constant bit rate but variable, it is preferable to specify the quality not by a bit rate but with a quality level.',
	'extensions_audio' => 'Audio formats',
	'extensions_video' => 'Video formats',

	// F
	'ffmpeg2theora_install' => 'FFMpeg2Theora on your server :',
	'framerate' => 'Frame rate : ',
	'frequence_audio' => 'Audio frequency : ',

	// G
	'gestion_format_entree' => 'Management of input formats',
	'gestion_format_sortie' => 'Management of output formats',

	// H
	'height_video' => 'Height of the output in px : ',

	// I
	'info_aucun_resultat_attente' => 'No documents awaiting encoding.',
	'info_audiobitrate' => 'Audio bitrate : ',
	'info_audiocodec' => 'Audio codec : ',
	'info_audiochannels' => 'Audio channels number : ',
	'info_audiosamplerate' => 'Audio sample rate : ',
	'info_bitrate' => 'Total bitrate : ',
	'info_compiler_avfilter-support' => 'Compiled with the support of video filters (formerly vhook)',
	'info_compiler_build_date_timestamp' => 'Build date',
	'info_compiler_configuration' => 'Compilation variables',
	'info_compiler_ffmpeg-php' => 'PHP has the FFMPEG extension',
	'info_compiler_ffmpeg-php-builddate' => 'Build date',
	'info_compiler_ffmpeg-php-gdenabled' => 'GD support in FFMpeg-php',
	'info_compiler_ffmpeg-php-infos' => 'Build informations of FFMpeg-php',
	'info_compiler_ffmpeg-php-version' => 'Version',
	'info_compiler_ffmpeg_version' => 'Version of FFMpeg',
	'info_compiler_gcc' => 'Version of the gcc compiler',
	'info_compiler_libavcodec_build_number' => 'Version number of libavcodec at the compilation',
	'info_compiler_libavcodec_version_number' => 'Version number of libavcodec used',
	'info_compiler_vhook-support' => 'Compiled with vhooks support',
	'info_document_encodage_en_cours_attente_avant' => '@nb@ encoding(s) are awaiting before beginning to encode this document.',
	'info_document_encodage_en_cours_attente_avant_unique' => 'This document will be the next to be encoded.',
	'info_document_encodage_en_cours' => 'This document is being encoded.',
	'info_document_encodage_en_cours_unique' => 'This document is waiting for encoding in one format.',
	'info_document_encodage_en_cours_plusieurs' => 'This document is waiting for encoding in @nb@ formats.',
	'info_document_encodage_realise' => 'It has ever been encoded in the next formats : ',
	'info_document_encode_formats' => 'This document has been encoded in : ',
	'info_duree' => 'Duration : ',
	'info_erreurs' => 'Errors',
	'info_ffmpeg2theora_libtheora_version' => 'Libtheora version used by ffmpeg2theora',
	'info_ffmpeg2theora_version' => 'ffmpeg2theora version ',
	'info_format' => 'Format : ',
	'info_framecount' => 'Frame count : ',
	'info_framerate' =>'Framerate : ',
	'info_infos_techniques' => 'Technical informations',
	'info_installer_encoder_ftp' => 'As Administrator, you can install (with FTP) .AVI .MPG .MP4 or .MOV files in the directory "tmp/upload" for encoding these files here.',
	'info_mime' => 'Mime type : ',
	'info_nom_fichier' => 'File name : ',
	'info_nombre_encodage_attente' => 'One document is awaiting encoding',
	'info_nombre_encodages_attentes' => 'There are @nb@ documents awaiting encoding',
	'info_nombre_encodes' => '@nb@ files have been properly encoded.',
	'info_objet_non_publie' => 'Not published',
	'info_page_ffmpeg_infos' => 'This page is only available for information. It shows the complete configuration of the FFMpeg installation on your system.',
	'info_poid_fichier' => 'File size',
	'info_profils_son' => 'Audio encoding profile(s)',
	'info_profils_video' => 'Video encoding profile(s)',
	'info_pixelformat' =>'Pixel format : ',
	'info_reencoder' => 'Encode (again)',
	'info_relancer_erreurs' => 'Resume all encodings in error',
	'info_selectionner_fichier_encoder' => 'You can encode one of the following files',
	'info_statut_encode_en_cours' => 'Ongoing',
	'info_statut_encode_erreur' => 'Error',
	'info_statut_encode_oui' => 'Encoded',
	'info_statut_encode_non' => 'Waiting',
	'info_statut_non_publie' => 'This object is not published',
	'info_version' => '@version@ version',
	'info_version_original' => 'Original',
	'info_videobitrate' => 'Video bitrate : ',
	'info_videocodec' => 'Video codec : ',
	'info_voir_log_erreur' => 'Show log',
	'install_ajout_champs_documents' => 'Adding fields on the spip_documents database table',
	'install_creation_base' => 'Creating the SPIPmotion database',
	'install_maj_base' => 'Update of the SPIPmotion database at version @version@',

	// L
	'label_codec_son' => 'Audio codec to use',
	'label_codec_video' => 'Video codec to use',
	'label_debug_mode' => 'Debug mode',
	'label_encodage_auto' => 'Autmatically encode',
	'label_encodeur' => 'Encoder',
	'label_extensions_prises_en_charge_encodage' => 'Supported formats for encoding',
	'label_extensions_prises_en_charge' => 'Formats supported',
	'label_extensions_sortie' => 'Encoding formats',
	'label_format_final' => 'Desired format',
	'label_passes_1_encodage' => '1 pass',
	'label_passes_2_encodage' => '2 pass',
	'label_passes_encodage' => 'Number of passes for encoding',
	'label_presets_qualite' => 'Video encoding quality (libx264 only)',
	'label_qualite_video_ffmpeg2theora' => 'Video encoding quality (if use of ffmpeg2theora only)',
	'label_relancer_encodage' => 'Re encoding',
	'label_verifier_logiciels' => 'Verify the softwares again',
	'label_vorbis_qualite' => 'Encoding quality (Only Vorbis audio)',
	'lien_enlever_previsu' => 'Remove the preview',
	'lien_ffmpeg_mac'=>'Install FFmpeg on Mac OSX',
	'lien_ffmpeg_linux'=>'Install FFmpeg on Linux',
	'lien_flvtool'=>'Install FLVTool2',
	'lien_forcer_ffmpeg_infos' => 'Force the update of these informations',
	'lien_recuperer_logo_fichier' => 'the file itself',
	'lien_supprimer_versions' => 'Remove all the versions of the document',
	'lien_supprimer_version' => 'Remove the @version@ version',

	// M
	'message_confirmation_encodage' => 'Are you sure you want to (re)encode this document in the @version@ format?',
	'message_confirmation_encodages' => 'Are you sure you want to (re)encode this document in all the formats?',
	'message_confirmation_suppression_version' => 'Are you sure you want to remove the @version@ version of this document?',
	'message_confirmation_suppression_versions' => 'Are you sure you want to remove all the encoded versions of this document?',
	'message_document_encours_encodage_version' => 'This document is currently awaiting encoding in @version@.',
	'message_encodage_erreur_log' => 'The log file is as follows :',
	'message_encodage_objet_lie' => 'It is associated with the object @objet@ #@id_objet@ :',
	'message_encodage_objet_lie_plusieurs' => 'These documents are associated  width the object @objet@ #@id_objet@ :',
	'message_encodage_oui' => 'The document #@id_document@ has been properly encoded.',
	'message_encodage_sujet_oui' => 'Encoding success',
	'message_encodage_sujet_erreur' => 'Encoding error',
	'message_encodage_sujet_termine' => 'Encoding success',
	'message_encodage_unique_oui' => 'The document #@id_document@ has been properly encoded to @extension@.',
	'message_encodage_unique_erreur' => 'The document #@id_document@ was not properly encoded to @extension@.',
	'message_texte_binaire_manquant' => 'A necessary software is not available on your server :',
	'message_texte_binaires_informer_safe_mode' => 'Safe mode is activated on your website. Binaries should be installed in the "@execdir@" folder of the server.',
	'message_texte_binaires_informer' => 'Please inform your system administrator.',
	'message_texte_binaires_manquant' => 'Several software needed are not available on your server :',
	'message_titre_binaire_manquant' => 'A missing software',
	'message_titre_binaires_manquant' => 'Plusieurs logiciels manquant',

	// O
	'options_config' => 'Encoding configuration',

	// P
	'profil_encodage' => 'Encoding profile for the format : @format@',
	'profils_encodage_son' => 'Encoding profile(s) (Sound)',
	'profils_encodage_video' => 'Encoding profile(s) (Video)',

	// R
	'recuperer_infos' => 'Retrieve technical datas',
	'recuperer_logo' => 'Retrieve a screenshot',

	// S
	'select_all' => 'Select all',
	'spipmotion_boite' => 'Configuration of SPIPmotion',
	'spipmotion_descriptif' => 'This plugin allows to encode audio and video files.',
	'spipmotion_liens'=>'Additional links : ',

	// T
	'thead_date' => 'Date',
	'thead_duree_encodage' => 'Encoding duration',
	'thead_format' => 'Format',
	'thead_id_auteur' => 'User',
	'thead_id_file' => 'Id in the queue',
	'thead_id_origine_doc' => 'Id of the original document',
	'thead_nombre' => 'Amount',
	'thead_objet_attache' => 'Attached to the object',
	'thead_stat_duree' => 'Encoded file length (in minutes) per encoding minute',
	'thead_stat_octet' => 'Original file size encoded per encoding minute',
	'thead_statut' => 'Status',
	'thead_template_encodage' => 'Encoding template',
	'titre_fichier_log' => 'Content of the log file for the ID #@id@ in the queue',
	'titre_page_ffmpeg_infos' => 'FFMpeg informations',
	'titre_page_file' => 'Encoding queue',
	'titre_page_file_attente' => 'Documents waiting for encoding',
	'titre_page_file_encodes' => 'List of realized encodings',
	'titre_page_file_encodes_jour' => 'Encodings by date',
	'titre_page_file_stats' => 'Encodings statistics',
	'titre_previsu' => 'Preview',
	'th_avfilter_description' => 'Description',
	'th_avfilter_nom' => 'Name',
	'th_format_decode' => 'Decode',
	'th_format_encode' => 'Encode',
	'th_format_nom_complet' => 'Complete name',
	'th_format_nom_court' => 'Short name',
	'th_format_type' => 'Type',

	// U
	'unselect_all' => 'Deselect all',

	// V
	'version_encodee_de' => 'This document is an encoded version of the document ID @id_orig@',

	//W
	'width_video' => 'Width of the output in px : ',

);
?>