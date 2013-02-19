<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/doc2img/lang
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_convertir_serie' => 'Convertir en une série d\'images',
	'bouton_convertir_vignette' => 'Convertir la première page en vignette',

	// C
	'cfg_explication_compression' => 'Défini la qualité de compression finale des fichiers de sortie (en poucentage)',
	'cfg_explication_format' => 'Utilisez une virgule "," pour séparer les formats',
	'cfg_explication_logo_auto' => 'Utiliser la première page exportée comme logo du document original s\'il n\'a pas déjà de vignette personnalisée.',
	'cfg_explication_resolution' => 'Défini la résolution utilisée en entrée avant de réaliser les exports. Pour les fichiers de type vectoriels (svg, pdf...) il est intéressant d\'augmenter cette résolution pour améliorer le résultat final. Cependant cela ralenti fortement le temps de génération et la taille des fichiers finaux. Un bon compromis est 150 ou 300 dpi.',
	'cfg_label_agrandissements' => 'Autoriser les agrandissements',
	'cfg_label_compression' => 'Qualité de compression',
	'cfg_label_conversion_auto' => 'Conversion automatique lorsque qu\'un document est joint',
	'cfg_label_format' => 'Extensions de fichiers à traiter (pdf, tiff, ...)',
	'cfg_label_format_sortie' => 'Le format de sortie par défaut',
	'cfg_label_hauteur' => 'Hauteur par défaut',
	'cfg_label_largeur' => 'Largeur par défaut',
	'cfg_label_logo_auto' => 'Première page comme logo',
	'cfg_label_proportions' => 'Conserver les proportions',
	'cfg_label_resolution' => 'Résolution',
	'cfg_legende_formats_entree' => 'Format d\'entrée',
	'cfg_legende_formats_sortie' => 'Les sorties',
	'cfg_legende_relation_original' => 'Relations avec le document original',

	// D
	'doc2img_reconvertir_doc' => '(Re)convertir ce document en une série d\'images',

	// E
	'erreur_format_document' => 'Un format de document ne peut être pris en compte : @type@',
	'erreur_formats_documents' => 'Plusieurs formats de document ne peuvent être pris en compte : @types@',
	'erreur_class_imagick' => 'Vous ne disposez pas de la class PHP Imagick. Vous ne pouvez donc pas utiliser ce plugin.',
	'explication_doc2img' => 'Ce plugin permet de transformer certains types de documents en une seule ou une série d\'images afin de pouvoir les visualiser.',

	// I
	'info_desc_page' => 'Ce document est composé d\'une pages.',
	'info_desc_pages' => 'Ce document est composé de @nb@ pages.',
	'info_nb_pages' => 'Nombre de pages :',
	'info_nb_pages_converties' => '@nb@ pages converties liées',
	'info_alt_image' => '@titre@, page @page@',
	'info_une_page_convertie' => 'Une page convertie liée'
);

?>
