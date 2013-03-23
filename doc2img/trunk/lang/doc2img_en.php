<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/doc2img?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_convertir_serie' => 'Convertir en une série d\'images', # NEW
	'bouton_convertir_vignette' => 'Convert the first page as thumbnail',

	// C
	'cfg_explication_compression' => 'Set the compression quality of the final output files (in percentage)',
	'cfg_explication_format' => 'Use a comma "," to separate formats',
	'cfg_explication_logo_auto' => 'Use the first exported page as the logo of the original document if it does not already have a personalized logo.',
	'cfg_explication_resolution' => 'Sets the resolution used as input before exporting the document. For vectorial type files (svg, pdf ...) it\'s interesting to increase the resolution to improve the final result. But it slowed the generation time and the final files size. A good compromise is 150 or 300 dpi.',
	'cfg_label_agrandissements' => 'Allow expansions',
	'cfg_label_compression' => 'Compression quality',
	'cfg_label_conversion_auto' => 'Automatic conversion when a document is attached',
	'cfg_label_format' => 'File extensions to work with (pdf, tiff...)',
	'cfg_label_format_sortie' => 'The default output format',
	'cfg_label_hauteur' => 'Default height',
	'cfg_label_largeur' => 'Default width',
	'cfg_label_logo_auto' => 'First page as a logo',
	'cfg_label_proportions' => 'Keep the aspect ratio',
	'cfg_label_resolution' => 'Resolution',
	'cfg_legende_formats_entree' => 'Input',
	'cfg_legende_formats_sortie' => 'Outputs',
	'cfg_legende_relation_original' => 'Relations with the original document',

	// D
	'doc2img_reconvertir_doc' => '(Re)convert this document to a serie of images',

	// E
	'erreur_class_imagick' => 'Vous ne disposez pas de la class PHP Imagick. Vous ne pouvez donc pas utiliser ce plugin.', # NEW
	'erreur_format_document' => 'A document format can not be used: @type@',
	'erreur_formats_documents' => 'Several document formats can not be used: @types@',
	'explication_doc2img' => 'This plugin allows you to convert certain types of documents in a single or a serie of images to view them.',

	// I
	'info_alt_image' => '@titre@, page @page@',
	'info_desc_page' => 'This document is composed by one sheet.',
	'info_desc_pages' => 'This document is composed by @nb@ sheets.',
	'info_nb_pages' => 'Number of pages:',
	'info_nb_pages_converties' => '@nb@ pages converties liées', # NEW
	'info_une_page_convertie' => 'One linke converted page'
);

?>
