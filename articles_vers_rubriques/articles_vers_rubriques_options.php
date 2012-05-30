<?php
//
// Auteur : Didier, www.ouhpla.net
// 
// Licence GPL 
//
//
// Transforme les brèves de tout le site en articles,
// les met dans une même rubrique
// et leur attribue optionnellement un même auteur


define('RUBRIQUE_DES_ARTICLES', 1);

define('AUTEUR_DES_ARTICLES', '');

define('ARTICLE_POUR_TEST', '');

define('IMG_SPIP_PATH', realpath(_DIR_IMG));

define('TABLE_ARTICLES_RUBRIQUES', 'spip_articles_rubriques');

define('ARTICLE_SECTION_VERS_RUBRIQUE_SURTITRE', false);

include_spip('base/articles_vers_rubriques_base');

?>
