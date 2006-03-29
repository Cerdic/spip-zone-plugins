<?php

  /** traitement d'un champs de formulaire au retour
   *  d'une edition eventuellement faite via tinyMCE
   */
function retourTinyMCE(&$str) {
	static $tag='<!-- TINY_MCE -->';
	$len=strlen($tag);
	if(substr($str, 0, $len)==$tag) {
		include_spip('sale');
		$str= sale(substr($str, $len));
	}
}

function exec_articles() {
	retourTinyMCE($GLOBALS['texte']);
	retourTinyMCE($GLOBALS['chapo']);

	include_once(_DIR_RESTREINT.'exec/articles.php');
	exec_articles_dist();
}

?>
