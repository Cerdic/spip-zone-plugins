<?php
// insert le css pour les styles supplementaires de LayoutGala dans le <head> du document (#INSERT_HEAD)
function LayoutGala_insert_head($flux) {
	$incHead = '<link rel="stylesheet" href="spip.php?page=layoutgala" type="text/css" media="all" />';
	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
}
?>