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


define('RUBRIQUE_DES_BREVES', 52);

define('AUTEUR_DES_BREVES', 11099);

define('BREVE_POUR_TEST', '');

define('IMG_SPIP_PATH', realpath(_DIR_IMG));

define('TABLE_BREVES_ARTICLES', 'spip_breves_articles');

include_spip('base/breves2art_base');

?>
