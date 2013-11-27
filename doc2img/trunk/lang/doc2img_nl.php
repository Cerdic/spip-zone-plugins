<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/doc2img?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_convertir_serie' => 'In een serie afbeeldingen converteren',
	'bouton_convertir_vignette' => 'Converteer de eerste bladzijde in een miniatuur',

	// C
	'cfg_explication_compression' => 'Bepaal de kwaliteit van de uiteindelijke compressie van de bestanden (als percentage)',
	'cfg_explication_format' => 'Gebruik een komma "," om de formaten te scheiden',
	'cfg_explication_logo_auto' => 'Gebruik de eerst geëxporteerde bladzijde als logo voor het oorspronkelijke document wanneer er geen aangepaste miniatuur is.',
	'cfg_explication_resolution' => 'Definieer de toe te passen resolutie alvorens te exporteren. Voor vectorbestanden (svg, pdf, ...) is het interessant deze resolutie te verhogen om het eindresultaat te verbeteren. Het zal wel een negatief effect hebben op de verwerkingstijd. Een goed compromis is 150 of 300 dpi.',
	'cfg_label_agrandissements' => 'Sta het vergroten toe',
	'cfg_label_compression' => 'Compressie-kwaliteit',
	'cfg_label_conversion_auto' => 'Automatische conversie wanneer een document wordt toegevoegd',
	'cfg_label_format' => 'Extensies van te verwerken bestanden (pdf, tiff, ..)',
	'cfg_label_format_sortie' => 'Het standaardformaat van het resultaat',
	'cfg_label_hauteur' => 'Standaardhoogte',
	'cfg_label_largeur' => 'Standaardbreedte',
	'cfg_label_logo_auto' => 'Eerste bladzijde als logo',
	'cfg_label_proportions' => 'Hodut rekening met de proporties',
	'cfg_label_resolution' => 'Resolutie',
	'cfg_legende_formats_entree' => 'Inkomend formaat',
	'cfg_legende_formats_sortie' => 'De resultaten',
	'cfg_legende_relation_original' => 'Relaties met het originele document',

	// D
	'doc2img_reconvertir_doc' => 'Zet dit document (opnieuw) om in een serie afbeeldingen',

	// E
	'erreur_class_imagick' => 'De PHP PHP Imagick is niet beschikbaar. Hierdoor kun je deze plugin niet gebruiken.',
	'erreur_format_document' => 'Het documentformaat kan niet worden verwerkt: @type@',
	'erreur_formats_documents' => 'Meerdere documentformaten kunnen niet worden verwerkt: @types@',
	'explication_doc2img' => 'Met deze plugin kunnen bepaalde types documenten worden omgezet in één of meer te tonen afbeeldingen.',

	// I
	'info_alt_image' => '@titre@, bladzijde @page@',
	'info_desc_page' => 'Dit document bestaat uit één bladzijde.',
	'info_desc_pages' => 'Dit document bestaat uit @nb@ bladzijdes.',
	'info_nb_pages' => 'Aantal bladzijdes:',
	'info_nb_pages_converties' => '@nb@ geconverteerde bladzijdes gekoppeld',
	'info_une_page_convertie' => 'Eén geconverteerde bladzijde gekoppeld'
);

?>
