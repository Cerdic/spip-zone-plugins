<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/spipmotion/lang
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bitrate' => 'Bitrate vidéo de la sortie en kb/s',
	'bitrate_audio' => 'Bitrate audio de la sortie en kb/s',
	'bouton_encoder' => 'Encoder',
	'bouton_reencoder_tout' => 'Réencoder tous les fichiers',
	'bouton_reencoder_tout_format' => 'Réencoder tous les fichiers au format @format@',
	'bouton_reencoder_tout_message' => 'Êtes vous sûr de vouloir relancer l\'encodage de tous les fichiers. Cette opération peut être très longue.',
	'bouton_reencoder_tout_message_format' => 'Êtes vous sûr de vouloir relancer l\'encodage de tous les fichiers au format @format@. Cette opération peut être très longue.',

	// C
	'caption_avfilter_geres' => 'Filtres vidéos (avfilters) gérés',
	'caption_codecs_geres' => 'Codecs pris en charge',
	'caption_formats_geres' => 'Formats pris en charge',
	'chemin_executable' => 'Chemin de l\'exécutable FFmpeg sur le serveur',
	'choix_debug_mode' => 'En mode debug, le webmestre du site reçoit par mail des informations à chaque encodage réussi ou raté',
	'choix_encodage_auto' => 'Les fichiers seront ajoutés automatiquement dans la file d\'attente d\'encodage dès leur insertion dans la base de donnée',
	'codec_type_audio' => 'Audio',
	'codec_type_soustitre' => 'Sous-titre',
	'codec_type_video' => 'Vidéo',

	// D
	'document_dans_file_attente' => 'Ce document est dans la file d\'attente pour l\'encodage',

	// E
	'encoder_son' => '(Ré)encoder ce son',
	'encoder_son_version' => '(Ré)encoder uniquement la version @version@ de ce son',
	'encoder_video' => '(Ré)encoder cette vidéo',
	'encoder_video_version' => '(Ré)encoder uniquement la version @version@ de cette vidéo',
	'erreur_binaire' => 'Un ou plusieurs logiciels nécessaires au bon fonctionnement du plugin ne sont pas disponibles sur votre système.',
	'erreur_chemin_ffmpeg' => 'Aucun encodage ne pourra être effectué car le chemin ne fonctionne pas.',
	'erreur_document_plus_disponible' => 'Ce document n\'est plus disponible sur le site',
	'erreur_formulaire_configuration' => 'Le formulaire contient au moins une erreur.',
	'erreur_script_spipmotion_non_executable' => 'Le script spipmotion.sh n\'a pas été trouvé ou n\'est pas exécutable.',
	'erreur_valeur_int' => 'Cette valeur doit être un chiffre entier.',
	'erreur_valeur_int_superieur' => 'Cette valeur doit être supérieure à @val@.',
	'erreur_verifier_droits' => 'Le script d\'encodage "script_bash/spipmotion.sh" n\'est pas executable.',
	'explication_encodeur' => 'Si vous choisissez l\'encodeur ffmpeg2theora pour vos fichiers Ogg vidéos, les choix de codecs vidéo et audios deviennent inutiles.',
	'explication_framerate' => 'Laissez ce champs vide pour garder le même nombre de frames par seconde que l\'original',
	'explication_presets_qualite' => 'Ce réglage permet de déterminer avec quel preset de qualité le codec libx264 va être utilisé. Ce réglage est déterminant pour la qualité du résultat final mais aussi sur la lenteur de l\'encodage.',
	'explication_qualite_video_ffmpeg2theora' => 'Ce réglage permet de déterminer le niveau général de qualité utilisé. Il modifie dynamiquement le bitrate vidéo utilisé.',
	'explication_vorbis_qualite' => 'Le codec vorbis (ou libvorbis) n\'utilisent pas de bit rate constant mais variable, il est donc préférable de spécifier la qualité non pas par un bit rate mais par un niveau de qualité souhaité.',
	'explications_extensions_prises_en_charge' => 'Les formats pris en charge sont l\'ensemble des formats de fichiers qui seront traités par SPIPmotion pour récupérer diverses informations (longueur, taille, vignette...)',
	'explications_extensions_prises_en_charge_encodage' => 'Ces formats sont ceux qui seront transcodés automatiquement ou à la demande',
	'explications_extensions_sortie' => 'Ces extensions sont les formats vers lesquels les fichiers seront encodés, vous pouvez en choisir plusieurs par type (attention à la lourdeur pour le serveur cependant).',
	'extensions_audio' => 'Formats audios',
	'extensions_video' => 'Formats vidéos',

	// F
	'ffmpeg_install' => 'FFMpeg sur votre serveur :',
	'ffmpeg2theora_install' => 'FFMpeg2Theora sur votre serveur :',
	'flvtool2_install' => 'FLVtool2 sur votre serveur :',
	'flvtoolplus_install' => 'FLVtool++ sur votre serveur :',
	'framerate' => 'Nombre d\'images par seconde',
	'frequence_audio' => 'Fréquence d\'échantillonage audio',

	// G
	'gestion_format_entree' => 'Gestion des formats d\'entrée',
	'gestion_format_sortie' => 'Gestion des formats de sortie',

	// H
	'height_video' => 'Hauteur de la sortie en px',

	// I
	'info_aucun_resultat_attente' => 'Aucun document n\'est en attente d\'encodage.',
	'info_audiobitrate' => 'Bitrate audio : ',
	'info_audiochannels' => 'Nombre de canaux audios : ',
	'info_audiocodec' => 'Codec audio : ',
	'info_audiosamplerate' => 'Fréquence d\'échantillonnage audio : ',
	'info_bitrate' => 'Bitrate total : ',
	'info_compiler_avfilter-support' => 'Compilé avec le support des filtres vidéos (anciennement vhook) :',
	'info_compiler_build_date_timestamp' => 'Date de compilation :',
	'info_compiler_configuration' => 'Variables de compilation',
	'info_compiler_ffmpeg-php' => 'PHP dispose de l\'extension FFmpeg :',
	'info_compiler_ffmpeg-php-builddate' => 'Date de compilation',
	'info_compiler_ffmpeg-php-gdenabled' => 'Support de GD dans FFmpeg-php',
	'info_compiler_ffmpeg-php-infos' => 'Informations de compilation de FFmpeg-php',
	'info_compiler_ffmpeg-php-version' => 'Version',
	'info_compiler_ffmpeg_version' => 'Version de FFMpeg :',
	'info_compiler_gcc' => 'Version du compilateur gcc :',
	'info_compiler_libavcodec_build_number' => 'Numéro de version de libavcodec à la compilation',
	'info_compiler_libavcodec_version_number' => 'Numéro de version de libavcodec utilisé',
	'info_compiler_vhook-support' => 'Compilé avec le support des vhooks',
	'info_document_encodage_en_cours' => 'Ce document est en cours d\'encodage.',
	'info_document_encodage_en_cours_attente_avant' => '@nb@ encodages sont en attente avant de commencer celui-ci.',
	'info_document_encodage_en_cours_attente_avant_unique' => 'Ce document sera le prochain à être encodé.',
	'info_document_encodage_en_cours_plusieurs' => 'Ce document est en attente d\'encodage en @nb@ formats.',
	'info_document_encodage_en_cours_unique' => 'Ce document est en attente d\'encodage en un format.',
	'info_document_encodage_en_erreur' => 'L\'encodage de ce document est en erreur.',
	'info_document_encodage_en_erreur_contacter' => 'Veuillez contacter un administrateur pour le lui signaler.',
	'info_document_encodage_en_erreur_relancer_url' => 'Vous pouvez relancer l\'encodage sur <a href="@url@">cette page</a>.',
	'info_document_encodage_realise' => 'Il a déjà été encodé en : ',
	'info_document_encodage_termine' => 'L\'encodage de ce document est termine.',
	'info_document_encodage_termine_recharge' => 'Vous pouvez recharger la page pour voir le contenu définitif.',
	'info_document_encode_formats' => 'Ce document a été encodé en : ',
	'info_duree' => 'Durée : ',
	'info_erreurs' => 'Erreurs',
	'info_ffmpeg2theora_libtheora_version' => 'Version de libtheora utilisée par ffmpeg2theora :',
	'info_ffmpeg2theora_version' => 'Version de ffmpeg2theora :',
	'info_flvtool2_version' => 'Version de FLVtool2 installée :',
	'info_flvtoolplus_version' => 'Version de FLVtool++ installée :',
	'info_format' => 'Format : ',
	'info_framecount' => 'Nombre de frames : ',
	'info_framerate' => 'Framerate : ',
	'info_infos_techniques' => 'Informations techniques',
	'info_installer_encoder_ftp' => 'En tant qu\'administrateur, vous pouvez installer (par FTP) des fichiers de type .AVI, .MPG, .MP4 ou .MOV dans le dossier "tmp/upload" pour ensuite les encoder au format FLV ici.',
	'info_mediainfo_version' => 'Version de MediaInfo installée :',
	'info_mime' => 'Type mime : ',
	'info_nom_fichier' => 'Nom du fichier : ',
	'info_nombre_encodage_attente' => 'Un document est en attente d\'encodage',
	'info_nombre_encodages_attentes' => 'Il y a @nb@ documents en attente d\'encodage',
	'info_nombre_encodes' => '@nb@ fichiers ont été correctement encodés.',
	'info_objet_non_publie' => 'Non publié',
	'info_page_ffmpeg_infos' => 'Cette page n\'est qu\'informative. Elle indique la configuration complète de l\'installation de FFmpeg sur votre système.',
	'info_pixelformat' => 'Format de pixel :',
	'info_poid_fichier' => 'Taille du fichier :',
	'info_profils_son' => 'Profil(s) d\'encodage son',
	'info_profils_video' => 'Profil(s) d\'encodage vidéo',
	'info_reencoder' => '(Ré)encoder',
	'info_relancer_erreurs' => 'Relancer tous les encodages en erreur',
	'info_rotation' => 'Angle de rotation :',
	'info_selectionner_fichier_encoder' => 'Vous pouvez encoder un des fichiers suivants',
	'info_spipmotion_sh_chemin' => 'Chemin du script :',
	'info_spipmotion_sh_version' => 'Version du script :',
	'info_statut_encode_en_cours' => 'En cours',
	'info_statut_encode_erreur' => 'En erreur',
	'info_statut_encode_non' => 'En attente',
	'info_statut_encode_oui' => 'Encodé',
	'info_statut_non_publie' => 'Cet objet n\'est pas publié',
	'info_version' => 'Version @version@',
	'info_version_original' => 'Original',
	'info_videobitrate' => 'Bitrate vidéo : ',
	'info_videocodec' => 'Codec vidéo : ',
	'info_voir_log_erreur' => 'Voir le log',
	'install_ajout_champs_documents' => 'Ajout des champs sur spip_documents',
	'install_creation_base' => 'Création de la base de spipmotion',

	// L
	'label_codec_son' => 'Codec audio à utiliser',
	'label_codec_video' => 'Codec vidéo à utiliser',
	'label_debug_mode' => 'Mode debug',
	'label_encodage_auto' => 'Encoder automatiquement',
	'label_encodeur' => 'Encodeur',
	'label_extensions_prises_en_charge' => 'Formats pris en charge de base',
	'label_extensions_prises_en_charge_encodage' => 'Formats pris en charge pour l\'encodage',
	'label_extensions_sortie' => 'Formats d\'encodage',
	'label_format_final' => 'Format désiré',
	'label_passes_1_encodage' => 'Une passe',
	'label_passes_2_encodage' => 'Deux passes',
	'label_passes_encodage' => 'Nombre de passes pour l\'encodage',
	'label_presets_qualite' => 'Qualité d\'encodage vidéo (libx264 uniquement)',
	'label_qualite_video_ffmpeg2theora' => 'Qualité d\'encodage vidéo (si utilisation de ffmpeg2theora uniquement)',
	'label_relancer_encodage' => 'Relancer l\'encodage',
	'label_verifier_logiciels' => 'Revérifier les logiciels',
	'label_vorbis_qualite' => 'Qualité d\'encodage audio (Vorbis uniquement)',
	'lien_enlever_previsu' => 'Enlever la prévisualisation',
	'lien_ffmpeg_linux' => 'Installer FFmpeg sur Linux',
	'lien_ffmpeg_mac' => 'Installer FFmpeg sur Mac OSX',
	'lien_flvtool' => 'Installer FLVTool2',
	'lien_forcer_ffmpeg_infos' => 'Forcer la mise à de ces informations',
	'lien_recharger' => 'Recharger',
	'lien_recuperer_logo_fichier' => 'le fichier lui-même',
	'lien_supprimer_version' => 'Supprimer la version @version@',
	'lien_supprimer_versions' => 'Supprimer toutes les versions du document',

	// M
	'mediainfo_install' => 'MediaInfo sur votre serveur :',
	'message_confirmation_encodage' => 'Êtes vous sûr de vouloir relancer l\'encodage de ce document au format @version@ ?',
	'message_confirmation_encodages' => 'Êtes vous sûr de vouloir relancer l\'encodage de ce document dans tous les formats ?',
	'message_confirmation_suppression_version' => 'Êtes vous sûr de vouloir supprimer la version @version@ de ce document?',
	'message_confirmation_suppression_versions' => 'Êtes vous sûr de vouloir supprimer tous les encodages de ce document?',
	'message_document_encours_encodage_version' => 'Ce document est actuellement en attente d\'encodage en @version@.',
	'message_encodage_erreur_log' => 'Le fichier de log est le suivant :',
	'message_encodage_objet_lie' => 'Il est associé à l\'objet @objet@ #@id_objet@ :',
	'message_encodage_objet_lie_plusieurs' => 'Ces documents sont associés  à l\'objet @objet@ #@id_objet@ :',
	'message_encodage_oui' => 'Le document @id_document@ a été correctement encodé.',
	'message_encodage_sujet_erreur' => 'Encodage en erreur',
	'message_encodage_sujet_oui' => 'Encodage réussi',
	'message_encodage_sujet_termine' => 'Encodage terminé',
	'message_encodage_unique_erreur' => 'Le document @id_document@ n\'a pas été correctement encodé en @extension@.',
	'message_encodage_unique_oui' => 'Le document @id_document@ a été correctement encodé en @extension@.',
	'message_erreur_spipmotion_sh_vignettes' => 'Le script de génération de vignettes automatique n\'est pas accesssible. Cette fonctionnalité est donc désactivée.',
	'message_texte_binaire_manquant' => 'Un logiciel nécessaire n\'est pas disponible sur votre serveur :',
	'message_texte_binaires_informer' => 'Veuillez en informer votre administrateur.',
	'message_texte_binaires_informer_exec' => 'Votre configuration de PHP ne permet pas d\'exécuter d\'applications.',
	'message_texte_binaires_informer_safe_mode' => 'Le safe mode est activé sur votre site. Les binaires nécessaires doivent se trouver dans le répertoire "@execdir@" du serveur.',
	'message_texte_binaires_manquant' => 'Plusieurs logiciels nécessaires ne sont pas disponibles sur votre serveur :',
	'message_titre_binaire_manquant' => 'Un logiciel manquant',
	'message_titre_binaires_manquant' => 'Plusieurs logiciels manquant',

	// O
	'options_config' => 'Configuration de l\'encodage',

	// P
	'profil_encodage' => 'Profil d\'encodage pour le format : @format@',
	'profils_encodage_son' => 'Profil(s) d\'encodage (Son)',
	'profils_encodage_video' => 'Profil(s) d\'encodage (Vidéo)',

	// R
	'recuperer_infos' => 'Récupérer les données techniques',
	'recuperer_logo' => 'Récupérer une vignette',

	// S
	'select_all' => 'Tout sélectionner',
	'spipmotion_boite' => 'Configuration de SPIPmotion',
	'spipmotion_descriptif' => 'Ce plugin permet d"encoder à la volée des documents audios et vidéos.',
	'spipmotion_sh_install' => 'Script d\'encodage de SPIPmotion :',
	'spipmotion_sh_vignettes_install' => 'Script de création de vignettes :',

	// T
	'th_avfilter_description' => 'Description',
	'th_avfilter_nom' => 'Nom',
	'th_format_decode' => 'Décode',
	'th_format_encode' => 'Encode',
	'th_format_nom_complet' => 'Nom complet',
	'th_format_nom_court' => 'Nom court',
	'th_format_type' => 'Type',
	'thead_date' => 'Date',
	'thead_document' => 'Document',
	'thead_duree' => 'Durée',
	'thead_duree_encodage' => 'Durée d\'encodage',
	'thead_format' => 'Format',
	'thead_id_auteur' => 'Utilisateur',
	'thead_id_file' => 'Id dans la file',
	'thead_id_origine_doc' => 'Id du document original',
	'thead_nombre' => 'Nombre',
	'thead_objet' => 'Objet',
	'thead_objet_attache' => 'Attaché à l\'objet',
	'thead_stat_duree' => 'Durée de fichier / min',
	'thead_stat_duree_long' => 'Durée du fichier (en minutes) encodée par minute d\'encodage',
	'thead_stat_octet' => 'Taille / min',
	'thead_stat_octet_long' => 'Taille du fichier original encodée par minute d\'encodage',
	'thead_statut' => 'Statut',
	'thead_template_encodage' => 'Template',
	'thead_template_encodage_utilise' => 'Template d\'encodage utilisé',
	'titre_fichier_log' => 'Contenu du fichier de log de l\'id #@id@ dans la file d\'attente',
	'titre_page_ffmpeg_infos' => 'Informations sur FFMpeg',
	'titre_page_file' => 'File d\'attente d\'encodage',
	'titre_page_file_attente' => 'Documents en attente d\'encodage',
	'titre_page_file_encodes' => 'Liste des encodages terminés',
	'titre_page_file_encodes_jour' => 'Encodages par date',
	'titre_page_file_stats' => 'Statistiques d\'encodage',
	'titre_previsu' => 'Prévisualisation',

	// U
	'unselect_all' => 'Tout désélectionner',

	// V
	'version_encodee_de' => 'Ce document est une version encodée du document @id_orig@',

	// W
	'width_video' => 'Largeur de la sortie en px'
);

?>
