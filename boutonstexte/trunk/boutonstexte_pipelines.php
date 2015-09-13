<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

// insert le css et le js externes pour boutonstexte dans le <head> du document (#INSERT_HEAD)
function boutonstexte_insert_head_css($flux)
{
    $metacfg = array(
        'cssFile' => 'css/boutonstexte',
    );
    meta_boutonstexte($metacfg);
    $cssFile = find_in_path($metacfg['cssFile'].'.css');

    $dir = (isset($GLOBALS['lang_dir']) and $GLOBALS['lang_dir'] == 'ltr') ? 'left' : 'right';
    $imgto = find_in_path('images/textonly.png');
    $imgtsd = find_in_path('images/fontsizedown.png');
    $imgtsu = find_in_path('images/fontsizeup.png');
    $flux .=
        '<link rel="stylesheet" href="'.$cssFile.'" type="text/css" media="all" />'
      ."<style type='text/css'>div.onlytext {text-align:$dir;}</style>"
        ;

    return $flux;
}

function boutonstexte_insert_head($flux)
{
    $metacfg = array(
        'selector' => '#content .texte',
        'jsFile' => 'javascript/boutonstexte.js',
        'skin' => 'zoom',
        'txtOnly' => 'boutonstexte:texte_seulement',
        'txtBackSpip' => 'boutonstexte:retour_a_spip',
        'txtSizeUp' => 'boutonstexte:augmenter_police',
        'txtSizeDown' => 'boutonstexte:diminuer_police',
    );
    meta_boutonstexte($metacfg);

    $selector = $metacfg['selector'];
    $jsFile = find_in_path($metacfg['jsFile']);
    $imgPath = dirname(find_in_path('boutonstexte/themes/'.$metacfg['skin'].'/fontsizeup.png')).'/';

    $txtOnly = txt_boutonstexte($metacfg['txtOnly']);
    $txtBackSpip = txt_boutonstexte($metacfg['txtBackSpip']);
    $txtSizeUp = txt_boutonstexte($metacfg['txtSizeUp']);
    $txtSizeDown = txt_boutonstexte($metacfg['txtSizeDown']);

    $flux .= <<<EOH
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript"><!--
	var boutonstexte_options = {
		'selector':'{$selector}',
		'imgPath':'{$imgPath}',
		'txtOnly':'{$txtOnly}',
		'txtBackSpip':'{$txtBackSpip}',
		'txtSizeUp':'{$txtSizeUp}',
		'txtSizeDown':'{$txtSizeDown}'
	};
//-->
</script>
EOH;

    return $flux;
}

function txt_boutonstexte($txt)
{
    if (!$txt || $txt == '_') {
        return '';
    }
    $t = texte_script(unicode_to_javascript(html2unicode(_T($txt))));
    $t = str_replace('\\\\', '\\', $t);

    return $t;
}

function meta_boutonstexte(&$metacfg)
{
    include_spip('inc/meta');
    global $meta;
    if (empty($meta['boutonstexte'])) {
        return 0;
    }
    $return = 0;
    $metabtxt = unserialize($meta['boutonstexte']);
    foreach ($metabtxt as $o => $v) {
        if (isset($metacfg[$o])) {
            $metacfg[$o] = $v;
            ++$return;
        }
    }

    return $return;
}
