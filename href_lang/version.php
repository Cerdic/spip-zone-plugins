<?php

/*
 * href_lang
 *
 * introduit un raccourci [texte|lang->http://lien.externe.net] pour les liens externes
 * qui ajoute l'attribut hreflang
 *
 * Auteur : James aka Klik�
 * � 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'href_lang';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['post_propre'] .= '|href_lang';

$GLOBALS['spip_matrice']['href_lang'] = dirname(__FILE__).'/href_lang.php';

?>
