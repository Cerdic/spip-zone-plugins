<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/meta"); // Pour avoir le charset du site
include_spip("inc/texte"); // Pour pouvoir utiliser propre
include_spip("public/parametrer"); // pour mes_fonctions

function exec_article_preview_dist()
{
	lire_metas();
	#header('Content-Type: text/html;charset='.lire_meta('charset'));
	#echo "<meta http-equiv='Content-Type' content='text/html; charset=".lire_meta('charset')."' />";
	echo propre($_POST['texte']);
	global $les_notes, $dir_lang;
	if ($les_notes) {
		echo ("<hr clear='all' /><div $dir_lang class='arial11'>"
		. $les_notes
		. "</div>");
	}
}
?>