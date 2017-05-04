<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function storify_is_story($texte) {
	if (strpos($texte, '<!--story-->') !== false
		and strncmp(ltrim($texte), '<!--story-->', 12) == 0) {
		return ' ';
	}
	return '';
}

function filtre_story_lines_from_texte_dist($texte) {
	include_spip('inc/storify');
	$v = storify_from_texte($texte);
	return $v['story_lines'];
}

function storify_safe_type($type) {
	static $types;
	if (!$types) {
		include_spip('inc/storify');
		$types = storify_types();
	}

	if(!$type or !isset($types[$type])) {
		$type = 'two_rows';
	}

	return $type;
}

function storify_type_options($type) {
	static $types;
	if (!$types) {
		include_spip('inc/storify');
		$types = storify_types();
	}
	$type = storify_safe_type($type);

	$options = array(''=>'');
	foreach (array_keys($types) as $t) {
		$options[$t] = _T('storify:label_type_'.$t);
	}

	$s = "";
	foreach ($options as $v=>$label) {
		$selected = ($v==$type?' selected="selected"':'');
		$s .= "<option value=\"$v\"$selected>$label</option>\n";
	}

	return $s;
}