<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// R
	'adaptive_images_titre' => 'Adaptive Images',
	'variante_mobileview_supprimee' => 'La variante pour mobile a été supprimée',
	'label_fichier_mobileview' => 'Version mobile',
	'aucun_mobileview' => 'Aucune version mobile.<br/><small>Vous pouvez fournir une version recadrée pour l\'affichage sur les petits écrans (<@width@px de large).
L\'image doit faire au moins @width_hr@px de large.</small>',
	'erreur_largeur_mobileview' => 'Cette image est trop petite. La version mobile doit être plus large que @width@px.',
	'warning_ratio_mobileview' => 'Attention, cette variante n\'a pas les mêmes proportions que l\'image originale. Taille attendue : <b>@size1@</b> ou <b>@size2@</b>',
	'bouton_recadrer' => 'Recadrer',

	'titre_configurer' => 'Configurer les Images Adaptatives',
	'explication_configuration_doc' => 'Voir la <a href="https://contrib.spip.net/4458">documentation</a> pour le détail de la configuration.',
	'label_lowsrc_jpg_bg_color' => 'Couleur de fond pour le JPG <i>lowsrc</i>',
	'label_max_width_1x' => 'Largeur max. des images adaptées (px)',
	'label_max_width_mobile_version' => 'Largeur max. de la version mobile (px)',
	'label_min_width_1x' => 'Largeur min. pour adapter (px)',
	'label_min_filesize' => 'Poids min. pour adapter (ko)',
	'label_default_bkpts' => '<i lang="en">Breakpoints</i> pour les variantes d\'image',
	'label_on_demand_production_1' => 'Produire les variantes d\'images uniquement quand elles sont nécessaires',
	'label_lazy_load_1' => 'Ne charger les images adaptatives que lorsqu\'elles sont visibles',
	'explication_on_demand_production' => 'En activant ce réglage, chaque variante d\'une image sera fabriquée la première fois qu\'un visiteur en a besoin.
Evite de générer toutes les images d\'un coup ce qui peut produire des erreurs sur les pages avec beaucoup d\'images.',
	'legend_compression_jpg' => 'Compression JPG',
	'label_10x_jpg_quality' => 'Image 1x',
	'label_15x_jpg_quality' => 'Image 1.5x',
	'label_20x_jpg_quality' => 'Image 2x',


	'legend_miniature_preview' => 'Génération de l\'image d\'aperçu',

	'explication_compression_jpg' => 'Indiquez la qualité des images produites : de 0 (compression maximale, poids minimum) à 100 (pas de compression, poids maximum).',

	'explication_thumbnail_method' => 'L\'image d\'aperçu est envoyée dans le html de la page, pour proposer un aperçu visuel pendant que les vrais images chargent. Elle doit donc être aussi légère que possible',

	'label_thumbnail_method' => 'Méthode de génération de l\'aperçu',
	'label_thumbnail_method_gradients' => 'Fond dégradé horizontal+vertical',

	'label_thumbnail_method_lowsrc' => 'Image basse définition floutée',
	'explication_miniature_basse_def' => 'Un bon compromis de reglage est : Largeur 128px/Qualité 40.',
	'label_lowsrc_width' => 'Largeur de l\'aperçu (px)',
	'label_lowsrc_jpg_quality' => 'Qualité de l\'aperçu',

	'label_thumbnail_method_potrace' => 'Tracé d\'un contour issu de l\'image',
	'label_thumbnail_method_geometrize' => 'Géométrization de l\'image <i>(nécessite de la puissance de calcul)</i>',

	'label_thumbnail_debug_1' => '<i>Activer le debug (les aperçus apparaissent au survol de l\'image)</i>',

	'label_markup_method' => 'Markup généré :',
	'label_markup_method_3layers' => 'Méthode des 3 couches (large support)',
	'label_markup_method_srcset' => '<tt>srcset</tt> + <tt>&lt;source></tt> (HTML5)',

);

?>