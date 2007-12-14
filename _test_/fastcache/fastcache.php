<?php

#
# un script pour servir quelques pages le plus vite possible
#
# implique que ces pages n'aient pas besoin d'executer du php a chaque hit
#
# (c) 2007 fil@rezo.net
#

# debut du code
define('_DIR_RESTREINT_ABS', 'ecrire/');

if (empty($_POST)
AND !isset($_COOKIE[_FC_COOKIE_PREFIX.'_admin'])
AND !isset($_COOKIE[_FC_COOKIE_PREFIX.'_session'])
AND !isset($_SERVER['PHP_AUTH_USER'])
) {
	define('_FC_FILE',
		_FC_DIR_CACHE .'fc_'
		.md5($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
	);
}

function fc_testie() {
	return
		(
		strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie')
		AND preg_match('/MSIE /i', $_SERVER['HTTP_USER_AGENT'])
		)
		OR $_GET['fctestie'];
}

if (defined('_FC_FILE')
AND @file_exists(_FC_FILE.'_head.inc')
AND @filemtime(_FC_FILE.'_head.inc') > @filemtime(_FC_META)
AND (
	(time()-@filemtime(_FC_FILE.'_head.inc') < _FC_PERIODE)
	OR (@filemtime(_FC_FILE.'.lock')>time()-10)
	OR !(1+touch(_FC_FILE.'.lock'))
)) {
	include _FC_FILE.'_head.inc';
	$f = _FC_FILE;
	if (fc_testie() AND @file_exists($f.'_ie'))
		$f .= '_ie';
	if (strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		header('Content-Encoding: gzip');
		$f .= '.gz';
	}
	header('Content-length: '.@filesize($f));
	header('Connection: close');
	@readfile($f);
	flush();

	// faire les stats ?
	if (_FC_STATS_SPIP) {
		include_once _DIR_RESTREINT_ABS.'inc_version.php';
		Fastcache_lancer_stats();
	}
}

else {
	include_once _DIR_RESTREINT_ABS.'inc_version.php';
	include _DIR_RESTREINT_ABS.'public.php';
}

?>
