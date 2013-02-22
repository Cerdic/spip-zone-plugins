<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/doc2img?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_convertir_serie' => 'Convertir en una serie de imágenes',
	'bouton_convertir_vignette' => 'Convertir la primera página en miniatura',

	// C
	'cfg_explication_compression' => 'Define la calidad de compresión final de los archivos de salida (en porcentaje)',
	'cfg_explication_format' => 'Utilice una coma "," para separar los formatos',
	'cfg_explication_logo_auto' => 'Utilizar la primera página exportada como logo del documento original si no hay una miniatura personalizada. 
',
	'cfg_explication_resolution' => 'Establece la resolución utilizada como entrada antes de realizar las exportaciones. Para los archivos de tipo vectoriales (svg, pdf...) es interesante aumentar esta resolución para mejorar el resultado final. Sin embargo, ello aminora destacadamente el tiempo de generación y el tamaño de los archivos finales. Un buen compromiso es 150 o 300 ppp (puntos por pulgada).',
	'cfg_label_agrandissements' => 'Autorizar ampliaciones',
	'cfg_label_compression' => 'Calidad de compresión',
	'cfg_label_conversion_auto' => 'Conversión automática cuando se adjunta un documento',
	'cfg_label_format' => 'Extensiones de archivos para procesar (pdf, tiff...)', # MODIF
	'cfg_label_format_sortie' => 'Formato de salida por defecto',
	'cfg_label_hauteur' => 'Altura por defecto',
	'cfg_label_largeur' => 'Anchura por defecto',
	'cfg_label_logo_auto' => 'Primera página como logo',
	'cfg_label_proportions' => 'Conservar las proporciones',
	'cfg_label_resolution' => 'Resolución',
	'cfg_legende_formats_entree' => 'Formato de entrada',
	'cfg_legende_formats_sortie' => 'Las salidas',
	'cfg_legende_relation_original' => 'Relaciones con el documento original',

	// D
	'doc2img_reconvertir_doc' => '(Re)convertir este documento en una serie de imágenes',

	// E
	'erreur_class_imagick' => 'No dispone de la clase PHP Imagick. Por ello, no puede utilizar este plugin.',
	'erreur_format_document' => 'Un formato de documento no puede ser tenido en cuenta: @type@',
	'erreur_formats_documents' => 'Varios formatos de documento no pueden ser tenidos en cuenta: @types@',
	'explication_doc2img' => 'Este plugin permite transformar ciertos tipos de documentos en una sola o en una serie de imágines a fin de poder visualizarlas.',

	// I
	'info_alt_image' => '@titre@, página @page@',
	'info_desc_page' => 'Este documento se compone de una página.', # MODIF
	'info_desc_pages' => 'Este documento se compone de @nb@ páginas.',
	'info_nb_pages' => 'Número de páginas:',
	'info_nb_pages_converties' => '@nb@ páginas convertidas relacionadas',
	'info_une_page_convertie' => 'Una página convertida relacionada'
);

?>
