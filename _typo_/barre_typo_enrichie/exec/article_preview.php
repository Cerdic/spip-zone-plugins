<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/meta");
include_spip("inc/texte");

function exec_article_preview_dist()
{
	lire_metas();
	header('Content-Type: text/html;charset='.lire_meta('charset')); 
	echo propre($_POST['texte']);
}
?>