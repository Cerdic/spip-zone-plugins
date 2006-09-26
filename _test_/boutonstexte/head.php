<?php
// insert le css et le js externes pour boutonstexte dans le <head> du document (#INSERT_HEAD)
function boutonstexte_insert_head($flux)
{
	$jsFile = find_in_path('boutonstexte.js');
	$cssFile = find_in_path('boutonstexte.css');
	$imgPath = dirname(find_in_path('img/fontsizeup.png'));

	$txtOnly = addslashes(unicode_to_javascript(html2unicode(_T(
		'boutonstexte:texte_seulement'))));
	$txtBackSpip = addslashes(unicode_to_javascript(html2unicode(_T(
		'boutonstexte:retour_a_spip'))));
	$txtSizeUp = addslashes(unicode_to_javascript(html2unicode(_T(
		'boutonstexte:augmenter_police'))));
	$txtSizeDown = addslashes(unicode_to_javascript(html2unicode(_T(
		'boutonstexte:diminuer_police'))));

	$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	boutonstexte = new boutonsTexte({
		'imgPath':'{$imgPath}',
		'txtOnly':'{$txtOnly}',
		'txtBackSpip':'{$txtBackSpip}',
		'txtSizeUp':'{$txtSizeUp}',
		'txtSizeDown':'{$txtSizeDown}'
	});
</script >
EOH;

	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
}
?>
