<?php

function nuage_insert_head($flux) {
	$css = "\n<style type=\"text/css\" id=\"styles_nuage\">
<!--
ul.nuage {
	margin:0;
	padding: 0;
	list-style: none;
}
ul.nuage li {
	display: inline;
	white-space: nowrap;
}
ul.nuage span.frequence {
	display: block;
	float: left;
	height: 0;
	overflow: auto;
	width: 0;
}
-->
</style>\n";
	return $css.$flux;
}

?>
