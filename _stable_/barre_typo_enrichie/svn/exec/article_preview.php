<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/meta"); // Pour avoir le charset du site
include_spip("inc/texte"); // Pour pouvoir utiliser propre
include_spip("public/composer"); // Pour pouvoir generer les notes de bas de page (pourquoi c'est pas dans inc/texte ?)

function exec_article_preview_dist()
{
	lire_metas();
	header('Content-Type: text/html;charset='.lire_meta('charset'));
	echo "<meta http-equiv='Content-Type' content='text/html; charset=".lire_meta('charset')." />";
	echo propre($_POST['texte']);
	echo '<hr />';
	echo calculer_notes();
}
?>