<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Formulaire
'titre_migre_formulaire' => 'Migration of a static website',
'sur_titre_migre_formulaire' => 'Type in infos',
'choix_mot_cle' => 'Choose a keyword',
'sous_choix_mot_cle' => 'The choosen keyword will be set for all imported articles. It\'s not mandatory to select one.',
'choix_url_listepages' => 'Page list URI (required)',
'liste_des_pages' => 'http://www.example.com/the_page_list.html',
'sous_choix_url_listepages' => 'for example http://www.example.com/the_page_list.html',
'choix_balise_centre_debut'=> 'Filter (regular expression) begin block tag',
'sous_choix_balise_centre_debut'=>'For example : &lt;.{3,5}NAME.*index.{3,5}&gt;',
'choix_balise_centre_fin'=> 'End of block tag',
'sous_choix_balise_centre_fin'=> 'For example : &lt;.{3,5}END.*index.*&gt;',
'choix_test'=>'Warning: Run a test only migration',
'sous_choix_test'=>'When the box is checked the retrieved contents will only be prompted',

'choix_balises' => 'HTML tags convertion filters (not required)',
'sous_choix_balises'=> 'Do not modify unless specific needs',

'choix_balises_filtre' => 'Selection filter',
'choix_balises_htos' => 'Converted tag',

'choix_balises_prem' => 'First tag to be filtered',
'sous_choix_balises_prem' => '<br />Leave empty if useless',

'choix_balises_br' => 'BR',
'sous_choix_balises_br' => ' ',
'choix_balises_thtd' => 'TH TD',
'sous_choix_balises_thtd' => ' ',
'choix_balises_tbody' => 'TBODY',
'sous_choix_balises_tbody' => ' ',
'choix_balises_table' => 'TABLE',
'sous_choix_balises_table' => ' ',
'choix_balises_font' => 'FONT',
'sous_choix_balises_font' => ' ',
'choix_balises_span' => 'SPAN',
'sous_choix_balises_span' => ' ',
'choix_balises_ulol' => 'UL OL',
'sous_choix_balises_ulol' => ' ',
'choix_balises_blockquote' => 'BLOCKQUOTE',
'sous_choix_balises_blockquote' => ' ',
'choix_balises_div' => 'DIV',
'sous_choix_balises_div' => ' ',
'choix_balises_hr' => 'HR',
'sous_choix_balises_hr' => ' ',
'choix_balises_bull' => '&amp;BULL;',
'sous_choix_balises_bull' => ' ',
'choix_balises_comment' => '&lt;!-- comments --&gt;',
'sous_choix_balises_comment' => ' ',
'choix_balises_nbsp' => '&amp;NBSP;',
'sous_choix_balises_nbsp' => ' ',
'choix_balises_dern' => 'Least tag to filter',
'sous_choix_balises_dern' => '<br />Leave empty if useless',


// Action

'titre_migre_action' => 'Migration step',
'sur_titre_migre_static' => 'Result of the migration process',
'processing_page' => 'Processing page : ',
'page_title' => 'Title : ',
'err_page_vide' => 'Error : This page is empty',
'err_article_deja_publie' => 'Error : This article is already online with n#: ',
'err_insert_article' => 'Error : impossible database insertion of:',

'insert_article_id' => 'Inserting article n#: ',
'update_article_id' => 'Updating article n#: ',
'insert_article_titre' => ' with this title: ',
'migre_fini' => 'End of all migration process',

// Inutilisee
'err_liste_pages_vide' => '<strong>Error: The page list seems empty.</strong> This plugin download an URL list using the page http://www.example.com/the_page_list.html. A line, blank or tab separated list of URIs to import should be listed in this file.',

'migre_last' => ''

);

?>
