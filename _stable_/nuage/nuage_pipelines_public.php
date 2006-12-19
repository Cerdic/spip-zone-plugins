<?php

function nuage_insert_head($flux) {
	$css = "\n<style>
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
