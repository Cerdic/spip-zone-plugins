<?php
function wgalerie_point_point($match) {
	$str = '';
	for ($i = $match[1]; $i <= $match[2]; $i++){
		$str .= $i.',';
	}
	$str = substr($str, 0, -1);
	return $str;
}

function wgalerie_vers_tableau ($texte) {
	$texte = str_replace(' ', '', $texte);
	$texte = preg_replace_callback(
            '`(\d+)\.\.(\d+?)`U',
            'wgalerie_point_point',
            $texte);
	$tableau = array();
	$tableau = explode (',', $texte);
	return $tableau;
}
?>