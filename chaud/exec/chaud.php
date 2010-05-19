<?php

function exec_chaud_dist() {
	include_spip('inc/chaud');

	if ($a = chaud_articles(1.0)) {
		echo _L("Articles chauds:");
	
		echo "<table>";
		foreach ($a as $id_article => $score) {
			echo recuperer_fond('prive/chaud_article', array('id' => $id_article, 'score' => $score));
		}
		echo "</table>";

	} else
		echo _L("Pas d'article chaud");

}

