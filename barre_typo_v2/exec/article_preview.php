<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/meta"); // Pour avoir le charset du site
include_spip("inc/texte"); // Pour pouvoir utiliser propre
include_spip("public/parametrer"); // pour mes_fonctions

function exec_article_preview_dist()
{
	global $les_notes, $dir_lang;
	$ret = "";
	#lire_metas();
	#header('Content-Type: text/html;charset='.lire_meta('charset'));
	#echo "<meta http-equiv='Content-Type' content='text/html; charset=".lire_meta('charset')."' />";

	$ret .= propre($_POST['texte']);
	if ($les_notes) {
		$ret .= ("<hr clear='all' /><div $dir_lang class='arial11'>"
		. $les_notes
		. "</div>");
	}
	include_spip('inc/actions');
	ajax_retour($ret,false);
}
?>
