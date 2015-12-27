<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/spipmotion?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
	'caption_avfilter_geres' => 'Filtros de vídeos (avfilters) gestionados',
	'caption_codecs_geres' => 'Códecs soportados',
	'caption_formats_geres' => 'Formatos soportados',
	'chemin_executable' => 'Ruta de acceso del ejecutable FFmpeg en el servidor',
	'choix_debug_mode' => 'En modo de depuración de programas, el webmaster del sitio recibe por mail información de cada codificación exitosa o fallida',
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
	'explication_framerate' => 'Deje este campo vacío para guardar el mismo número de imágenes por segundo que en el original',
	'explication_presets_qualite' => 'Este reglaje permite determinar con qué preajuste de calidad el códec libx264 será utilizado. Este reglaje es determinante para la calidad del resultado final pero también en la lentitud de la codificación. ',
	'explication_vorbis_qualite' => 'El códec vorbis (o libvorbis) no utiliza tasa de bits constante sino variable, es preferible especificar la calidad no por una tasa de bits sino por un nivel de calidad deseable.',
	'explications_extensions_prises_en_charge' => 'Los formatos cargados son todos los formatos de archivos que serán tratados por SPIPmotion para recuperar diversas informaciones (longitud, tamaño, miniatura...)',
	'explications_extensions_prises_en_charge_encodage' => 'Estos formatos son aquéllos que serán transcodificados automáticamente o mediante solicitud',
	'explications_extensions_sortie' => 'Estas extensiones son los formatos en los que los archivos serán codificados, puede elegir varios por tipo (atención no obstante al peso para el servidor).',
	'extensions_audio' => 'Formatos audios',
	'extensions_video' => 'Formatos vídeos',

	// F
	'ffmpeg_install' => 'FFMpeg en su servidor:',
	'ffprobe_install' => 'FFprobe en su servidor:',
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
	'info_audiosamplerate' => 'Frecuencia de procesamiento de señales de audio: ',
	'info_bitrate' => 'Tasa total de bits: ',
	'info_bitrate_mode' => 'Modo:',
	'info_compiler_avfilter-support' => 'Compilado con el soporte de filtros de vídeo (antiguamente vhook) :',
	'info_compiler_build_date_timestamp' => 'Fecha de compilación:',
	'info_compiler_configuration' => 'Variables de compilación',
	'info_compiler_ffmpeg-php' => 'PHP dispone de la extensión FFmpeg :',
	'info_compiler_ffmpeg-php-builddate' => 'Fecha de compilación',
	'info_compiler_ffmpeg-php-gdenabled' => 'Soporte de GD en FFmpeg-php',
	'info_compiler_ffmpeg-php-infos' => 'Información de compilación de FFmpeg-php',
	'info_compiler_ffmpeg-php-version' => 'Versión',
	'info_compiler_ffmpeg_version' => 'Versión de FFMpeg:',
	'info_compiler_gcc' => 'Versión del compilador gcc:',
	'info_compiler_libavcodec_build_number' => 'Número de versión de libavcodec en la compilación',
	'info_compiler_libavcodec_version_number' => 'Número de versión de libavcodec utilizada',
	'info_compiler_vhook-support' => 'Compilado con soporte de los vhooks',
	'info_document_encodage_en_cours_attente_avant' => '@nb@ conversiones están a la espera antes de comenzar con ésta.',
	'info_document_encodage_en_cours_attente_avant_unique' => 'Este documento será el próximo en ser codificado. ',
	'info_document_encodage_en_cours_plusieurs' => 'Este documento está a la espera de conversión en @nb@ formatos.',
	'info_document_encodage_en_cours_unique' => 'Este documento está a la espera de conversión en un formato. ',
	'info_document_encodage_en_erreur' => 'La codificación de este documento es errónea.',
	'info_document_encodage_en_erreur_contacter' => 'Contacte por favor a un administrador para informarle.',
	'info_document_encodage_en_erreur_relancer_url' => 'Puede relanzar la codificación en <a href="@url@">esta página</a>.',
	'info_document_encodage_realise' => 'Ya se ha convertido en: ',
	'info_document_encodage_termine' => 'La codificación de este documento ha terminado.',
	'info_document_encodage_termine_recharge' => 'Puede recargar la página para ver el contenido definitivo.',
	'info_document_encode_formats' => 'Este documento se ha convertido en: ',
	'info_duree' => 'Duración: ',
	'info_encodeur' => 'Codificador: ',
	'info_erreurs' => 'Errores',
	'info_extension' => 'Extensión: ',
	'info_ffprobe_disponible' => 'FFprobe está disponible',
	'info_flvtoolplus_version' => 'Versión de FLVtool++ instalada:',
	'info_format' => 'Formato: ',
	'info_framecount' => 'Número de imágenes: ',
	'info_framerate' => 'Imágenes por segundo: ',
	'info_infos_techniques' => 'Informaciones técnicas',
	'info_media' => 'Tipo de medio:',
	'info_mediainfo_version' => 'Versión de MediaInfo instalada:',
	'info_mime' => 'Internet media type: ',
	'info_nom_fichier' => 'Nombre del archivo: ',
	'info_page_ffmpeg_infos' => 'Esta página no es más que informativa. Indica la configuración completa de instalación de FFmpeg en vuestro sistema.',
	'info_pixelformat' => 'Formato de píxel:',
	'info_poid_fichier' => 'Tamaño del archivo:',
	'info_reencoder' => '(Re)codificar',
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
	'label_debug_mode' => 'Modo depuración de programas',
	'label_encodage_auto' => 'Codificar automáticamente',
	'label_encodeur' => 'Codificador',
	'label_extensions_prises_en_charge' => 'Formatos de base',
	'label_extensions_prises_en_charge_encodage' => 'Formatos de base para la conversión',
	'label_extensions_sortie' => 'Formatos de codificación',
	'label_format_final' => 'Formato deseado',
	'label_passes_1_encodage' => 'Una pasada',
	'label_passes_2_encodage' => 'Dos pasadas',
	'label_passes_encodage' => 'Número de pasadas para la conversión',
	'label_presets_qualite' => 'Calidad de conversión de video (libx264 solamente)',
	'label_relancer_encodage' => 'Relanzar la conversión',
	'label_verifier_logiciels' => 'Reconfirmar los softwares',
	'label_vorbis_qualite' => 'Calidad de conversión de audio (Vorbis solamente)',
	'lien_enlever_previsu' => 'Quitar la previsualización',
	'lien_forcer_ffmpeg_infos' => 'Forzar la actualización de la información ',
	'lien_recharger' => 'Recargar',
	'lien_recuperer_logo_fichier' => 'el propio archivo',
	'lien_supprimer_version' => 'Eliminar la versión @version@',
	'lien_supprimer_versions' => 'Eliminar todas las versiones del documento',
	'lien_voir_metadatas' => 'Ver todos los metadatos',

	// M
	'mediainfo_install' => 'MediaInfo en vuestro servidor:',
	'message_confirmation_encodage' => 'Está seguro de querer relanzar la codificación de este documento en formato @version@ ?',
	'message_confirmation_encodages' => '¿Está seguro de querer relanzar la codificación de este documento en todos los formatos?',
	'message_confirmation_suppression_version' => '¿Está seguro de querer eliminar la versión @version@ de este documento?',
	'message_confirmation_suppression_versions' => '¿Está seguro de querer eliminar todas las codificaciones de este documento?',
	'message_document_encours_encodage_version' => 'Este documento está actualmente a la espera de codificación en @version@.',
	'message_encodage_erreur_log' => 'El archivo del historial es el siguiente:',
	'message_encodage_objet_lie' => 'Se ha asociado al objeto @objet@ #@id_objet@:',
	'message_encodage_objet_lie_plusieurs' => 'Estos documentos están asociados al objeto @objet@ #@id_objet@:',
	'message_encodage_oui' => 'El documento @id_document@ se ha codificado correctamente.',
	'message_encodage_sujet_erreur' => 'Codificación errónea',
	'message_encodage_sujet_oui' => 'Codificación exitosa',
	'message_encodage_sujet_termine' => 'Codificación terminada',
	'message_encodage_unique_erreur' => 'El documento @id_document@ no se ha codificado correctamente en @extension@.',
	'message_encodage_unique_oui' => 'El documento @id_document@ se ha codificado correctamente en @extension@.',
	'message_erreur_spipmotion_sh_vignettes' => 'El script de generación automática de miniaturas no está accesible. Esta función está desactivada.',
	'message_sans_piste_audio' => 'Este documento no tiene pista de audio',
	'message_texte_binaire_manquant' => 'Un software necesario no está disponible en su servidor:',
	'message_texte_binaires_informer' => 'Informe por favor a su administrador.',
	'message_texte_binaires_informer_exec' => 'Su configuración de PHP no permite ejecutar aplicaciones.',
	'message_texte_binaires_informer_safe_mode' => 'El modo seguro está activado para su sitio web. Los binarios necesarios deben encontrarse en el directorio "@execdir@" del servidor.',
	'message_texte_binaires_manquant' => 'Varios softwares necesarios no están disponibles en su servidor:',
	'message_titre_binaire_manquant' => 'Falta un software',
	'message_titre_binaires_manquant' => 'Faltan varios softwares',

	// O
	'options_config' => 'Configuración de las conversiones',

	// P
	'profil_encodage' => 'Perfil de conversión para el formato: @format@',
	'profils_encodage_son' => 'Perfil(es) de conversión (Sonido)',
	'profils_encodage_video' => 'Perfil(es) de conversión (Vídeo)',

	// R
	'recuperer_infos' => 'Recuperar los datos técnicos',
	'recuperer_logo' => 'Recuperar una miniatura',

	// S
	'select_all' => 'Seleccionar todo',
	'son_bitrate_cbr' => 'Bitrate constante',
	'son_bitrate_vbr' => 'Bitrate variable',
	'spipmotion_boite' => 'Configuración de SPIPmotion',
	'spipmotion_descriptif' => 'Este plugin permite codificar al momento documentos de audio y de vídeo. ',
	'spipmotion_sh_install' => 'Script de conversión de SPIPmotion:',
	'spipmotion_sh_vignettes_install' => 'Script de creación de miniaturas:',

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
	'thead_stat_octet_long' => 'Tamaño del archivo original convertido por minuto de conversión',
	'thead_template_encodage' => 'Perfil',
	'thead_template_encodage_utilise' => 'Perfil de conversión utilizado',
	'titre_fichier_log' => 'Contenido del archivo de historial del id #@id@ en la lista de espera',
	'titre_fichiers_double_encodage' => 'Estos archivos parecen ser el resultado de archivos codificados varias veces',
	'titre_fichiers_doubles_spip' => 'Estos archivos parecen ser el resultado de archivos insertados varias veces en la base de datos',
	'titre_page_erreurs' => 'Errores potenciales de SPIPmotion',
	'titre_page_ffmpeg_infos' => 'Información sobre FFMpeg',
	'titre_page_file_stats' => 'Estadísticas de conversión ',
	'titre_page_metas_doc' => 'Metadatos del documento #@id@',

	// U
	'unselect_all' => 'Deshacer toda la selección',

	// W
	'width_video' => 'Anchura de la salida en px'
);

?>
