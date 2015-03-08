<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function urls_generer_url_article_dist($id_article, $args='', $ancre='') {
	return _DIR_RACINE . $id_article . ($args ? "?$args" : '') .($ancre ? "#$ancre" : '');
}
