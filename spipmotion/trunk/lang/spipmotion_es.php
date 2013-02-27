<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/spipmotion?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bitrate' => 'Tasa de bits de vídeo de la salida en kb/s',
	'bitrate_audio' => 'Tasa de bits de audio de la salida en kb/s',
	'bouton_reencoder_tout' => 'Recodificar todos los archivos',
	'bouton_reencoder_tout_format' => 'Recodificar todos los archivos en el formato @format@',
	'bouton_reencoder_tout_message' => '¿Está seguro de querer reordenar la codificación de todos los archivos? Esta operación puede tardar mucho. ',
	'bouton_reencoder_tout_message_format' => '¿Está seguro de querer reordenar la codificación de todos los archivos en formato @format@? Esta operación puede tardar mucho. ',
	'bouton_supprimer_encodages_doubles' => 'Eliminar todos los archivos resultados de doble codificación',

	// C
	'caption_avfilter_geres' => 'Filtres vidéos (avfilters) gérés', # NEW
	'caption_codecs_geres' => 'Códecs soportados',
	'caption_formats_geres' => 'Formatos soportados',
	'chemin_executable' => 'Ruta de acceso del ejecutable FFmpeg en el servidor',
	'choix_debug_mode' => 'En mode debug, le webmestre du site reçoit par mail des informations à chaque encodage réussi ou raté', # NEW
	'choix_encodage_auto' => 'Los archivos se añadirán automáticamente en la lista de espera de codificación tras su inserción en la base de datos',
	'codec_type_audio' => 'Audio',
	'codec_type_soustitre' => 'Subtítulo',
	'codec_type_video' => 'Vídeo',

	// E
	'encoder_son' => '(Re)codificar este sonido',
	'encoder_son_version' => '(Re)codificar únicamente la versión @version@ de este sonido',
	'encoder_video' => '(Re)codificar este vídeo',
	'encoder_video_version' => '(Re)codificar únicamente la versión @version@ de este vídeo',
	'erreur_binaire' => 'Uno o varios softwares necesarios para el buen funcionamiento del plugin no están disponibles en su sistema.',
	'erreur_chemin_ffmpeg' => 'Ninguna codificación se podrá efectuar porque la ruta no funciona. ',
	'erreur_document_inexistant' => 'Este documento ya no existe.',
	'erreur_document_interdit' => 'Usted no dispone de los derechos suficientes para acceder a esta página',
	'erreur_document_plus_disponible' => 'Este documento no está disponible en el sitio',
	'erreur_formulaire_configuration' => 'El formulario contiene al menos un error.',
	'erreur_script_spipmotion_non_executable' => 'El script spipmotion.sh no se ha encontrado o no es ejecutable.',
	'erreur_valeur_int' => 'Este valor debe ser un número entero.',
	'erreur_valeur_int_superieur' => 'Cette valor debe ser superior a @val@.',
	'erreur_verifier_droits' => 'El script de codificación "script_bash/spipmotion.sh" no es ejecutable.',
	'explication_double_encodage_supprimer_secu' => 'Puede eliminarlo con toda seguridad, la codificación original está disponible.',
	'explication_encodeur' => 'Si vous choisissez l\'encodeur ffmpeg2theora pour vos fichiers Ogg vidéos, les choix de codecs vidéo et audios deviennent inutiles.', # NEW
	'explication_framerate' => 'Deje este campo vacío para guardar el mismo número de imágenes por segundo que en el original',
	'explication_presets_qualite' => 'Ce réglage permet de déterminer avec quel preset de qualité le codec libx264 va être utilisé. Ce réglage est déterminant pour la qualité du résultat final mais aussi sur la lenteur de l\'encodage.', # NEW
	'explication_qualite_video_ffmpeg2theora' => 'Ce réglage permet de déterminer le niveau général de qualité utilisé. Il modifie dynamiquement le bitrate vidéo utilisé.', # NEW
	'explication_vorbis_qualite' => 'Le codec vorbis (ou libvorbis) n\'utilisent pas de bit rate constant mais variable, il est donc préférable de spécifier la qualité non pas par un bit rate mais par un niveau de qualité souhaité.', # NEW
	'explications_extensions_prises_en_charge' => 'Les formats pris en charge sont l\'ensemble des formats de fichiers qui seront traités par SPIPmotion pour récupérer diverses informations (longueur, taille, vignette...)', # NEW
	'explications_extensions_prises_en_charge_encodage' => 'Estos formatos son aquéllos que serán transcodificados automáticamente o mediante solicitud',
	'explications_extensions_sortie' => 'Ces extensions sont les formats vers lesquels les fichiers seront encodés, vous pouvez en choisir plusieurs par type (attention à la lourdeur pour le serveur cependant).', # NEW
	'extensions_audio' => 'Formatos audios',
	'extensions_video' => 'Formatos vídeos',

	// F
	'ffmpeg2theora_install' => 'FFMpeg2Theora en su servidor:',
	'ffmpeg_install' => 'FFMpeg en su servidor:',
	'ffprobe_install' => 'FFprobe en su servidor:',
	'flvtool2_install' => 'FLVtool2 en su servidor:',
	'flvtoolplus_install' => 'FLVtool++ en su servidor:',
	'framerate' => 'Número de imágenes por segundo',
	'frequence_audio' => 'Frecuencia de procesamiento de señales audio',

	// G
	'gestion_format_entree' => 'Gestión de los formatos de entrada',
	'gestion_format_sortie' => 'Gestión de los formatos de salida',

	// H
	'height_video' => 'Altura de la salida en px',

	// I
	'info_aspect_ratio' => 'Relación de visualizaciones del vídeo',
	'info_audiobitrate' => 'Tasa de bits de audio: ',
	'info_audiochannels' => 'Número de canales audios:',
	'info_audiocodec' => 'Códec audio: ',
	'info_audiosamplerate' => 'Fréquence d\'échantillonnage audio : ', # NEW
	'info_bitrate' => 'Tasa total de bits: ',
	'info_compiler_avfilter-support' => 'Compilé avec le support des filtres vidéos (anciennement vhook) :', # NEW
	'info_compiler_build_date_timestamp' => 'Fecha de compilación:',
	'info_compiler_configuration' => 'Variables de compilación',
	'info_compiler_ffmpeg-php' => 'PHP dispose de l\'extension FFmpeg :', # NEW
	'info_compiler_ffmpeg-php-builddate' => 'Fecha de compilación',
	'info_compiler_ffmpeg-php-gdenabled' => 'Support de GD dans FFmpeg-php', # NEW
	'info_compiler_ffmpeg-php-infos' => 'Informaciones de compilación de FFmpeg-php',
	'info_compiler_ffmpeg-php-version' => 'Versión',
	'info_compiler_ffmpeg_version' => 'Versión de FFMpeg:',
	'info_compiler_gcc' => 'Versión del compilador gcc:',
	'info_compiler_libavcodec_build_number' => 'Numéro de version de libavcodec à la compilation', # NEW
	'info_compiler_libavcodec_version_number' => 'Número de versión de libavcodec utilizada',
	'info_compiler_vhook-support' => 'Compilé avec le support des vhooks', # NEW
	'info_document_encodage_en_cours_attente_avant' => '@nb@ conversiones están a la espera antes de comenzar con ésta.',
	'info_document_encodage_en_cours_attente_avant_unique' => 'Este documento será el próximo en ser codificado. ',
	'info_document_encodage_en_cours_plusieurs' => 'Este documento está a la espera de conversión en @nb@ formatos.',
	'info_document_encodage_en_cours_unique' => 'Ce document est en attente d\'encodage en un format.', # NEW
	'info_document_encodage_en_erreur' => 'La codificación de este documento es errónea.',
	'info_document_encodage_en_erreur_contacter' => 'Veuillez contacter un administrateur pour le lui signaler.', # NEW
	'info_document_encodage_en_erreur_relancer_url' => 'Vous pouvez relancer l\'encodage sur <a href="@url@">cette page</a>.', # NEW
	'info_document_encodage_realise' => 'Ya se ha convertido en: ',
	'info_document_encodage_termine' => 'La codificación de este documento ha terminado.',
	'info_document_encodage_termine_recharge' => 'Puede recargar la página para ver el contenido definitivo.',
	'info_document_encode_formats' => 'Este documento se ha convertido en: ',
	'info_duree' => 'Duración: ',
	'info_erreurs' => 'Errores',
	'info_ffmpeg2theora_libtheora_version' => 'Version de libtheora utilisée par ffmpeg2theora :', # NEW
	'info_ffmpeg2theora_version' => 'Version de ffmpeg2theora :', # NEW
	'info_ffprobe_disponible' => 'FFprobe est disponible', # NEW
	'info_flvtool2_version' => 'Versión de FLVtool2 instalada:',
	'info_flvtoolplus_version' => 'Versión de FLVtool++ instalada:',
	'info_format' => 'Formato: ',
	'info_framecount' => 'Número de imágenes: ',
	'info_framerate' => 'Framerate : ', # NEW
	'info_infos_techniques' => 'Informaciones técnicas',
	'info_mediainfo_version' => 'Versión de MediaInfo instalada:',
	'info_mime' => 'Type mime : ', # NEW
	'info_nom_fichier' => 'Nombre del archivo: ',
	'info_page_ffmpeg_infos' => 'Cette page n\'est qu\'informative. Elle indique la configuration complète de l\'installation de FFmpeg sur votre système.', # NEW
	'info_pixelformat' => 'Formato de píxel:',
	'info_poid_fichier' => 'Tamaño del archivo:',
	'info_rotation' => 'Ángulo de rotación:',
	'info_spipmotion_sh_chemin' => 'Ruta del script:',
	'info_spipmotion_sh_version' => 'Versión del script:',
	'info_version' => 'Versión @version@',
	'info_version_original' => 'Original',
	'info_videobitrate' => 'Tasas de bits vídeo: ',
	'info_videocodec' => 'Códec vídeo: ',
	'info_voir_log_erreur' => 'Ver el historial',

	// L
	'label_codec_son' => 'Códec audio a utilizar',
	'label_codec_video' => 'Códec vídeo a utilizar',
	'label_debug_mode' => 'Mode debug', # NEW
	'label_encodage_auto' => 'Codificar automáticamente',
	'label_encodeur' => 'Codificador',
	'label_extensions_prises_en_charge' => 'Formats pris en charge de base', # NEW
	'label_extensions_prises_en_charge_encodage' => 'Formats pris en charge pour l\'encodage', # NEW
	'label_extensions_sortie' => 'Formatos de codificación',
	'label_format_final' => 'Formato deseado',
	'label_passes_1_encodage' => 'Une passe', # NEW
	'label_passes_2_encodage' => 'Deux passes', # NEW
	'label_passes_encodage' => 'Nombre de passes pour l\'encodage', # NEW
	'label_presets_qualite' => 'Qualité d\'encodage vidéo (libx264 uniquement)', # NEW
	'label_qualite_video_ffmpeg2theora' => 'Qualité d\'encodage vidéo (si utilisation de ffmpeg2theora uniquement)', # NEW
	'label_relancer_encodage' => 'Relancer l\'encodage', # NEW
	'label_verifier_logiciels' => 'Revérifier les logiciels', # NEW
	'label_vorbis_qualite' => 'Qualité d\'encodage audio (Vorbis uniquement)', # NEW
	'lien_enlever_previsu' => 'Quitar la previsualización',
	'lien_forcer_ffmpeg_infos' => 'Forcer la mise à de ces informations', # NEW
	'lien_recharger' => 'Recargar',
	'lien_recuperer_logo_fichier' => 'le fichier lui-même', # NEW
	'lien_supprimer_version' => 'Eliminar la versión @version@',
	'lien_supprimer_versions' => 'Eliminar todas las versiones del documento',
	'lien_voir_metadatas' => 'Ver todos los metadatos',

	// M
	'mediainfo_install' => 'MediaInfo en vuestro servidor:',
	'message_confirmation_encodage' => 'Êtes vous sûr de vouloir relancer l\'encodage de ce document au format @version@ ?', # NEW
	'message_confirmation_encodages' => 'Êtes vous sûr de vouloir relancer l\'encodage de ce document dans tous les formats ?', # NEW
	'message_confirmation_suppression_version' => 'Êtes vous sûr de vouloir supprimer la version @version@ de ce document?', # NEW
	'message_confirmation_suppression_versions' => 'Êtes vous sûr de vouloir supprimer tous les encodages de ce document?', # NEW
	'message_document_encours_encodage_version' => 'Este documento está actualmente a la espera de codificación en @version@.',
	'message_encodage_erreur_log' => 'Le fichier de log est le suivant :', # NEW
	'message_encodage_objet_lie' => 'Se ha asociado al objeto @objet@ #@id_objet@:',
	'message_encodage_objet_lie_plusieurs' => 'Ces documents sont associés  à l\'objet @objet@ #@id_objet@ :', # NEW
	'message_encodage_oui' => 'El documento @id_document@ se ha codificado correctamente.',
	'message_encodage_sujet_erreur' => 'Codificación errónea',
	'message_encodage_sujet_oui' => 'Codificación exitosa',
	'message_encodage_sujet_termine' => 'Codificación terminada',
	'message_encodage_unique_erreur' => 'Le document @id_document@ n\'a pas été correctement encodé en @extension@.', # NEW
	'message_encodage_unique_oui' => 'Le document @id_document@ a été correctement encodé en @extension@.', # NEW
	'message_erreur_spipmotion_sh_vignettes' => 'Le script de génération de vignettes automatique n\'est pas accesssible. Cette fonctionnalité est donc désactivée.', # NEW
	'message_sans_piste_audio' => 'Este documento no tiene pista de audio',
	'message_texte_binaire_manquant' => 'Un logiciel nécessaire n\'est pas disponible sur votre serveur :', # NEW
	'message_texte_binaires_informer' => 'Veuillez en informer votre administrateur.', # NEW
	'message_texte_binaires_informer_exec' => 'Votre configuration de PHP ne permet pas d\'exécuter d\'applications.', # NEW
	'message_texte_binaires_informer_safe_mode' => 'Le safe mode est activé sur votre site. Les binaires nécessaires doivent se trouver dans le répertoire "@execdir@" du serveur.', # NEW
	'message_texte_binaires_manquant' => 'Varios softwares necesarios no están disponibles en su servidor:',
	'message_titre_binaire_manquant' => 'Falta un software',
	'message_titre_binaires_manquant' => 'Faltan varios softwares',

	// O
	'options_config' => 'Configuración de las conversiones',

	// P
	'profil_encodage' => 'Profil d\'encodage pour le format : @format@', # NEW
	'profils_encodage_son' => 'Perfil(es) de conversión (Sonido)',
	'profils_encodage_video' => 'Perfil(es) de conversión (Vídeo)',

	// R
	'recuperer_infos' => 'Recuperar los datos técnicos',
	'recuperer_logo' => 'Récupérer une vignette', # NEW

	// S
	'select_all' => 'Seleccionar todo',
	'spipmotion_boite' => 'Configuración de SPIPmotion',
	'spipmotion_descriptif' => 'Ce plugin permet d\'encoder à la volée des documents audios et vidéos.', # NEW
	'spipmotion_sh_install' => 'Script de conversión de SPIPmotion:',
	'spipmotion_sh_vignettes_install' => 'Script de création de vignettes :', # NEW

	// T
	'th_avfilter_description' => 'Descripción',
	'th_avfilter_nom' => 'Nombre',
	'th_format_decode' => 'Descodifica',
	'th_format_encode' => 'Codifica',
	'th_format_nom_complet' => 'Nombre completo',
	'th_format_nom_court' => 'Nombre corto',
	'th_format_type' => 'Tipo',
	'thead_stat_duree' => 'Duración de archivo / min',
	'thead_stat_duree_long' => 'Duración del archivo (en minutos) convertida por minuto de conversión',
	'thead_stat_octet' => 'Tamaño / min',
	'thead_stat_octet_long' => 'Taille du fichier original encodée par minute d\'encodage', # NEW
	'thead_template_encodage' => 'Perfil',
	'thead_template_encodage_utilise' => 'Perfil de conversión utilizado',
	'titre_fichier_log' => 'Contenu du fichier de log de l\'id #@id@ dans la file d\'attente', # NEW
	'titre_fichiers_double_encodage' => 'Ces fichiers semblent être le résultat de fichiers encodés plusieurs fois', # NEW
	'titre_fichiers_doubles_spip' => 'Ces fichiers semblent être le résultat de fichiers insérés plusieurs fois en base', # NEW
	'titre_page_erreurs' => 'Errores potenciales de SPIPmotion',
	'titre_page_ffmpeg_infos' => 'Informations sur FFMpeg', # NEW
	'titre_page_file_stats' => 'Estadísticas de conversión ',
	'titre_page_metas_doc' => 'Metadatos del documento #@id@',

	// U
	'unselect_all' => 'Deshacer toda la selección',

	// W
	'width_video' => 'Anchura de la salida en px'
);

?>
