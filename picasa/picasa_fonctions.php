<?php
// insert le css et le js externes pour l'échiquier dans le <head> du document (#INSERT_HEAD)
function picasa_insert_head($flux)
{
	$metacfg = array(
		'cssFile' => 'gallery',
		'imgPath' => 'img/open.gif',

	);
	
	$selector = $metacfg['selector'];
	$jsFile = generer_url_public('picasa.js');
	$cssFile = $metacfg['cssFile'];
	$imgPath = dirname(find_in_path($metacfg['imgPath']));

	$incHead = <<<EOH
<link rel="stylesheet" href="spip.php?page=picasa.css" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
EOH;

	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
}
	
?>
