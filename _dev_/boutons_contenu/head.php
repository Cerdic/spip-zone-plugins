<?php
// insert le css et le js externes pour boutons_contenu dans le <head> du document (#INSERT_HEAD)
function boutons_contenu_insert_head($flux)
{
	$jsFile = find_in_path('boutons_contenu.js');
	$cssFile = find_in_path('boutons_contenu.css');
	$imgPath = dirname(find_in_path('img/fontsizeup.png'));

	$txtSizeUp = _T('boutons_contenu:augmenter_police');
	$txtSizeDown = _T('boutons_contenu:diminuer_police');

	$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	boutons_contenu = new boutonsContenu({
		'imgPath':'{$imgPath}',
		'txtSizeUp':'{$txtSizeUp}',
		'txtSizeDown':'{$txtSizeDown}'
	});
</script >
EOH;

	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
}
?>
