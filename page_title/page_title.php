<?php

function page_title_affichage_final(&$page) {
	if (
	!$GLOBALS['html']
	OR strpos($page, '<title')
	OR !strpos($page, '</head>')
	)
		return $page;

	// array_unique pour eviter NOM_SITE | NOM_SITE sur la home
	$title = join(' | ',
		array_unique(array_map('strip_tags', array_filter(array(
			preg_match(',<(h1).*</\1>,Ums', $page, $r) ? $r[0] : null,
			$GLOBALS['meta']['nom_site']
		)))));
	$page = str_replace('</head>', '<title>'.$title.'</title></head>', $page);

	return $page;

}

