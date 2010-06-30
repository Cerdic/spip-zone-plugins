<?php
define('_DIR_PDF',_DIR_IMG.'pdf/');
$GLOBALS['spip2pdf_version'] = "spip2pdf v1.0";

/**
 * Set intertitre size
 */
define('PDF_INTERTITRE_SIZE',15);

/**
 * Define the values of margins
 * default '10 30'
 * right margin and left margin are both 10 user units
 * Margin Top is 30 user units
 */
define('PDF_AUTHOR','Auteur du document');
/**
 * Define the values of margins
 * default '10 30'
 * right margin and left margin are both 10 user units
 * Margin Top is 30 user units
 */
define('PDF_MARGINS','10 30');
/**
 * Define the name of the pdf document
 */
define('PDF_OUTPUTFILE','spip2pdf.pdf');
/**
 * Define the title of the pdf document
 */
define('PDF_TITLE','génération de documents au format pdf');
/**
 * Define the user unit of the document
 */
define("PDF_DOCUMENT_UNIT","mm");
/**
 * Define the subject of the pdf document
 */
define('PDF_SUBJECT','Utilisation du plugin SPIP2PDF - Notice détaillée');
/**
 * Define the keywords of the pdf document
 */
define('PDF_KEYWORDS','PDF SPIP PHP PLUGIN');
/**
 * Set pdf header enable
 */
define('PDF_HEADER_ENABLE','non');
/**
 * Set pdf header label
 */
define('PDF_HEADER_LABEL','PLUGIN SIP2PDF - Génération de documents au format PDF à  partir de SPIP');
/**
 * Set pdf header margin
 */
define('PDF_HEADER_MARGIN',10);
/**
 * Set pdf header font
 */
define('PDF_HEADER_FONT','freemono');
/**
 * Set pdf header font size
 */
define('PDF_HEADER_FONT_SIZE',10);
/**
 * Set pdf header align
 */
define('PDF_HEADER_ALIGN','left');
/**
 * Set pdf header color
 */
define('PDF_HEADER_COLOR','#000000');
/**
 * Set pdf header font style
 */
define('PDF_HEADER_FONT_STYLE','B');
/**
 * Set pdf header text label
 */
define('PDF_HEADER_TXT_Label','copyright @Internews - plugin SPIP2PDF');
/**
 * Set pdf header text font
 */
define('PDF_HEADER_TXT_FONT','tahoma');
/**
 * Set pdf header text font size
 */
define('PDF_HEADER_TXT_FONT_SIZE',10);
/**
 * Set pdf header text font style
 */
define('PDF_HEADER_TXT_FONT_STYLE','U');
/**
 * Set pdf header text align
 */
define('PDF_HEADER_TXT_ALIGN','L');
/**
 * Set pdf header text color
 */
define('PDF_HEADER_TXT_COLOR','#000000');

/**
 * Set pdf footer enable
 */
define('PDF_FOOTER_ENABLE','non');
/**
 * Set pdf footer margin
 */
define('PDF_FOOTER_MARGIN',10);
/**
 * Set pdf footer font
 */
define('PDF_FOOTER_FONT','freemono');
/**
 * Set pdf footer font size
 */
define('PDF_FOOTER_FONT_SIZE',10);
/**
 * Set pdf footer align
 */
define('PDF_FOOTER_ALIGN','left');
/**
 * Set pdf footer color
 */
define('PDF_FOOTER_COLOR','#000000');
/**
 * Set pdf footer font style
 */
define('PDF_FOOTER_FONT_STYLE','B');
/**
 * Set pdf footer text font
 */
define('PDF_FOOTER_TXT_FONT','freemono');
/**
 * Set pdf footer text font size
 */
define('PDF_FOOTER_TXT_FONT_SIZE',10);
/**
 * Set pdf footer text font style
 */
define('PDF_FOOTER_TXT_FONT_STYLE','U');
/**
 * Set pdf footer text align
 */
define('PDF_FOOTER_TXT_ALIGN','left');
/**
 * Set pdf footer text color
 */
define('PDF_FOOTER_TXT_COLOR','#000000');
/**
 * Default number of colmns
 */
define('PDF_MULTICOL_NUMBER',2);
/**
 * Set pdf multicolumns space
 */
define('PDF_MULTICOL_SPACE',5);

?>
