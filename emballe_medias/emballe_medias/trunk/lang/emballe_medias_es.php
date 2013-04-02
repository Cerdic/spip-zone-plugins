<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/emballe_medias?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'analyze_document' => 'Análisis de su documento',
	'ancre_formulaire_upload' => 'Volver al formulario de puesta en línea de documentos',
	'ancre_formulaire_validation' => 'Ir al formulario de validación',
	'ancre_haut_page' => 'Volver arriba',
	'aucun_document_type' => 'No existe ningún documento del tipo necesario',

	// B
	'bouton_delier_document' => 'Desvincular este documento de este artículo',
	'bouton_enlever' => 'Quitar',
	'bouton_parcourir' => 'Examinar',
	'bouton_recuperer_document' => 'Recuperar el (los) documento(s)',
	'bouton_supprimer' => 'Eliminar',

	// C
	'cancel_upload' => 'Anular la puesta en red?',
	'cancelled' => 'Anulación',
	'changer_type_article' => 'Cambiar el tipo de artículo',
	'complete' => 'Este archivo se ha subido. Ahora puede editarlo. ',
	'configurer_les_extensions' => 'Debe configurar las extensiones autorizadas.',
	'connection_obligatoire' => 'Debe estar identificado en el sitio.',

	// D
	'document_appareil' => 'Aparato:',
	'document_credits' => 'Créditos',
	'document_date' => 'Fecha:',
	'document_description' => 'Descripción del documento:',
	'document_description_no_crayons' => 'No hay descripción alguna disponible, puede añadir una haciendo doble click sobre este texto. ',
	'document_dimensions' => 'Dimensiones:',
	'document_extension' => 'Extensión:',
	'document_id' => 'Identificador del documento:',
	'document_infos_complementaires' => 'Información complementaria',
	'document_licence' => 'Licencia:',
	'document_logo' => 'Logotipo del documento:',
	'document_nom_fichier' => 'Nombre del archivo:',
	'document_poid_fichier' => 'Tamaño del archivo:',
	'document_titre' => 'Título del documento:',
	'document_type' => 'Tipo de documento:',

	// E
	'em_next' => 'Documento siguiente',
	'em_prev' => 'Documento anterior',
	'emballe_medias' => 'Emballe médias', # NEW
	'emballe_medias_fichiers' => 'Emballe médias (Fichiers)', # NEW
	'emballe_medias_styles' => 'Emballe médias (Styles)', # NEW
	'emballe_medias_types' => 'Emballe médias (Types)', # NEW
	'erreur_article_inexistant' => 'El media que desea modificar no existe.',
	'erreur_aucun_fichier' => 'Elija por favor un archivo',
	'erreur_aucun_media_correspondant' => 'Ningún media corresponde a los criterios',
	'erreur_autorisation_article' => 'No dispone de los derechos necesarios para modificar el artículo solicitado.',
	'erreur_beforeunload' => 'Está publicando en red un documento',
	'erreur_conflit_secteur' => 'No puede crear una plantilla para los artículos y para los media dentro de la misma sección',
	'erreur_demander_validation_titre' => 'Ha solicitado modificar el título o algunos medias tienen ya un título personalizado. Marque por favor la siguiente casilla para imponer la modificación de los títulos.',
	'erreur_diogene_multiple' => 'Vous ne pouvez avoir qu\'un seul template "emballe média" sur ce site', # NEW
	'erreur_document_disparu' => 'El documento original ya no está disponible. Puede volver a publicarlo a continuación, el archivo original era: @fichier@',
	'erreur_document_existant' => 'Un documento similiar ya existe: @nom@',
	'erreur_document_insere' => 'Este documento está insertado en el contenido del artículo, por lo que no puede eliminarse. ',
	'erreur_fichier_trop_gros' => 'El archivo es demasiado grande.',
	'erreur_filesize_limit' => '@taille_max@ es el máximo aceptado por su configuración PHP.',
	'erreur_invalid_file_type' => 'Tipo de archivo no válido. ',
	'erreur_lot_selection_medias' => 'Seleccione al menos un media para editar',
	'erreur_media_sans_document' => 'Ningún documento está adjunto a su media. No podrá publicarlo en tanto ningún documento esté asociado.',
	'erreur_publier_categorie_avant' => 'Debe crear al menos <a href="@url@" class="spip_in">una sección</a> previamente.',
	'erreur_publier_categorie_avant_demander_admin' => 'No existe ninguna categoría. Contacte por favor con un administrador para que cree al menos una. ',
	'erreur_secteur_inexistant' => 'El sector asociado a esta plantilla no existe. Contacte por favor con un administrador.',
	'erreur_upload_fournir_objet' => 'Debe proporcionar un tipo de objeto.',
	'erreur_upload_session' => 'Ninguna sesión visitante.',
	'erreur_zero_byte_files' => 'Es imposible publicar en red archivos de 0 byte.',
	'explication_chercher_article' => 'Lors de la soumission d\'un nouveau article, si l\'id_article n\'est pas renseigné en tant que paramètre du formulaire, chercher l\'existence d\'un article en cours de rédaction du même auteur et insérer l\'article dedans (sinon on crée systématiquement un nouvel article)', # NEW
	'explication_config_readonly' => 'Esta opción está desactivada. Debe ser impuesta por el tema que utiliza.',
	'explication_file_size_limit' => 'Límite de tamaño para un archivo (MB). @taille_max@ es el máximo aceptado por su configuración PHP.',
	'explication_gerer_modifs_types' => 'Muestra un formulario en la columna izquierda de la página de modificación del artículo, permitiendo a los autores elegir ellos mismos el tipo de artículo. ',
	'explication_gerer_types' => 'Typer les articles (remplir le champs "em_type" de la table article) en fonction du type de document mis en ligne. Si cette option est activée, il sera possible de définir plusieurs formulaire différents en fonction du type de fichier à mettre en ligne.', # NEW
	'explication_infos_documents' => 'Esta información es extraída directamente de metadatos de la imagen.',
	'explication_medias_prepas' => 'Los medias listados a continuación están en curso de preparación, debe cambiar su estatus a "propuesto para publicación" para que un administrador los publique definitivamente. ',
	'explication_medias_prepas_auth_publier' => 'Los medias listados a continuación están en curso de preparación, debe cambiar su estatus para que sean publicados. ',
	'explication_medias_props' => 'Les médias listés ci dessous sont proposés à la publication, vous devez attendre qu\'un administrateur change leur statut pour qu\'ils soient visibles sur le site.', # NEW
	'explication_medias_props_auth_publier' => 'Les médias listés ci dessous sont proposés à la publication, vous devez changer leur statut pour qu\'ils soient publiés en ligne ou attendre qu\'un administrateur ne le fasse.', # NEW
	'explication_traitement_lot_intro' => 'Para procesar sus medias por lote, seleccione previamente los medias a modificar, después complete los campos del formulario que serán aplicados.',
	'extensions_audio' => 'Extensiones Audio:',
	'extensions_autorisees' => 'Extensiones de archivos autorizados',
	'extensions_images' => 'Extensiones Imagen:',
	'extensions_texte' => 'Extensiones Texto:',
	'extensions_video' => 'Extensiones Vídeo:',

	// F
	'failed_validation' => 'La validación del archivo ha fallado. La publicación se ha cancelado.',
	'file_queue_limit' => 'Límite del número de archivos en lista de espera',
	'file_size_limit' => 'El tamaño máximo de un archivo es de @taille@ MB',
	'file_upload_limit' => 'Limitar el número de archivos a publicar en red',
	'file_upload_limit_public' => 'El límete máximo del número de archivos a publicar en red es de',

	// H
	'hauteur_img_previsu' => 'Altura máxima (en px) de la previsualización de las imágenes',

	// I
	'info_statut_prepa' => 'En preparación',
	'info_statut_prop' => 'Propuestos para publicación',
	'info_statut_publies' => 'Publicados',

	// L
	'label_case_gerer_modifs_types' => 'Mostrar el formulario de cambio de tipo',
	'label_case_gerer_types' => 'Activar la gestión de los tipos',
	'label_case_publier_dans_secteur' => 'Permitir publicar artículos sin categoría (en la raíz del sector medias).',
	'label_case_types_autoriser_normal' => 'Dans le cas où aucun type n\'est sélectionné, on autorise la publication de type "normal"', # NEW
	'label_cfg_file_size_limit' => 'Límite del tamaño de los archivos en MB',
	'label_change_statut_em_normal' => 'Modificar el estatus de su media',
	'label_changer_type' => 'Modificar el tipo de documento(s) a publicar: ',
	'label_chercher_article' => '¿Buscar un artículo?',
	'label_choisir_medias_lot' => 'Elija los medias a procesar',
	'label_choisir_type' => 'Elegir el tipo de documento(s) a publicar: ',
	'label_couleur_claire' => 'Color claro',
	'label_couleur_foncee' => 'Color oscuro',
	'label_couleur_texte_bouton' => 'Color del texto del botón de cargar (upload)',
	'label_em_charger_supprimer' => 'Eliminar el archivo del repertorio FTP tras importarlo',
	'label_flash_bouton_styles' => 'Estilos del botón de cargar (upload)',
	'label_forcer_validation_titre' => 'Forzar la inclusión del título',
	'label_gerer_modifs_types' => 'Permitir modificar el tipo a posteriori',
	'label_gerer_types' => 'Administrar los tipos de artículos',
	'label_publier_dans_secteur' => 'Publication hors catégorie', # NEW
	'label_selectionnez_types_medias' => 'Choisissez le statut des médias à sélectionner :', # NEW
	'label_texte_upload' => 'Explicaciones para la subida en línea',
	'label_types_autoriser_normal' => 'Autorizar publicación sin tipo definido',
	'label_types_disponibles' => 'Tipos disponibles',
	'label_upload_debug' => 'Afficher le debug du formulaire d\'upload des documents', # NEW
	'largeur_img_previsu' => 'Anchura máxima (en px) de la previsualización de imágenes',
	'legend_gerer_styles' => 'Gestión de estilos',
	'legend_gerer_types' => 'Gestión de tipos de artículos',
	'legend_mise_en_ligne_multiple' => 'Publicar archivo(s)',
	'legend_mise_en_ligne_unique' => 'Publicar archivo',
	'lien_charger_doc_trad' => 'Desde el artículo de origen',
	'lien_charger_ftp' => 'Desde la FTP',
	'lien_charger_local' => 'Desde su ordenador',
	'lien_creer_nouveau_media' => 'Crear un nuevo media',
	'lien_edition_lot' => 'Edición por lote',
	'lien_edition_un' => 'Edición uno por uno',
	'lien_voir_origine' => 'Ver el original',
	'lien_zoom_image' => 'Zoom',

	// M
	'maj_plugin' => 'Mise à jour du plugin "Emballe Médias" à la version @version@.', # NEW
	'max_file_size' => 'El tamaño máximo del archivo es: ',
	'media_propose_detail' => 'El media "@titre@" está propuesto para publicación
	desde',
	'media_propose_sujet' => '[@nom_site_spip@] Propone: @titre@',
	'media_propose_titre' => 'Media propuesto
	---------------',
	'media_propose_url' => 'Le invitamos a consultarlo. Está disponible en la dirección:',
	'media_publie_detail' => 'El media "@titre@" acaba de ser publicado por @connect_nom@.',
	'media_publie_sujet' => '[@nom_site_spip@] PUBLICADO: @titre@',
	'media_publie_titre' => 'Media publicado
	--------------',
	'media_valide_date' => 'Sujeto a modificación, este media será publicado',
	'media_valide_detail' => 'El media "@titre@" ha sido validado por @connect_nom@.',
	'media_valide_sujet' => '[@nom_site_spip@] VALIDADO: @titre@',
	'media_valide_titre' => 'Media validado
	--------------',
	'media_valide_url' => 'A la espera, está visible desde esta dirección temporal:',
	'message_aucun_media_attente' => 'No tiene ningún media a la espera de publicación.',
	'message_delier_document' => 'Ce document est déjà lié à un autre objet. Vous ne pouvez le supprimer. Vous pouver seulement le délier de l\'objet en cours.', # NEW
	'message_doc_trad_indisponible' => 'Ningún documento está disponible en el artículo de origen.',
	'message_document_original' => 'Este artículo es la versión original de:',
	'message_drag_file' => 'Déposez le fichier ici.', # NEW
	'message_drag_files' => 'Déposez les fichiers ici.', # NEW
	'message_info_media_proposer' => 'Cambie su estatus a "@statut@" para que los administradores puedan validarlo.',
	'message_info_media_publier' => 'Cambie su estatus a "@statut@" para que sea visible en el sitio. ',
	'message_info_media_statut' => 'Este media está actualmente "@statut@".',
	'message_medias_maj_nb' => '@nb@ médias ont été mis à jour.', # NEW
	'message_medias_maj_statut_nb' => 'Le statut des médias sélectionnés a été mis à jour en "@statut@"', # NEW
	'message_medias_maj_statut_un' => 'Son statut a été mis à jour en "@statut@"', # NEW
	'message_medias_maj_un' => '@nb@ ha sido actualizado. ',
	'message_navigateur_redirection' => 'Votre navigateur va être redirigé.', # NEW
	'message_notice_articles_prepa_nb' => 'Vous avez @nb@ médias en préparation.', # NEW
	'message_notice_articles_prepa_un' => 'Vous avez un média en préparation.', # NEW
	'message_notice_nb_articles_prepa_autres' => 'Vous avez @nb@ autres médias en préparation.', # NEW
	'message_notice_voir_articles_prepa' => 'Voir <a href="@url@" class="@class_lien@">ces médias</a>.', # NEW
	'message_selectionner_media_editer' => 'Sélectionnez un média dans la liste afin de l\'éditer.', # NEW
	'message_type_mis_a_jour' => 'El tipo de artículo ha sido actualizado',
	'message_type_pas_mis_a_jour' => 'El tipo de artículo no ha sido modificado. ',

	// N
	'nb_doc_uploaded' => '@nb@ documentos publicados',
	'no_credits_crayons' => 'Ningún crédito especificado',

	// P
	'pending' => 'En lista de espera...',
	'previsu_document' => 'Previsualización del documento',
	'previsu_document_nb' => 'Previsualización del documento número @nb@',

	// Q
	'queue_limit_exceeded' => 'Ha intentado adjuntar demasiados archivos.',
	'queue_limit_max' => 'La limite maximale du nombre de fichier dans la file d\'attente est', # NEW
	'queue_limit_reached' => 'Vous avez atteint la limite.', # NEW
	'queue_limit_un' => 'No puede seleccionar más que un solo archivo',

	// S
	'security_error' => 'Error de seguridad',
	'select_all' => 'Seleccionar todo',
	'server_io_error' => 'Error de servidor (IO)',
	'stopped' => 'Detenido...',
	'supprimer_document' => 'Eliminar el documento',
	'swfupload_alternative_js' => 'Debe activar el javascript para publicar un documento',

	// T
	'temps_passe' => 'passé', # NEW
	'temps_restant' => 'restant', # NEW
	'titre_lien_publier' => 'Publicar',
	'titre_medias_preparation' => 'Sus medias en preparación',
	'titre_modification_media' => 'Modificación del media: @titre@',
	'titre_nouveau_document' => 'Nuevo documento',
	'titre_nouveau_document_audio' => 'Nuevo documento audio',
	'titre_nouveau_document_image' => 'Nueva imagen',
	'titre_nouveau_document_texte' => 'Nuevo documento texto',
	'titre_nouveau_document_video' => 'Nuevo documento vídeo',
	'type_aucun' => 'Ningún tipo específico',
	'type_audio' => 'Audio',
	'type_image' => 'Imagen',
	'type_invalide' => 'Le type de document choisi est invalide, modifiez votre choix.', # NEW
	'type_media' => 'Tipo de media: ',
	'type_normal' => 'Ningún tipo específico',
	'type_obligatoire' => 'La configuration du site vous oblige à choisir un type pour ce document. Sélectionnez celui désiré dans la liste ci-dessous.', # NEW
	'type_texte' => 'Texte', # NEW
	'type_video' => 'Video', # NEW
	'types_fichiers_autorises' => 'L\'ensemble des extensions de fichier autorisées sont : @types@', # NEW

	// U
	'unhandled_error' => 'Erreur inconnue', # NEW
	'unselect_all' => 'Tout déselectionner', # NEW
	'upload_error' => 'Erreur de mise en ligne :', # NEW
	'upload_failed' => 'La mise en ligne a échoué.', # NEW
	'upload_fichiers' => 'Mise en ligne des fichiers', # NEW
	'upload_limit_exceeded' => 'Upload limit exceeded.', # NEW
	'uploading' => 'Mise en ligne...', # NEW

	// V
	'verification_fichier' => 'Vérification du fichier...', # NEW
	'verifier_formulaire' => 'Il y a des erreurs.<br />Vérifiez le contenu du formulaire.' # NEW
);

?>
