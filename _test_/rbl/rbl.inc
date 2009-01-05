<?php

// Verifier les POST contre une blacklist publique
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$lookup = implode('.', array_reverse(explode('.', $GLOBALS['ip']))) . '.'
		. ((isset($GLOBALS['meta']['rbl'])
		AND is_array($conf = @unserialize($GLOBALS['meta']['rbl']))
		AND strlen($conf['rbl']))
			? $conf['rbl']
			: 'sbl-xbl.spamhaus.org'
		);
	if ($lookup !== gethostbyname($lookup)) {
		spip_log('rbl blocked: '.$GLOBALS['ip'].' response: '.gethostbyname($lookup), 'rbl');
		header('HTTP/1.1 403 Forbidden');
		die ("<html> <head> <title>403 Forbidden</ title> </head>
		<body> <p>".$GLOBALS['ip']." is listed in RBL.</p> </body> </html>");
	}
}

?>
