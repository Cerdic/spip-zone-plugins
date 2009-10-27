<?php

#
# Definitions
#

# fonction de control : indique la valeur de ab (0 ou 1)
function ab_control() {
	static $ab;
	if (isset($ab)) return $ab;

	# n = nombre de scenarios
	$cfg = @unserialize($GLOBALS['meta']['ab']);
	if (!isset($cfg['n']))
		return;

	if (!is_null(_request('var_ab'))) {
		include_spip('inc/cookie');
		spip_setcookie('var_ab', $ab = _request('var_ab'));
	} else if (isset($_COOKIE['var_ab']))
		$ab = $_COOKIE['var_ab'];
	else
		$ab = ord(substr(md5($GLOBALS['ip']), -1)); # bien melanger les bits

	$ab = intval($ab)%intval($cfg['n']);

	$GLOBALS['marqueur'] .= 'ab:'.$ab;
	return $ab;
}

# silo de stockage
function ab_silo($page, $ab) {
	# n = nombre de scenarios
	$cfg = @unserialize($GLOBALS['meta']['ab']);
	if (!isset($cfg['n']))
		return "ab:error";
	return "ab$ab:".$cfg['n'].":".$_SERVER['HTTP_HOST']."$page";
}

# fonction de memorisation de la page vue
function ab_memo($page) {
	# on ne memorise jamais les bots ni les admins
	if (isset($_COOKIE['spip_admin'])) {
		#echo ab_control();
		return;
	}
	if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot'))
		return;

	if (function_exists('xcache_inc')) {
		xcache_inc(ab_silo($page, ab_control()));

		# log a la rache pour ne rien perdre si le serveur se relance
		if ($page !== '/') {
			$cfg = @unserialize($GLOBALS['meta']['ab']);
			foreach (array_filter(preg_split(',[\r\n]+,', $cfg['urls'])) as $p)
				spip_log("$p 0=".xcache_get(ab_silo($p, 0))." 1=".xcache_get(ab_silo($p,1)), 'ab');
		}
	}
}


#
# Action
#

# memoriser si l'URL est suivie
if ($cfg = @unserialize($GLOBALS['meta']['ab'])
AND in_array($_SERVER['REQUEST_URI'], preg_split(",[\r\n]+,", $cfg['urls'])))
	ab_memo($_SERVER['REQUEST_URI']);

# ajouter AB0 ou AB1 dans le chemin
_chemin('AB'.ab_control());

?>
