<?php

function nuage_insert_head($flux) {
	$css = "\n<style>
	ul.nuage_frequence, ul.nuage_frequence li {
		margin: 0;
		padding: 0;
		}
	ul.nuage_frequence { list-style: none; }
	ul.nuage_frequence li { display: inline; }
	span.nuage_frequence {
		display: block;
		float: left;
		height: 0;
		overflow: auto;
		width: 0;
	}
	</style>\n";
	return $css.$flux;
}

?>
