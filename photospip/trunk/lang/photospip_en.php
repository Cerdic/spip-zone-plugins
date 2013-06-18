<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/photospip?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_creer_vignette' => 'Create a thumbnail from this document',
	'bouton_editer_image' => 'Edit the picture',
	'bouton_editer_vignette' => 'Edit the thumbnail',
	'bouton_modifier_document' => 'Edit the informations of the document',
	'bouton_revenir_version' => 'Go back to that version',
	'bouton_supprimer_previsu' => 'Revert to the version without preview',
	'bouton_supprimer_version' => 'Delete this version',
	'bouton_supprimer_vignette' => 'Delete this thumbnail',
	'bouton_supprimer_vignette_document' => 'Delete the thumbnail of this document',
	'bouton_tester' => 'Preview',
	'bouton_valider_continuer' => 'Validate and continue',
	'bouton_valider_fermer' => 'Validate and return to previous page',

	// D
	'date_doc' => 'Publication date:',
	'date_modif_doc' => 'Date of last update:',
	'donnees_exif' => 'EXIF datas',

	// E
	'erreur_auth_modifier' => 'You are not allowed to edit this document.',
	'erreur_choisir_version' => 'Choose a version',
	'erreur_doc_numero' => 'You must indicate an existing document.',
	'erreur_form_filtre' => 'Please specify a filter to apply.',
	'erreur_form_filtre_sstest' => 'The filter you tried can\'t be tested. You can only apply it.',
	'erreur_form_filtre_valeur_obligatoire' => 'You must choose a value.',
	'erreur_form_type_resultat' => 'You must choose the type of result',
	'erreur_image_process' => 'The site does not use GD2 to manage images, please use it to process them.',
	'erreur_nb_versions_atteint' => 'The number of different versions of the image has been reached (@nb@). You can only test it but not apply the changes.',
	'erreur_previsu' => 'If you are satisfied by the result you can validate the form at the bottom, otherwise you can try other filters.',
	'erreur_selectionner_au_moins_une_valeur' => 'You must select at least one value',
	'erreur_valeur_numerique' => 'This filter requires a numeric value as a parameter',
	'erreur_valeurs_numeriques' => 'This filter requires numerical values',
	'explication_image_flip_horizontal' => 'Apply a "mirror" on a horizontal axis (no setting required).',
	'explication_image_flip_vertical' => 'Apply a "mirror" effect on a vertical axis (no setting required).',
	'explication_image_flou' => 'Image_flou filter makes the image ... blurred. You can pass it as a parameter a number between 1 and 11, defining the blur intensity (from 1 to 11 pixels blur).',
	'explication_image_gamma' => 'Gamma filter changes the brightness of an image.<br />It renders an image lighter or darker.<br />Its parameter is between -254 and 254. Values ​​greater than zero makes the picture clearer (254 makes the whole image completely white), the negative values ​​make the image darker (-254 makes the image completely black).',
	'explication_image_nb' => 'Transforms the image into black and white',
	'explication_image_niveau_de_gris_auto' => 'Automatic correction of the levels of the image.<br /> (Does not require parameters).',
	'explication_image_passe_partout' => 'This filter will reduce the size of the image to the minimum making it entering into the width and height provided.',
	'explication_image_recadre' => 'Crops the image according to the user selection.',
	'explication_image_reduire' => 'This filter will reduce the size of the image in proportion depending on the height and width provided.',
	'explication_image_rotation' => 'Rotates the image by an angle equal to the passed parameter. Positive values ​​are in the clockwise direction and vice versa.<br />Warning: This filter changes the dimensions of the image.',
	'explication_image_saturation_desaturation' => 'This filter will saturate or desaturate the colors of an image.<br />Image brightness and contrast remain unchanged.<br />In the first case, the color is "faded", the effect used subtly, will give aged photographic tones...<br />In the other case, on the contrary, the same filter may "boost" colors.',
	'explication_image_sincity' => 'This filter gives a "Sin City" effect (Requires no setting).<br />It executes a contrasted desaturation and a red accentuation.',
	'explication_resultats' => 'During the validation of image editing, three types of results are possible.',
	'explication_resultats_defaut' => 'Default selected value when loading the form.',
	'explication_tourner' => 'Rotate the image by 90, 180 or 270 degrees<br />This filter can not be tested, it can only be applied.',

	// I
	'id_document' => 'ID document in the website:',
	'image_taille_actuelle' => 'Current size of the picture:',
	'info_modifier_image' => 'Edit the picture',
	'info_modifier_vignette' => 'Edit the thumbnail of the document #@id_document@',
	'info_nb_versions' => '@nb@ versions',
	'info_nb_versions_une' => 'One version',

	// L
	'label_angle_rotation' => 'Rotation angle:', # MODIF
	'label_choisir_filtres' => 'Choose filters to enable',
	'label_compression_rendu' => 'Compression quality for rendering (in %, default 85):',
	'label_couleur_sepia' => 'Color :',
	'label_hauteur_previsu' => 'Maximum height of the preview in px (450 by default):',
	'label_image_aplatir' => 'Flatten the image',
	'label_image_flip_horizontal' => 'Horizontal flip of the image',
	'label_image_flip_vertical' => 'Vertical flip of the image',
	'label_image_flou' => 'Blur filter',
	'label_image_gamma' => 'Gamma filter',
	'label_image_nb' => 'Black and white filter',
	'label_image_niveau_de_gris_auto' => 'Automatic levels',
	'label_image_passe_partout' => 'Reduce the image (passe-partout)',
	'label_image_recadre' => 'Crop the picture',
	'label_image_reduire' => 'Reduce the picture',
	'label_image_rotation' => 'Manual rotation of the image',
	'label_image_saturation_desaturation' => '[De]saturation filter',
	'label_image_sepia' => 'Sepia Filter',
	'label_image_sincity' => 'Sin City Filter',
	'label_largeur_previsu' => 'Maximum width of the preview in px (450 by default): ',
	'label_limiter_version' => 'Limit the number of possible versions to:',
	'label_modif_creer_nouvelle_image' => 'A new document will be created from the original image',
	'label_modif_creer_version_image' => 'The original image will be saved as new version of the document that will replace it in the site',
	'label_modif_remplacer_image' => 'The original image will simply be replaced',
	'label_modif_vignette_creer_version_image' => 'The original thumbnail will be saved as version of the new thumbnail which will replace it in the site',
	'label_modif_vignette_remplacer_image' => 'The original thumbnail will simply be replaced',
	'label_niveau_flou' => 'Blur level:',
	'label_niveau_gamma' => 'Gamma level:',
	'label_niveau_saturation_desaturation' => 'Saturation level:',
	'label_ratio' => 'Selection ratio:',
	'label_ratio_libre' => 'Free',
	'label_recadre_height' => 'Selection height (px):',
	'label_recadre_width' => 'Selection width (px):',
	'label_recadre_x1_y1' => 'Position (upper left corner)',
	'label_recadre_x2_y2' => 'Position (lower right corner)',
	'label_reduire_height' => 'Height (in px):',
	'label_reduire_width' => 'Width (in px) :',
	'label_resultats' => 'Choice of possible results by the user',
	'label_resultats_defaut' => 'Default preset',
	'label_tourner' => 'Configured rotation',
	'label_tourner_180' => 'Half a turn',
	'label_tourner_270' => 'A quarter turn to the left',
	'label_tourner_90' => 'A quarter turn to the right',
	'label_type_modification' => 'What will be the result?',
	'label_type_retour' => 'What to do when the filter is applied?',
	'label_type_retour_continuer' => 'Continue to modify the image',
	'label_type_retour_retour' => 'Close modification',
	'legend_configuration' => 'Configuration of the plugin',
	'legend_configuration_publique' => 'Configuration of the public part',
	'legend_configuration_resultats' => 'Configuration of the results',
	'legend_filtres_a_disposition' => 'Filters available',
	'legende_filtres_de_couleur' => 'Color filters',
	'legende_filtres_format' => 'Change the format',
	'lien_editer_image' => 'Édit this picture',
	'lien_editer_vignette' => 'Edit the thumbnail',

	// M
	'message_image_taille_actuelle' => 'Actual size of the picture: @largeur@x@hauteur@px.',
	'message_limite_versions' => 'The number of previous versions are limited to @limite@.',
	'message_nouvelle_image_creee' => 'Your new image has been  created #@id_document@',
	'message_ok_version_retour' => 'You are back to the version #@version@',
	'message_ok_version_supprimee' => 'The version #@version@ has been deleted',
	'message_pas_de_versions' => 'This document is not versioned.',
	'message_vignette_installe_succes' => 'The thumbnail has been successfully uploaded',

	// P
	'photospip' => 'PhotoSPIP',

	// T
	'taille_fichier' => 'Filesize: ',
	'title_version' => 'Version #@version@',
	'titre_informations_images' => 'Document Information',
	'titre_page_image_edit' => 'Image editing',
	'type_original' => 'Type of document: '
);

?>
