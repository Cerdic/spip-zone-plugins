<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Autoriser les tag <b>, <i>, <em>, <sc> et <br />
 * dans le nom/signature d'un auteur.
 *
 * Par defaut, <multi> est toujours autorise.
 * Cf : http://www.spip.net/fr_article5666.html
 * Cf : http://core.spip.org/projects/spip/repository/revisions/21016 a 21018
 */
if (!defined('_TAGS_NOM_AUTEUR')) {
	define('_TAGS_NOM_AUTEUR', 'b, i, em, sc, br');
}
