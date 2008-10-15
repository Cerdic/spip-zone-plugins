<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// CFG
	'configuration' => 'Configuration du plugin',
	'limiter_version' => 'Limiter le nombre de versions possibles à :',
	'vider_version' => 'Intervale entre les vidages des versions interm&eacute;diaires (en nombre de jours, 0 = jamais) :',
	'compression_rendu' => 'Qualit&eacute; de compression du rendu (en %, 85 par d&eacute;faut) :',
	'filtres_a_disposition' => 'Filtres &agrave; disposition',
	'configuration_publique' => 'Configuration de la partie publique',
	'hauteur_previsu' => 'Hauteur maximale de la pr&eacute;visualisation en px (450 par d&eacute;faut) : ',
	'largeur_previsu' => 'Largeur maximale de la pr&eacute;visualisation en px (450 par d&eacute;faut) : ',
		
	// PUBLIC
	
	// Versions
	'versions_precedentes' => 'Pr&eacute;c&eacute;dentes versions',
	'pas_de_versions' => '<p>Il n\'y a pas encore de versions diponibles de cette image.</p>',
	'informations_images' => 'Informations du document',
	'id_document' => 'ID du document dans le site : ',
	'type_original' => 'Type du document : ',
	'date_doc' => 'Date de mise en ligne : ',
	'date_modif_doc' => 'Date de derni&egrave;re modification : ',
	'taille_fichier' => 'Taille du fichier : ',
	'fichier_original' => 'Fichier original : ',
	'donnees_exif' => 'Donn&eacute;es EXIF',
	
	'tester' => 'Tester',
	'valider' => 'Appliquer',
	
	'limite_versions_public' => 'Les versions pr&eacute;c&eacute;dentes sont limit&eacute;es &agrave; @limite@',
	'revenir_version' => 'Revenir &agrave; la version : ',
	'supprimer_version' => 'Supprimer la version : ',
	'nb_versions_depasse' => 'Le nombre de versions diff&eacute;rentes de l\'image a &eacute;t&eacute; d&eacute;pass&eacute;. Vous ne pouvez que tester et non appliquer les modifications.',
	
	// Filtres de formats
	'filtres_format' => 'Modifier le format',
	'tourner' => 'Rotation param&egrave;tr&eacute;e',
	'tourner_text' => '<p>Appliquer une rotation de 90, 180 ou 270 degr&eacute;s &agrave; l\'image</p><p>Ce filtre ne peut &ecirc;tre test&eacute;, il ne peut &ecirc;tre qu\'appliqu&eacute;.</p>',
	'tourner_90' => 'Tourner de 90 degr&eacute;s (dans le sens des aiguilles d\'une montre)',
	'tourner_180' => 'Tourner de 180 degr&eacute;s (dans le sens des aiguilles d\'une montre)',
	'tourner_270' => 'Tourner de 270 degr&eacute;s (dans le sens des aiguilles d\'une montre)',
	'image_flip_vertical' => 'Flip Vertical de l\'image',
	'image_flip_vertical_text' => '<p>Appliquer un effet de « miroir » selon un axe vertical (Aucun r&eacute;glage n&eacute;cessaire)</p>',	
	'image_flip_horizontal' => 'Flip Horizontal de l\'image',
	'image_flip_horizontal_text' => '<p>Appliquer un effet de « miroir » selon un axe horizontal (Aucun r&eacute;glage n&eacute;cessaire)</p>',
	'image_rotation' => 'Rotation manuelle de l\'image',
	'image_rotation_text' => '<p>Fait tourner l’image d’un angle &eacute;gal au param&egrave;tre pass&eacute;. Les valeurs positives sont dans le sens des aiguilles d’une montre et inversement.</p><p>Attention : ce filtre modifie les dimensions de l’image.</p>',
	'angle_rotation' => 'Angle de rotation : ',
	'image_recadre' => 'Recadrer l\'image',
	'image_recadre_text' => '<p>Recadre l\'image en fonction de la s&eacute;lection de l\'utilisateur.</p>',
	'recadre_width' => 'Largeur de la s&eacute;lection (en px) : ',
	'recadre_height' => 'Hauteur de la s&eacute;lection (en px) : ',	
	'recadre_x1_y1' => 'Position (coin sup&eacute;rieur gauche)',	
	'recadre_x2_y2' => 'Position (coin inf&eacute;rieur droit)',
	'image_taille_actuelle' => 'Taille actuelle de l\'image :',
	'ratio' => 'Ratio de la s&eacute;lection :',
	'ratio_libre' => 'Libre : ',
		
	// Filtres de coloration
	'filtres_de_couleur' => 'Filtres de coloration',
	'image_sepia' => 'Filtre Sepia',
	'image_nb' => 'Filtre Noir et Blanc',
	'image_gamma' => 'Filtre Gamma',
	'image_flou' => 'Filtre Flou',

	'image_gamma_text' => '<p>Le filtre Gamma change la luminosit&eacute; d\'une image.</p><p>Il rend une image plus claire ou plus fonc&eacute;e.</p><p>Son param&egrave;tre est compris entre -254 et 254. Les valeurs sup&eacute;rieures &agrave; z&eacute;ro rendent l\'image plus claire (254 rend toute l\'image enti&egrave;rement blanche) ; les valeurs n&eacute;gatives rendent l\'image plus fonc&eacute;e (-254 rend l\'image compl&egrave;tement noire).</p>',
	'niveau_gamma' => 'Niveau Gamma :',
	'image_flou_text' => '<p>Le filtre image_flou rend l\'image... floue. On peut lui passer en param&egrave;tre un nombre compris entre 1 et 11, d&eacute;finissant l’intensit&eacute; du floutage (de 1 pixel de floutage à 11 pixels de floutage).</p>',
	'niveau_flou' => 'Niveau de floutage : ',
	'image_nb_text' => 'Transforme l\'image en noir et blanc',
	
	// Filtres supplementaires
	'filtres_supplementaires' => 'Filtres suppl&eacute;mentaires',
	'image_saturation_desaturation' => 'Filtre [D&eacute;-]saturation',
	'image_saturation_desaturation_text' => '<p>Ce filtre va saturer ou d&eacute;saturer les couleurs d’une image.</p><p>La luminosit&eacute; de l’image et le contraste sont inchang&eacute;s</p><p>Dans le premier cas, la couleur est « affadie » ; l’effet, utilis&eacute; subtilement, donnera des tonalit&eacute;s de photographie vieillie...</p><p>Dans l’autre cas, &agrave; l’inverse, le m&ecirc;me filtre pourra « doper » les couleurs.</p>',
	'niveau_saturation_desaturation' => 'Niveau de saturation : ',
	'image_sincity' => 'Filtre Sin City',
	'image_sincity_text' => '<p>Ce filtre donne un aspect "Sin City" (Ne n&eacute;cessite aucun r&eacute;glage).</p><p>Il ex&eacute;cute une d&eacute;saturation contrast&eacute;e et une accentuation du rouge.</p>',
	'image_niveau_de_gris_auto' => 'Niveaux automatiques',
	'image_niveau_de_gris_auto_text' => '<p>Correction automatique des niveaux de l\'image.</p><p>(Ne n&eacute;cessite pas de param&egrave;tres)</p>',
	
	//aide
	'texte_en_cours' => '<div class="waiting"><h2>Modification de votre image...</h2><p>Veuillez patienter que l\'op&eacute;ration soit termin&eacute;e</p></div>',
	'titre_aide' => 'Aide',
	'texte_aide' =>'<p>Pour appliquer une modification &agrave; votre image, il vous suffit de choisir le type en le cochant &agrave; cot&eacute; de son titre et de proc&eacute;der &agrave; son r&eacute;glage.</p>
	<p>Ensuite choisissez entre "tester" ou "appliquer", puis validez.</p>
	<p><strong>NB :</strong> Ces traitements d\'images sont lourds et peuvent prendre quelque temps avant de s\'effectuer. C\'est pourquoi nous vous recommandons de les "tester" avant (cela cr&eacute;era une pr&eacute;visualisation). Chaque test repartira de la derni&egrave;re version. Appliquer cr&eacute;era une nouvelle version disponible.</p>',
	'modification_pas_autorisee' => 'Vous ne disposez pas des droits n&eacute;cessaires pour pouvoir modifier ce document',
	// Messages
	'previsu' => 'Si le r&eacute;sultat vous satisfait vous pouvez le valider en bas du formulaire, sinon vous pouvez tester d\'autres filtres',
	'sanstest' => 'Le filtre que vous avez essay&eacute; ne permet pas d\'&ecirc;tre test&eacute;. Vous ne pouvez que l\'appliquer.',
	'sansfiltre' => 'Vous n\'avez pas s&eacute;lectionn&eacute; de filtre.',
	'sansconf' => 'Vous n\'avez pas configur&eacute; votre filtre.',
);
