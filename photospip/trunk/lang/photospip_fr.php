<?php

/*
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bouton_tester' => 'Prévisualiser',
	
	// E
	'erreur_auth_modifier' => 'Vous n\'êtes pas autoriser à modifier ce document.',
	'erreur_doc_numero' => 'Vous devez indiquer un identifiant de document existant.',
	'erreur_form_filtre' => 'Veuillez indiquer un filtre à appliquer.',
	'erreur_form_filtre_sstest' => 'Le filtre que vous avez essayé ne permet pas d\'&ecirc;tre testé. Vous ne pouvez que l\'appliquer.',
	'erreur_image_process' => 'Le site n\'utilise pas GD2 pour gérer les images, veuillez l\'utiliser pour leur traitement.',

	// I
	'info_modifier_image' => 'Éditer l\'image',
	
	// L
	'label_choisir_filtres' => 'Choisissez les filtres à activer',
	'label_compression_rendu' => 'Qualité de compression du rendu (en %, 85 par défaut) :',
	'label_hauteur_previsu' => 'Hauteur maximale de la prévisualisation en px (450 par défaut) : ',
	'label_largeur_previsu' => 'Largeur maximale de la prévisualisation en px (450 par défaut) : ',
	'label_limiter_version' => 'Limiter le nombre de versions possibles à :',
	'label_vider_version' => 'Intervale entre les vidages des versions intermédiaires (en nombre de jours, 0 = jamais) :',
	'legend_configuration' => 'Configuration du plugin',
	'legend_configuration_publique' => 'Configuration de la partie publique',
	'legend_filtres_a_disposition' => 'Filtres à disposition',
	'lien_editer_image' => 'Éditer cette image',
	
	// P
	'photospip' => 'PhotoSPIP',
	
	
	// T
	'titre_page_image_edit' => 'Édition de l\'image',
	
	// PUBLIC
	
	// Versions
	'versions_precedentes' => 'Précédentes versions',
	'pas_de_versions' => 'Il n\'y a pas encore de versions diponibles de cette image.',
	'informations_images' => 'Informations du document',
	'id_document' => 'ID du document dans le site : ',
	'type_original' => 'Type du document : ',
	'date_doc' => 'Date de mise en ligne : ',
	'date_modif_doc' => 'Date de dernière modification : ',
	'taille_fichier' => 'Taille du fichier : ',
	'fichier_original' => 'Fichier original : ',
	'donnees_exif' => 'Données EXIF',
	
	'tester' => 'Tester',
	'valider' => 'Appliquer',
	
	'limite_versions_public' => 'Les versions précédentes sont limitées à @limite@',
	'revenir_version' => 'Revenir à la version&nbsp;:',
	'supprimer_version' => 'Supprimer la version&nbsp;:',
	'nb_versions_depasse' => 'Le nombre de versions différentes de l\'image a été dépassé. Vous ne pouvez que tester et non appliquer les modifications.',
	
	
	'image_taille_actuelle' => 'Taille actuelle de l\'image&nbsp;:',
	

	'explication_image_flip_horizontal' => 'Appliquer un effet de « miroir » selon un axe horizontal (Aucun réglage nécessaire).',
	'explication_image_flip_vertical' => 'Appliquer un effet de « miroir » selon un axe vertical (Aucun réglage nécessaire).',	
	'explication_image_flou' => 'Le filtre image_flou rend l\'image... floue. On peut lui passer en paramètre un nombre compris entre 1 et 11, définissant l’intensité du flou (de 1 à 11 pixels de flou).',
	'explication_image_gamma' => 'Le filtre Gamma change la luminosité d\'une image.<br />Il rend une image plus claire ou plus foncée.<br />Son paramètre est compris entre -254 et 254. Les valeurs supérieures à zéro rendent l\'image plus claire (254 rend toute l\'image entièrement blanche); les valeurs négatives rendent l\'image plus foncée (-254 rend l\'image complètement noire).',
	'explication_image_nb' => 'Transforme l\'image en noir et blanc',
	'explication_image_niveau_de_gris_auto' => 'Correction automatique des niveaux de l\'image.<br />(Ne nécessite pas de paramètres).',
	'explication_image_recadre' => 'Recadre l\'image en fonction de la sélection de l\'utilisateur.',
	'explication_image_saturation_desaturation' => 'Ce filtre va saturer ou désaturer les couleurs d’une image.<br />La luminosité de l’image et le contraste sont inchangés<br />Dans le premier cas, la couleur est « affadie » ; l’effet, utilisé subtilement, donnera des tonalités de photographie vieillie...<br />Dans l’autre cas, à l’inverse, le même filtre pourra «doper» les couleurs.',
	'explication_image_sincity' => 'Ce filtre donne un aspect "Sin City" (Ne nécessite aucun réglage).<br />Il exécute une désaturation contrastée et une accentuation du rouge.',
	'explication_image_rotation' => 'Fait tourner l’image d’un angle égal au paramètre passé. Les valeurs positives sont dans le sens des aiguilles d’une montre et inversement.<br />Attention : ce filtre modifie les dimensions de l’image.',
	'explication_tourner' => 'Appliquer une rotation de 90, 180 ou 270 degrés à l\'image<br />Ce filtre ne peut &ecirc;tre testé, il ne peut &ecirc;tre qu\'appliqué.',
	
	'label_angle_rotation' => 'Angle de rotation&nbsp:',
	'label_couleur_sepia' => 'Couleur&nbsp;:',
	'label_image_flip_vertical' => 'Flip Vertical de l\'image',
	'label_image_flip_horizontal' => 'Flip Horizontal de l\'image',
	'label_image_flou' => 'Filtre Flou',
	'label_image_gamma' => 'Filtre Gamma',
	'label_image_nb' => 'Filtre Noir et Blanc',
	'label_image_niveau_de_gris_auto' => 'Niveaux automatiques',
	'label_image_recadre' => 'Recadrer l\'image',
	'label_image_rotation' => 'Rotation manuelle de l\'image',
	'label_image_saturation_desaturation' => 'Filtre [Dé-]saturation',
	'label_image_sepia' => 'Filtre Sepia',
	'label_image_sincity' => 'Filtre Sin City',
	'label_niveau_flou' => 'Niveau de flou&nbsp;:',
	'label_niveau_gamma' => 'Niveau Gamma&nbsp;:',
	'label_niveau_saturation_desaturation' => 'Niveau de saturation&nbsp;:',
	'label_ratio' => 'Ratio de la sélection&nbsp;:',
	'label_ratio_libre' => 'Libre',
	'label_recadre_height' => 'Hauteur de la sélection (en px)&nbsp;:',
	'label_recadre_width' => 'Largeur de la sélection (en px)&nbsp;:',	
	'label_recadre_x1_y1' => 'Position (coin supérieur gauche)',	
	'label_recadre_x2_y2' => 'Position (coin inférieur droit)',
	'label_tourner' => 'Rotation paramètrée',
	'label_tourner_90' => 'Tourner de 90 degrés (dans le sens des aiguilles d\'une montre)',
	'label_tourner_180' => 'Tourner de 180 degrés (dans le sens des aiguilles d\'une montre)',
	'label_tourner_270' => 'Tourner de 270 degrés (dans le sens des aiguilles d\'une montre)',
	'legende_filtres_de_couleur' => 'Filtres de coloration',
	'legende_filtres_format' => 'Modifier le format',
	'legende_filtres_supplementaires' => 'Filtres supplémentaires',
	
	
	//aide
	'texte_en_cours' => '<div class="waiting"><h2>Modification de votre image...</h2><p>Veuillez patienter que l\'opération soit terminée</p></div>',
	'titre_aide' => 'Aide',
	'texte_aide' =>'<p>Pour appliquer une modification à votre image, il vous suffit de choisir le type en le cochant à coté de son titre et de procéder à son réglage.</p>
	<p>Ensuite choisissez entre "tester" ou "appliquer", puis validez.</p>
	<p><strong>NB :</strong> Ces traitements d\'images sont lourds et peuvent prendre quelque temps avant de s\'effectuer. C\'est pourquoi nous vous recommandons de les "tester" avant (cela créera une prévisualisation). Chaque test repartira de la dernière version. Appliquer créera une nouvelle version disponible.</p>',
	'modification_pas_autorisee' => 'Vous ne disposez pas des droits nécessaires pour pouvoir modifier ce document',
	// Messages
	'previsu' => 'Si le résultat vous satisfait vous pouvez le valider en bas du formulaire, sinon vous pouvez tester d\'autres filtres',
	'sanstest' => 'Le filtre que vous avez essayé ne permet pas d\'&ecirc;tre testé. Vous ne pouvez que l\'appliquer.',
	'sansfiltre' => 'Vous n\'avez pas sélectionné de filtre.',
	'sansconf' => 'Vous n\'avez pas configuré votre filtre.',
);
