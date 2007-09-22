<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Configuration / CFG
'config_migre_descriptif' => 'Website migration config',
'choix_mot_cle_selection' => '<br />(You can select some keywords  with shift or ctrl)<br /><a onclick="$(\'#migre_id_mot\').find(\'option\').attr(\'selected\', false).end().trigger(\'change\');">x</a> unselect all.',
'config_choix_test' => 'Choose your migration type (full or test)',
'config_choix_rubrique' => 'Choose the rubrique',
'choix_rubrique_selection' => 'The imported articles will be moved in this selected rubrique',
'config_cs_decoupe' => 'Splitting article pages with COUTEAU_SUISSE Decoupe',
'sous_choix_cs_decoupe' => 'Checking this box will add the "++++" to split your article pages',

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

'choix_balises_prem' => 'First filter',

'choix_balises_comment' => '&lt;!-- comments --&gt;',
'choix_balises_script' => 'script/style',
'choix_balises_italique' => 'I',
'choix_balises_bold' => 'B',
'choix_balises_h' => 'Hn',
'choix_balises_tr' => 'TR',
'choix_balises_thtd' => 'TH TD',
'choix_balises_br' => 'BR',
'choix_balises_tbody' => 'TBODY',
'choix_balises_table' => 'TABLE',
'choix_balises_font' => 'FONT',
'choix_balises_span' => 'SPAN',
'choix_balises_ulol' => 'UL OL',
'choix_balises_blockquote' => 'BLOCKQUOTE',
'choix_balises_div' => 'DIV',
'choix_balises_hr' => 'HR',
'choix_balises_bull' => '&amp;BULL;',
'choix_balises_li' => 'LI',
'choix_balises_slashli' => '/LI',
'choix_balises_nbsp' => '&amp;NBSP;',
'choix_balises_slashtrtd' => '/T[RHD]',
'choix_balises_p' => 'P',
'choix_balises_dern' => 'Last filter',


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
'article_affiche_par_spip' => 'Article as shown by SPIP',
'article_edite_par_spip' => 'Article edited by SPIP',

// Inutilisee
'err_liste_pages_vide' => '<strong>Error: The page list seems empty.</strong> This plugin download an URL list using the page http://www.example.com/the_page_list.html. A line, blank or tab separated list of URIs to import should be listed in this file.',

'migre_last' => ''

);

?>
