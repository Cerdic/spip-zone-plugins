<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Formulaire
'titre_migre_formulaire' => 'Migration d\'un site statique',
'sur_titre_migre_formulaire' => 'Saisie des informations',
'choix_mot_cle' => 'Choisir un mot-cl&eacute; (facultatif)',
'sous_choix_mot_cle' => 'Le mot-cl&eacute; choisi sera mis sur tous les articles migr&eacute;s. Vous n\'&ecirc;tes pas oblig&eacute;s d\'en choisir un.',
'choix_url_listepages' => 'URL de la liste des pages &agrave; (obligatoire)',
'liste_des_pages' => 'http://www.monsite.com/la_liste_des_pages.html',
'sous_choix_url_listepages' => 'par exemple http://www.monsite.com/la_liste_des_pages.html',
'choix_balise_centre_debut'=> 'Filtre (expresssion r&eacute;guli&egrave;re) de d&eacute;but de bloc (facultatif)',
'sous_choix_balise_centre_debut'=>'Par exemple : &lt;.{3,5}NAME.*index.{3,5}&gt;',
'choix_balise_centre_fin'=> 'Balise de fin de bloc (facultatif)',
'sous_choix_balise_centre_fin'=> 'Par exemple : &lt;.{3,5}END.*index.*&gt;',
'choix_test'=>'Attention : R&eacute;aliser un import &agrave; blanc',
'sous_choix_test'=>'Si la case est coch&eacute;e les contenus seront seulement affich&eacute;s',

'choix_balises' => 'Filtres de conversion des balises HTML (facultatif)',
'sous_choix_balises'=> 'Ne pas les modifier sauf besoins sp&eacute;cifiques',

'choix_balises_filtre' => 'Filtre de s&eacute;lection',
'choix_balises_htos' => 'Conversion &eacute;ventuelle',

'choix_balises_prem' => 'Balise &agrave; filtrer en premier',
'sous_choix_balises_prem' => '<br>Laisser vide si inutile',

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
'choix_balises_comment' => '&lt;!-- xxx --&gt;',
'sous_choix_balises_comment' => ' ',
'choix_balises_nbsp' => '&amp;NBSP;',
'sous_choix_balises_nbsp' => ' ',
'choix_balises_dern' => 'Balise &agrave; filtrer en dernier',
'sous_choix_balises_dern' => '<br />Laisser vide si inutile',


// Action

'titre_migre_action' => 'R&eacute;alisation de la migration',
'sur_titre_migre_static' => 'R&eacute;sultat de la migration demand&eacute;e',
'processing_page' => 'Traitement de la page : ',
'page_title' => 'Titre : ',
'err_page_vide' => 'Erreur : La page est vide',
'err_article_deja_publie' => 'Erreur : Cet article est en ligne avec le num&eacute;ro : ',
'err_insert_article' => 'Erreur : Insertion impossible dans la base de donn&eacute;es de :',

'insert_article_id' => 'Insertion de l\'article num&eacute;ro : ',
'update_article_id' => 'Mise &agrave; jour de l\'article num&eacute;ro : ',
'insert_article_titre' => ' avec le titre : ',
'migre_fini' => 'Fin des traitements de migration',

// Inutilisee
'err_liste_pages_vide' => '<strong>Erreur: La liste des pages est vide.</strong> Ce plugin r&eacute;cup&egrave;re une liste d\'adresses internet (URLs) contenues dans la page web http://www.mondomaine.com/la_liste_des_pages.html. Ce fichier doit contenir une suite de liens hypertexte vers les pages à importer dans spip, sous la forme &gt;a href="http://www.mondomaine.com/page_a_importer_1.html"&lt;Titre dans spip&gt;/a&lt;',

'migre_last' => ''

);

?>
