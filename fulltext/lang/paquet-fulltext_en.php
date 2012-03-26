<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/paquet-fulltext?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'fulltext_description' => 'This plugin allows on one hand to use the FULLTEXT search mode  of MySQL and thereby improve greatly the search engine compared to the native search engine of SPIP, and on the other hand to index the contents of some documents.
-* exploit the FULLTEXT SEARCH IN BOOLEAN MODE of MySQL, cf. [->http://dev.mysql.com/doc/refman/5.0/en/fulltext-boolean.html]

For an optimum use, you need to install additional programs and setting up  their use in <code> mes_options.php</code> or via the plugin control panel:
-** All types of documents must have a defined constant as <code>_FULLTEXT_EXT_EXE</code> (where EXT is the document extension) or equivalent in the plugin control panel.
-** Document indexing is disabled by default (no constant defined).
-* For {{PDF}} documents:
-** Install [Xpdf->http://www.foolabs.com/xpdf/]
-*** On [Ubuntu->http://packages.ubuntu.com/fr/hardy/xpdf-utils],
-*** On Mac OS X via [MacPorts->http://xpdf.darwinports.com/] or with this [compiled version->http://users.phg-online.de/tk/MOSXS/xpdf-tools-3.dmg],
-*** On [other OS->http://www.foolabs.com/xpdf/download.html]
-** Define these constants (or use the control panel):
-*** <code>_FULLTEXT_PDF_EXE</code> (for example <code>/usr/bin/pdftotext</code>) : path to the binary <code>pdftotext</code> of [Xdpf->http://www.foolabs.com/xpdf/] to transform PDF files in raw text
-*** <code>_FULLTEXT_PDF_CMD_OPTIONS</code> (par exemple <code>-enc UTF-8</code>) : Options d\'appel de l\'exécutable
-*<code>_FULLTEXT_TAILLE</code> : Taille maximum conservée pour la version texte des fichiers (50000 par défaut)
-* For the {{DOC, PPT, XLS}} documents:
-** Install [Catdoc->http://www.wagner.pp.ru/~vitus/software/catdoc/]
-*** on [Ubuntu/Linux->http://www.wagner.pp.ru/~vitus/software/catdoc/],
-*** on [Windows->http://blog.brush.co.nz/2009/09/catdoc-windows/]
-** Define the corresponding constants (or use the control panel) the same way as for the PDF.
-* For {{ODT, DOCX, PPTX, XLSX}} documents:
-** Use PHP functions and classes (requires PHP 5.2 and the -enable-zip option).
  ', # NEW
	'fulltext_slogan' => 'FULLTEXT indexation to speed up searches'
);

?>
