<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.mediaspip.net/spip.php?page=tradlang
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'analyze_document' => 'Analyzing your document',
	'ancre_formulaire_upload' => 'Back to the upload form',
	'ancre_formulaire_validation' => 'Go to the validation form',
	'ancre_haut_page' => 'Back to the top of the page',
	'aucun_document_type' => 'No document of the necessary type has been uploaded.',

	// B
	'bouton_delier_document' => 'Unlink this document from this article',
	'bouton_forcer' => 'Force this document',
	'bouton_parcourir' => 'Browse',
	'bouton_recuperer_document' => 'Retrieve the document',
	'bouton_supprimer' => 'Remove',

	// C
	'cancel_upload' => 'Cancel upload ?',
	'cancelled' => 'Cancelled',
	'changer_type_article' => 'Changer le type de l\'article',
	'complete' => 'Complete.',
	'configurer_les_extensions' => 'You have to configure the allowed extensions.',
	'connection_obligatoire' => 'You should be identified to the website.',

	// D
	'document_appareil' => 'Camera:',
	'document_credits' => 'Credits:',
	'document_date' => 'Date:',
	'document_description' => 'Document description:',
	'document_description_no_crayons' => 'No description is available, you can add one by double clicking on this text.',
	'document_dimensions' => 'Dimensions :',
	'document_extension' => 'Extension :',
	'document_id' => 'Document ID:',
	'document_infos_complementaires' => 'Additional informations',
	'document_licence' => 'License:',
	'document_logo' => 'Document logo:',
	'document_nom_fichier' => 'Filename:',
	'document_poid_fichier' => 'File size:',
	'document_titre' => 'Document title:',
	'document_type' => 'Document\'s type:',

	// E
	'em_next' => 'Next document',
	'em_prev' => 'Previous document',
	'emballe_medias' => 'Wrap medias',
	'emballe_medias_fichiers' => 'Wrap medias (Files)',
	'emballe_medias_styles' => 'Wrap medias (Styles)',
	'emballe_medias_types' => 'Wrap medias (Types)',
	'erreur_article_inexistant' => 'The media you want to edit doesn\'t exist.',
	'erreur_aucun_fichier' => 'Please choose a file',
	'erreur_aucun_media_correspondant' => 'No media match your criteria.',
	'erreur_autorisation_article' => 'You don\'t have the necessary rights to edit this article.',
	'erreur_beforeunload' => 'You are uploading a document',
	'erreur_conflit_secteur' => 'You can\'t create a template for the articles and the medias for the same main section',
	'erreur_diogene_multiple' => 'You can only have on template "wrap media" on this website',
	'erreur_document_disparu' => 'The original document is no longer available. Below you can bring it back online, the original file was: @fichier@',
	'erreur_document_existant' => 'A similar document is already present: @nom@.',
	'erreur_document_insere' => 'This document is inserted inside the content of the article. It can\'t be deleted.',
	'erreur_fichier_trop_gros' => 'The file is too big.',
	'erreur_filesize_limit' => '@taille_max@ is the maximal accepted by your PHP configuration.',
	'erreur_invalid_file_type' => 'Invalid file type.',
	'erreur_lot_selection_medias' => 'Select at least one media to edit',
	'erreur_media_sans_document' => 'No document is attached to your media. You can\'t publish it until a document is associated to it.',
	'erreur_publier_categorie_avant' => 'You should at least create <a href="@url@" class="spip_in">a sub category</a> before publishing a media.',
	'erreur_publier_categorie_avant_demander_admin' => 'No category exists. Please contact an administrator so that he creates at least one.',
	'erreur_secteur_inexistant' => 'The sector linked to that template doesn\'t exist. Please contact an administrator.',
	'erreur_upload_fournir_objet' => 'You have to provide an object type.',
	'erreur_upload_session' => 'No visitor session.',
	'erreur_zero_byte_files' => 'It is impossible to upload 0 byte files.',
	'explication_chercher_article' => 'Upon submission of a new article, if the ID of the article is not filled in as a parameter of the form, search for the existence of an article being written by the same author and insert the document into it (otherwise we create systematically a new article)',
	'explication_config_readonly' => 'This option is disabled. It must be overridden by the theme you are using.',
	'explication_file_size_limit' => 'Limite de taille pour un fichier (MB). @taille_max@ est le maximum accepté par votre configuration PHP.',
	'explication_gerer_modifs_types' => 'Displays a form in the left column of the edit page of the articles, allowing authors to choose their own type.',
	'explication_gerer_types' => 'Give a type to the articles (fill in the field "em_type" in the article database table) depending on the type of document available online. If this option is enabled, it will be possible to define several different forms depending on the type of files to upload.',
	'explication_infos_documents' => 'These informations are directly extracted from the image metadatas.',
	'explication_medias_prepas' => 'Media listed below are being prepared, you must change their status to "proposed for publication" for an administrator publishes them permanently.',
	'explication_medias_prepas_auth_publier' => 'Media listed below are being prepared, you must change their status for them to be published online.',
	'explication_medias_props' => 'Media listed below are proposed to publication, you must wait for an administrator changes their status for them to be visible on the site.',
	'explication_medias_props_auth_publier' => 'Media listed below are proposed to publication, you must change their status for them to be published online or wait for an administrator does it.',
	'explication_traitement_lot_intro' => 'To handle your media by batch, first select the media to change, then fill in the form fields to be applied.',
	'extensions_audio' => 'Audio extensions:',
	'extensions_autorisees' => 'File extensions allowed: ',
	'extensions_images' => 'Picture extensions:',
	'extensions_texte' => 'Textual extensions:',
	'extensions_video' => 'Video extensions:',

	// F
	'failed_validation' => 'Failed Validation.  Upload skipped.',
	'file_queue_limit' => 'Queued file limit number: ',
	'file_size_limit' => 'The maximal file size is @taille@ MB.',
	'file_upload_limit' => 'Upload file limit number: ',
	'file_upload_limit_public' => 'The maximum number of files to upload is',

	// H
	'hauteur_img_previsu' => 'Maximal height (in px) of the pictures preview',

	// I
	'info_statut_prepa' => 'Being prepared',
	'info_statut_prop' => 'Proposed to publication',
	'info_statut_publies' => 'Published',
	
	// L
	'label_case_gerer_modifs_types' => 'Show the type change form',
	'label_case_gerer_types' => 'Enable types management',
	'label_case_publier_dans_secteur' => 'Allow to publish articles without category (at the root of the media sector).',
	'label_case_types_autoriser_normal' => 'If no type is selected, we allow the publication of "normal" type',
	'label_cfg_file_size_limit' => 'File size limit in MB',
	'label_change_statut_em_normal' => 'Edit the statuts of your media',
	'label_changer_type' => 'Modify the document(s) type to upload: ',
	'label_chercher_article' => 'Search for an article ?',
	'label_choisir_medias_lot' => 'Choose the media to handle',
	'label_choisir_type' => 'Choose the document(s) type to upload: ',
	'label_couleur_claire' => 'Clear color',
	'label_couleur_foncee' => 'Dark color',
	'label_couleur_texte_bouton' => 'Text color of the upload button',
	'label_em_charger_supprimer' => 'Delete the file from the FTP directory after import',
	'label_flash_bouton_styles' => 'Styles of the upload button',
	'label_forcer_validation_titre' => 'Forcing taking into account the title',
	'label_gerer_modifs_types' => 'Allow to post change the type',
	'label_gerer_types' => 'Manage article\'s types',
	'label_publier_dans_secteur' => 'Publish at the root',
	'label_selectionnez_types_medias' => 'Choose the status of the media to select:',
	'label_texte_upload' => 'Explanations for the upload',
	'label_types_autoriser_normal' => 'Allow to publish without defined type',
	'label_types_disponibles' => 'Available types',
	'label_upload_debug' => 'Show the debug of the uploader form',
	'largeur_img_previsu' => 'Maximal width (in px) of the pictures preview',
	'legend_gerer_styles' => 'Styles management',
	'legend_gerer_types' => 'Management of the article\'s types',
	'legend_mise_en_ligne_multiple' => 'Upload files',
	'legend_mise_en_ligne_unique' => 'Upload a file',
	'lien_charger_doc_trad' => 'From the original article',
	'lien_charger_ftp' => 'From the FTP',
	'lien_charger_local' => 'From your computer',
	'lien_creer_nouveau_media' => 'Create a new media',
	'lien_edition_lot' => 'Batch editing',
	'lien_edition_un' => 'Editing one by one',
	'lien_voir_origine' => 'Show original',
	'lien_zoom_image' => 'Zoom',

	// M
	'maj_plugin' => 'Update of the "Wrap Medias" plugins at the version @version@.',
	'max_file_size' => 'The maximal file size is: ',
	'message_delier_document' => 'This document is allready linked to an other object. So you can\'t delete it definitively. You only can unlink it from the current article.',
	'message_doc_trad_indisponible' => 'No document is available in the original article.',
	'message_document_original' => 'This article is the original version of:',
	'message_drag_file' => 'Drop the file here.',
	'message_drag_files' => 'Drop files here.',
	'message_info_media_proposer' => 'Change its status in "@statut@" to allow the administrators to validate it.',
	'message_info_media_publier' => 'Change its status in "@statut@" for it to be visible on the website.',
	'message_info_media_statut' => 'This media is currently "@statut@".',
	'message_medias_maj_nb' => '@nb@ media have been updated.',
	'message_medias_maj_un' => '@nb@ media has been updated.',
	'message_medias_maj_statut_nb' => 'The status of the selected media has been updated in "@statut@"',
	'message_medias_maj_statut_un' => 'Its status has been updated in "@statut@"',
	'message_navigateur_redirection' => 'Your browser will be redirected',
	'message_notice_articles_prepa_nb' => 'You have @nb@ media being prepared.',
	'message_notice_articles_prepa_un' => 'You have one media being prepared.',
	'message_notice_nb_articles_prepa_autres' => 'You have @nb@ other media being prepared.',
	'message_notice_voir_articles_prepa' => 'Show <a href="@url@" class="@class_lien@">these medias</a>.',
	'message_selectionner_media_editer' => 'Select a media in the list to edit it.',
	'message_type_mis_a_jour' => 'The article\'s type has been updated',
	'message_type_pas_mis_a_jour' => 'The article\'s type has not been modified',

	// N
	'nb_doc_uploaded' => '@nb@ documents uploaded',
	'no_credits_crayons' => 'No specified credits',

	// P
	'pending' => 'Pending...',
	'previsu_document' => 'Preview of the document',
	'previsu_document_nb' => 'Preview of the document number @nb@',

	// Q
	'queue_limit_exceeded' => 'You have attempted to queue too many files',
	'queue_limit_max' => 'The max number of files is',
	'queue_limit_reached' => 'You have reached the upload limit.',
	'queue_limit_un' => 'You may select only one file.',

	// S
	'security_error' => 'Security Error',
	'select_all' => 'Select all',
	'server_io_error' => 'Server (IO) Error',
	'stopped' => 'Stopped',
	'supprimer_document' => 'Delete the document',
	'swfupload_alternative_js' => 'You need to activate javascript on your browser to upload documents',

	// T
	'temps_passe' => 'elapsed',
	'temps_restant' => 'remaining',
	'titre_lien_publier' => 'Publish',
	'titre_medias_preparation' => 'Your media being prepared',
	'titre_modification_media' => 'Edition of the media: @titre@',
	'titre_nouveau_document' => 'New document',
	'titre_nouveau_document_audio' => 'New audio document',
	'titre_nouveau_document_image' => 'New picture',
	'titre_nouveau_document_texte' => 'New text document',
	'titre_nouveau_document_video' => 'New video document',
	'type_aucun' => 'No specific type',
	'type_audio' => 'Audio',
	'type_image' => 'Picture',
	'type_invalide' => 'The type of document chosen is invalid, change your choice.',
	'type_media' => 'Media type: ',
	'type_normal' => 'No specific type',
	'type_obligatoire' => 'The configuration of the site requires you to choose a type for this document. Select the one you want from the list below.',
	'type_texte' => 'Text',
	'type_video' => 'Video',
	'types_fichiers_autorises' => 'All file extensions allowed are: @types@',

	// U
	'unhandled_error' => 'Unhandled Error',
	'unselect_all' => 'Deselect All',
	'upload_error' => 'Upload Error:',
	'upload_failed' => 'Upload Failed.',
	'upload_fichiers' => 'Uploading files',
	'upload_limit_exceeded' => 'Upload limit exceeded.',
	'uploading' => 'Uploading...',

	// V
	'verification_fichier' => 'Inpection of the file...',
	'verifier_formulaire' => 'There are errors.<br />Check the contents of the form.'
);

?>
