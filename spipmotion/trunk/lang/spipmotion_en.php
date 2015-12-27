<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/spipmotion?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bitrate' => 'Video bitrate of the output in kb/s',
	'bitrate_audio' => 'Audio bitrate of the output in kb/s',
	'bouton_reencoder_tout' => 'Encode again all files',
	'bouton_reencoder_tout_format' => 'Encode again all files in @format@',
	'bouton_reencoder_tout_message' => 'Are you sure you want to encode again all files. This can be very long.',
	'bouton_reencoder_tout_message_format' => 'Are you sure you want to encode again all files in @format@. This can be very long.',
	'bouton_supprimer_encodages_doubles' => 'Delete all files resulting of double encoding',

	// C
	'caption_avfilter_geres' => 'Video filters (avfilters) managed',
	'caption_codecs_geres' => 'Supported Codecs',
	'caption_formats_geres' => 'Supported formats',
	'chemin_executable' => 'Path to FFmpeg on the server',
	'choix_debug_mode' => 'In debug mode, the webmaster receives mail information to each succeeded or failed encoding',
	'choix_encodage_auto' => 'The files will be automatically added in the encoding queue when they are inserted to the database',
	'codec_type_audio' => 'Audio',
	'codec_type_soustitre' => 'Subtitles',
	'codec_type_video' => 'Video',

	// E
	'encoder_son' => '(Re)encode this sound',
	'encoder_son_version' => '(Re)encode only the @version@ version of this sound',
	'encoder_video' => '(Re)encode this video',
	'encoder_video_version' => '(Re)encode only the @version@ version of this video',
	'erreur_binaire' => 'One or several softwares needed for the plugin are not available on the system.',
	'erreur_chemin_ffmpeg' => 'No encoding can be performed because the path does not work.',
	'erreur_document_inexistant' => 'This document doesn’t exist.',
	'erreur_document_interdit' => 'You don’t have sufficient privileges to access this page',
	'erreur_document_plus_disponible' => 'This document is not available on the website anymore',
	'erreur_formulaire_configuration' => 'The form contains at least one error.',
	'erreur_script_spipmotion_non_executable' => 'The script spipmotion.sh was not found or is not executable.',
	'erreur_valeur_int' => 'This value must be an integer.',
	'erreur_valeur_int_superieur' => 'This value must be greater than @val@.',
	'erreur_verifier_droits' => 'The encoding script "script_bash/spipmotion.sh" is not executable.',
	'explication_double_encodage_supprimer_secu' => 'You can safely delete it, the original encoding is available.',
	'explication_framerate' => 'Leave this field blank to keep the same framerate than the original',
	'explication_presets_qualite' => 'This setting allows you to determine what quality preset libx264 codec will use. This setting is critical to the quality of the final result but also on the slow encoding.',
	'explication_vorbis_qualite' => 'Vorbis codec (or libvorbis) don’t use constant bit rate but variable, it is preferable to specify the quality not by a bit rate but with an expected quality level .',
	'explications_extensions_prises_en_charge' => 'The supported formats are all file formats which will be treated by SPIPmotion to retrieve various information (length, size, screenshots ...)',
	'explications_extensions_prises_en_charge_encodage' => 'These formats are those who will be transcoded automatically or on demand',
	'explications_extensions_sortie' => 'These extensions are the formats to which the files are encoded, you can choose several types (attention to the use of the server though).',
	'extensions_audio' => 'Audio formats',
	'extensions_video' => 'Video formats',

	// F
	'ffmpeg_install' => 'FFMpeg on your server:',
	'ffprobe_install' => 'FFprobe on your server:',
	'flvtoolplus_install' => 'FLVtool + + on your server:',
	'framerate' => 'Frame rate',
	'frequence_audio' => 'Audio frequency',

	// G
	'gestion_format_entree' => 'Management of input formats',
	'gestion_format_sortie' => 'Management of output formats',

	// H
	'height_video' => 'Height of the output in px',

	// I
	'info_aspect_ratio' => 'Display ratio of the video',
	'info_audiobitrate' => 'Audio bitrate: ',
	'info_audiochannels' => 'Audio channels number: ',
	'info_audiocodec' => 'Audio codec: ',
	'info_audiosamplerate' => 'Audio sample rate: ',
	'info_bitrate' => 'Total bitrate: ',
	'info_bitrate_mode' => 'Mode:',
	'info_compiler_avfilter-support' => 'Compiled with the support of video filters (formerly vhook):',
	'info_compiler_build_date_timestamp' => 'Build date:',
	'info_compiler_configuration' => 'Compilation variables',
	'info_compiler_ffmpeg-php' => 'PHP has the FFMPEG extension:',
	'info_compiler_ffmpeg-php-builddate' => 'Build date',
	'info_compiler_ffmpeg-php-gdenabled' => 'GD support in FFMpeg-php:',
	'info_compiler_ffmpeg-php-infos' => 'Build informations of FFMpeg-php:',
	'info_compiler_ffmpeg-php-version' => 'Version',
	'info_compiler_ffmpeg_version' => 'Version of FFMpeg:',
	'info_compiler_gcc' => 'Version of the gcc compiler:',
	'info_compiler_libavcodec_build_number' => 'Version number of libavcodec at the compilation',
	'info_compiler_libavcodec_version_number' => 'Version number of libavcodec used',
	'info_compiler_vhook-support' => 'Compiled with vhooks support',
	'info_document_encodage_en_cours_attente_avant' => '@nb@ conversion(s) are awaiting before beginning this one.',
	'info_document_encodage_en_cours_attente_avant_unique' => 'This document will be the next to be converted.',
	'info_document_encodage_en_cours_plusieurs' => 'This document is waiting for conversion in @nb@ formats.',
	'info_document_encodage_en_cours_unique' => 'This document is waiting for conversion in one format.',
	'info_document_encodage_en_erreur' => 'The encoding of this document contains an error.',
	'info_document_encodage_en_erreur_contacter' => 'Please contact an administrator to warn him.',
	'info_document_encodage_en_erreur_relancer_url' => 'You can restart the encoding <a href="@url@">on this page</a>.',
	'info_document_encodage_realise' => 'It has ever been converted in: ',
	'info_document_encodage_termine' => 'The conversion of this document is finished.',
	'info_document_encodage_termine_recharge' => 'You can reload the page to see the definitive content.',
	'info_document_encode_formats' => 'This document has been converted in: ',
	'info_duree' => 'Duration: ',
	'info_encodeur' => 'Encoder: ',
	'info_erreurs' => 'Errors',
	'info_extension' => 'Extension: ',
	'info_ffprobe_disponible' => 'FFprobe is available',
	'info_flvtoolplus_version' => 'Version FLVtool + + installed:',
	'info_format' => 'Format: ',
	'info_framecount' => 'Frame count: ',
	'info_framerate' => 'Framerate: ',
	'info_infos_techniques' => 'Technical informations',
	'info_media' => 'Media type:',
	'info_mediainfo_version' => 'MediaInfo version installed:',
	'info_mime' => 'Mime type: ',
	'info_nom_fichier' => 'File name: ',
	'info_page_ffmpeg_infos' => 'This page is only available for information. It shows the complete configuration of the FFMpeg installation on your system.',
	'info_pixelformat' => 'Pixel format: ',
	'info_poid_fichier' => 'File size:',
	'info_reencoder' => '(Re)encode',
	'info_rotation' => 'Rotation angle:',
	'info_spipmotion_sh_chemin' => 'Path of the script:',
	'info_spipmotion_sh_version' => 'Version of the script:',
	'info_version' => '@version@ version',
	'info_version_original' => 'Original',
	'info_videobitrate' => 'Video bitrate: ',
	'info_videocodec' => 'Video codec: ',
	'info_voir_log_erreur' => 'Show log',

	// L
	'label_codec_son' => 'Audio codec to use',
	'label_codec_video' => 'Video codec to use',
	'label_debug_mode' => 'Debug mode',
	'label_encodage_auto' => 'Convert automatically',
	'label_encodeur' => 'Encoder',
	'label_extensions_prises_en_charge' => 'Formats supported',
	'label_extensions_prises_en_charge_encodage' => 'Supported formats for conversion',
	'label_extensions_sortie' => 'Conversion formats',
	'label_format_final' => 'Desired format',
	'label_passes_1_encodage' => '1 pass',
	'label_passes_2_encodage' => '2 pass',
	'label_passes_encodage' => 'Number of passes for conversion',
	'label_presets_qualite' => 'Video conversion quality (libx264 only)',
	'label_relancer_encodage' => 'Relaunch conversion',
	'label_verifier_logiciels' => 'Verify the softwares again',
	'label_vorbis_qualite' => 'Conversion quality (Only Vorbis audio)',
	'lien_enlever_previsu' => 'Remove the preview',
	'lien_forcer_ffmpeg_infos' => 'Force the update of these informations',
	'lien_recharger' => 'Reload',
	'lien_recuperer_logo_fichier' => 'the file itself',
	'lien_supprimer_version' => 'Remove the @version@ version',
	'lien_supprimer_versions' => 'Remove all the versions of the document',
	'lien_voir_metadatas' => 'See the whole metadatas',

	// M
	'mediainfo_install' => 'MediaInfo on your server:',
	'message_confirmation_encodage' => 'Are you sure you want to (re)encode this document in the @version@ format?',
	'message_confirmation_encodages' => 'Are you sure you want to (re)encode this document in all the formats?',
	'message_confirmation_suppression_version' => 'Are you sure you want to remove the @version@ version of this document?',
	'message_confirmation_suppression_versions' => 'Are you sure you want to remove all the encoded versions of this document?',
	'message_document_encours_encodage_version' => 'This document is currently awaiting encoding in @version@.',
	'message_encodage_erreur_log' => 'The log file is as follows:',
	'message_encodage_objet_lie' => 'It is associated with the object @objet@ #@id_objet@:',
	'message_encodage_objet_lie_plusieurs' => 'These documents are associated  width the object @objet@ #@id_objet@:',
	'message_encodage_oui' => 'The document #@id_document@ has been properly encoded.',
	'message_encodage_sujet_erreur' => 'Encoding error',
	'message_encodage_sujet_oui' => 'Encoding success',
	'message_encodage_sujet_termine' => 'Encoding success',
	'message_encodage_unique_erreur' => 'The document #@id_document@ was not properly encoded to @extension@.',
	'message_encodage_unique_oui' => 'The document #@id_document@ has been properly encoded to @extension@.',
	'message_erreur_spipmotion_sh_vignettes' => 'The script for automatic generation of thumbnails is not accessible. This feature is disabled.',
	'message_sans_piste_audio' => 'This document has no audio',
	'message_texte_binaire_manquant' => 'A necessary software is not available on your server:',
	'message_texte_binaires_informer' => 'Please inform your system administrator.',
	'message_texte_binaires_informer_exec' => 'Your PHP configuration does not allow to run applications.',
	'message_texte_binaires_informer_safe_mode' => 'Safe mode is activated on your website. Binaries should be installed in the "@execdir@" folder of the server.',
	'message_texte_binaires_manquant' => 'Several software needed are not available on your server:',
	'message_titre_binaire_manquant' => 'A missing software',
	'message_titre_binaires_manquant' => 'Plusieurs logiciels manquant',

	// O
	'options_config' => 'Conversion configuration',

	// P
	'profil_encodage' => 'Conversion profile for the format: @format@',
	'profils_encodage_son' => 'Conversion profile(s) (Sound)',
	'profils_encodage_video' => 'Conversion profile(s) (Video)',

	// R
	'recuperer_infos' => 'Retrieve technical datas',
	'recuperer_logo' => 'Retrieve a screenshot',

	// S
	'select_all' => 'Select all',
	'son_bitrate_cbr' => 'Constant bitrate',
	'son_bitrate_vbr' => 'Variable bitrate',
	'spipmotion_boite' => 'Configuration of SPIPmotion',
	'spipmotion_descriptif' => 'This plugin allows to encode on the fly audio and video files.',
	'spipmotion_sh_install' => 'SPIPmotion conversion script:',
	'spipmotion_sh_vignettes_install' => 'Thumbnail creation script:',

	// T
	'th_avfilter_description' => 'Description',
	'th_avfilter_nom' => 'Name',
	'th_format_decode' => 'Decode',
	'th_format_encode' => 'Encode',
	'th_format_nom_complet' => 'Complete name',
	'th_format_nom_court' => 'Short name',
	'th_format_type' => 'Type',
	'thead_stat_duree' => 'File length / minute',
	'thead_stat_duree_long' => 'Converted file length (in minutes) per conversion minute',
	'thead_stat_octet' => 'File size / minute',
	'thead_stat_octet_long' => 'Original file size converted per converting minute',
	'thead_template_encodage' => 'Template',
	'thead_template_encodage_utilise' => 'Conversion template used',
	'titre_fichier_log' => 'Content of the log file for the ID #@id@ in the queue',
	'titre_fichiers_double_encodage' => 'These files appear to be the result of files encoded several times',
	'titre_fichiers_doubles_spip' => 'These files appear to be the result of files several time inserted in the database',
	'titre_page_erreurs' => 'Potential errors of SPIPmotion',
	'titre_page_ffmpeg_infos' => 'FFMpeg informations',
	'titre_page_file_stats' => 'Conversion statistics',
	'titre_page_metas_doc' => 'Metadatas of the document #@id@',

	// U
	'unselect_all' => 'Deselect all',

	// W
	'width_video' => 'Width of the output in px'
);

?>
