<?php
// insert le css et le js externes pour boutonstexte dans le <head> du document (#INSERT_HEAD)
function boutonstexte_insert_head($flux)
{
	$metacfg = array(
		'jsFile' => 'boutonstexte.js',
		'cssFile' => 'boutonstexte.css',
		'imgPath' => 'images/fontsizeup.png',
		'txtOnly' => 'boutonstexte:texte_seulement',
		'txtBackSpip' => 'boutonstexte:retour_a_spip',
		'txtSizeUp' => 'boutonstexte:augmenter_police',
		'txtSizeDown' => 'boutonstexte:diminuer_police'
	);
	meta_boutonstexte($metacfg);
	
	$jsFile = find_in_path($metacfg['jsFile']);
	$cssFile = find_in_path($metacfg['cssFile']);
	$imgPath = dirname(find_in_path($metacfg['imgPath']));

	$txtOnly = txt_boutonstexte($metacfg['txtOnly']);
	$txtBackSpip = txt_boutonstexte($metacfg['txtBackSpip']);
	$txtSizeUp = txt_boutonstexte($metacfg['txtSizeUp']);
	$txtSizeDown = txt_boutonstexte($metacfg['txtSizeDown']);

	$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	var boutonstexte = new boutonsTexte({
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
	
function txt_boutonstexte($txt)
{
	if (!$txt || $txt == '_') {
		return '';
	}
	return addslashes(unicode_to_javascript(html2unicode(_T($txt))));
}
	
function meta_boutonstexte(&$metacfg)
{
	include_spip('inc/meta');
	lire_metas();
    global $meta;
    if (empty($meta['boutonstexte'])) {
    	return 0;
    }
   	$return = 0;
    $metabtxt = unserialize($meta['boutonstexte']);
    foreach ($metabtxt as $o=>$v) {
    	if (isset($metacfg[$o])) {
    		$metacfg[$o] = $v;
		   	++$return;
    	}
    }
    return $return;
}
?>
