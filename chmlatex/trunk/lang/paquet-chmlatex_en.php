<?php
    if (!defined('_ECRIRE_INC_VERSION')) return;

    $GLOBALS[$GLOBALS['idx_lang']] = array(
    'chmlatex_nom' => "CHM & LaTeX",

    'chmlatex_slogan' => "Export a sector in CHM or LaTeX format",

    'chmlatex_description' => "The CHM & LaTeX plugin is designed to export the contents of a sector of a SPIP site in the formats:

-* Static HTML format for compiling in [?CHM] format using [HTML Help Workshop-> https://www.microsoft.com/en-us/download/details.aspx?id=21138]
-* Latex format for compiling in PDF format with [XeLatex-> http://www.xelatex.org]

     Multilingualism is supported as follows:

- * Using a unique structure of sections with titles and texts of the sections translated using tags [& lt; multi & gt; -> http://programmer.spip.net/Les-Polyglottes-multi];
- * Use of translation mechanisms SPIP articles. Untranslated items are replaced by the reference article translations.

The export form can be found in Publication menu of private area.",
    );
?>
