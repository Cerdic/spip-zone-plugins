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

if (defined('_FC_KEY')) {
	$GLOBALS['meta']['memoization'] = _FC_CFG_MEMOIZATION;
	require_once _FC_MEMOIZATION;
}

if (defined('_FC_KEY')
AND $p = cache_get(_FC_KEY)
AND $p['time'] == @filemtime(_FC_META)
AND strlen($p['gz'])
) {
	// choix du body
	if (fc_testie()
	AND $ie = cache_get('ie'._FC_KEY)
	)
		$p['gz'] = $ie;

	// envoi des entetes
	eval($p['head']);

	// compression gzip
	if (strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		header('Content-Encoding: gzip');
		$body = &$p['gz'];
	} else
		$body = gzinflate(substr($p['gz'], 10));

	// cache navigateur ?
	$etag = '"'.md5($body).'"';
	header('ETag: '.$etag);
	if (@$_SERVER['HTTP_IF_NONE_MATCH'] == $etag
	OR (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) AND strstr($p['head'], $_SERVER['HTTP_IF_MODIFIED_SINCE']))
	) {
		header('HTTP/1.0 304 Not Modified');
		exit;
	}

	// ultime entete : la longueur
	header('Content-length: '.strlen($body));
	header('Connection: close');
	echo $body;

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
