<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/tabcsv/trunk/lang/

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'tabcsv_description' => 'tabcsv is a model for Spip that converts content or files in Comma-separated values (CSV) format into a Spip array.

- * By default, there is no text delimiter and the field separator is the; (semicolon).
- * Via the parameters (see documentation), you can redefine the text delimiter to "(double quotation mark) and the field separator to what you want.
- * The text delimiter should be set to "(double quotation mark):
- ** If the CSV content data contains the character of the field separator
- ** If ({{Attention, model feature!}}) The CSV content data contains one or more \'(single quotation marks) AND the field separator is the; (semicolon)

The model has been tested under Spip 3.1, but there is not particular reason that it does not work also under Spip 3.0, or 2.1 and 2.0. In other words, you have to test. Feedback from users is welcome.',
	'tabcsv_nom' => 'CSV import',
	'tabcsv_slogan' => 'Importing CSV arrays into Spip arrays.'

);
?>