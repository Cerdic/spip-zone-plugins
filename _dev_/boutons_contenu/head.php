<?php
// insert le css et le js externes pour boutons_contenu dans le <head> du document (#INSERT_HEAD)
function BoutonsContenu_insert_head($flux)
{
	$jsFile = find_in_path('boutons_contenu.js');
	$cssFile = find_in_path('boutons_contenu.css');
	$imgPath = dirname($jsFile) . '/img';
//	$imgPath = find_in_path('fontBigger.png');
	$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	boutons_contenu = new boutonsContenu({'imgPath':'{$imgPath}'});
</script >
EOH;

	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
}
?>
