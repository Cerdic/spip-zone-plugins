<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// autoriser les tag <b>, <i>, <em>, <sc> et <br />
// dans le nom/signature d'un auteur.
// par d�faut, <multi> est toujours autoris�.
// Cf : http://www.spip.net/fr_article5666.html
// Cf : http://core.spip.org/projects/spip/repository/revisions/21016 � 21018
if (!defined('_TAGS_LOGIN'))  define('_TAGS_LOGIN', 'b, i, em, sc, br');

?>