<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * Stéphanie De Nadaï
 * webdesigneuse.net
 * © 2008 - Distribué sous licence GNU/GPL
 *
##############################################################*/

$GLOBALS[$GLOBALS['idx_lang']] = array(
// A
'apercu_data' => 'Preview of the extracted articles',
'aux_rub' => 'sections',
'aux_art' => 'articles',
'aide_ligne' => 'Online help',
'aide' => 'Help',
'auteurs' => 'Authors',

// B
// C
'config' => 'Configuration',
'config_extract' => 'Configuration of extraction of articles',
'config_extraction' => 'Configuration  of extraction',

// D
'description_cfg' => 'Configure here Export C.S.V. plugin',
'del_cfg' => 'Erase all',
'descriptif' => 'Description',
'date' => 'Date',


// E
'extract_data' => 'Extract data',
'extraction_data' => 'Extraction of data',
'extraction_data_back' => 'Go to extraction of data',
'explications' => '<p><strong>Only published articles and confirmed signatures are exported</strong></p>
<ol class="exportcsv_ol"><li>To download the C.S.V file :
		<ul><li>For articles : click "Download articles" above</li>
			<li>For petitions : click the title of the article </li>
		</ul>
		</li>
		<li>Save the file on your computer</li>
		<li>Launch your spreadsheet software (OpenOffice or Excel<sup>&reg;</sup>)</li>
		<li>Open the downloaded file 
		<ul>
			<li>With OpenOffice, choose <strong>Unicode (UTF-8)</strong> as character set ; <strong>semicolon</strong> as separator and <strong>text by quotation marks</strong></li>
			<li>With Excel, choose "All files" in <em>Type of file</em></li>
		</ul></li>
		</ol>',
'elements_a_extract' => 'elements to be extracted',
'erreur_lien_config' => '<p><a href="?exec=cfg&amp;cfg=exportcsv">Go to configuration</a></p>',
'erreur_admin_config' => '<p>Contact an administrator for configuration</p>',
'erreur_pas_de_config' => '<p>Configuration is not made </p>',
'erreur_pas_de_rub' => '<p>No section was selected</p>',
'erreur_pas_de_champ' => '<p>No element to be displayed was selected</p>',
'erreur_pet_id_article' => '<p>No petition found. <br />Did you select an article ?',

// F
// G
// H
// I
'info_config_rub' => 'Select groups of keywords you want to assign to the sections.<br /> ',
'info_config_art' => 'Select groups of keywords you want to assign to the articles.<br /> ',
'info_nb_lignes' => 'Lines 0 to 100 out of ',
'info_nb_lignes_a' => 'Lines 0 to ',
'sur_total' => ' out of ',

// J K
// L
'lien_url' => 'Hyperlink : URL',
'lien_nom' => 'Hyperlink : Title',

// M
'gmc_associes' => 'Groups of associated keywords',
'mc_associes' => 'Associated keywords',
'mot_clef' => 'Keywords',

// N 
// O 
'ok_cfg' => 'Ok',

// P 
'pet_titre' => 'Petitions',
'pet_lien_extract' => 'Download signatures',
// Q 

// R
'rub_a_extraire' => 'Articles to be extracted from sections',
'reset_cfg' => 'Reset',

// S
'publie' => 'Published online',
'prive' => 'Not published',
'signature' => '<p><strong>Export C.S.V</strong></p> 
<p><small>Extraction of SPIP data for spreadsheet in C.S.V type file</small>.</p>
<p>Plugin created by <a href="http://www.webdesigneuse.net/">St&eacute;phanie De&nbsp;Nada&iuml;</a>.</p>',

// T
'telecharger_data' => 'Download articles',
'titre' => 'Title',
'titre_page' => 'Export CSV',
'titre_gros_page' => 'Export CSV',
'toutes_selectionnees' => 'All sub-sections will be selected',
'rien' => 'Nothing',


// U
// V
// W X Y Z
'z' => 'z'
);

?>
