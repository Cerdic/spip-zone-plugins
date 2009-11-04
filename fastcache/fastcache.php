<?php

#
# un script pour servir quelques pages le plus vite possible
#
# implique que ces pages n'aient pas besoin d'executer du php a chaque hit
#
# (c) 2007-2009 fil@rezo.net
#

# debut du code
define('_DIR_RESTREINT_ABS', 'ecrire/');

if (empty($_POST)
AND !isset($_COOKIE[_FC_COOKIE_PREFIX.'_admin'])
AND !isset($_COOKIE[_FC_COOKIE_PREFIX.'_session'])
AND !isset($_SERVER['PHP_AUTH_USER'])
)
	define('_FC_KEY', 'fastcache:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

function fc_testie() {
	return
		(
		strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie')
		AND preg_match('/MSIE /i', $_SERVER['HTTP_USER_AGENT'])
		)
		OR isset($_GET['fctestie']);
}

if (defined('_FC_KEY'))
	require_once _FC_MEMOIZATION;

if (defined('_FC_KEY')
AND $p = cache_get(_FC_KEY)
AND $p['time'] == @filemtime(_FC_META)
) {
	// choix du body
	$b = (strlen($p['ie']) AND fc_testie()) ? 'ie' : 'body';

	// envoi des entetes
	eval($p['head']);

	// compression gzip
	if (_FC_GZIP
	AND strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		header('Content-Encoding: gzip');
		$p[$b] = gzencode($p[$b]);
	}

	// cache navigateur ?
	$etag = '"'.md5($p[$b]).'"';
	header('ETag: '.$etag);
	if (@$_SERVER['HTTP_IF_NONE_MATCH'] == $etag
	OR (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) AND strstr($p['head'], $_SERVER['HTTP_IF_MODIFIED_SINCE']))
	) {
		header('HTTP/1.0 304 Not Modified');
		exit;
	}

	// ultime entete : la longueur
	header('Content-length: '.strlen($p[$b]));
	header('Connection: close');
	echo $p[$b];

	// faire les stats ?
	if (_FC_STATS_SPIP) {
		include_once _DIR_PLUGIN_FASTCACHE.'public/stats.php';
		public_stats();
	}
}

else {
	include_once _DIR_RESTREINT_ABS.'inc_version.php';
	include _DIR_RESTREINT_ABS.'public.php';
}

?>
