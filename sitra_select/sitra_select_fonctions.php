<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function generer_sitra_select($str = '',$select){
	if (!$str)
		return;
	$str = str_replace(array(' ',';',':'),array('',',',','), $str);
	$datas = explode(',',$str);
	$txt = '';
	foreach($datas as $data){
		$selected = '';
		if ($select == $data)
			$selected = ' selected="selected"';
		$txt .= '<option'.$selected.' value="'.$data.'">'.$data.'</option>'."\n";
	}
	return $txt;
}
?>